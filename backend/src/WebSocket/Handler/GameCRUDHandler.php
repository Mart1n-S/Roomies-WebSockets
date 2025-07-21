<?php

namespace App\WebSocket\Handler;

use App\Enum\Game;
use App\Entity\User;
use App\Entity\GameRoom;
use App\Mapper\GameRoomMapper;
use Symfony\Component\Uid\Uuid;
use Ratchet\ConnectionInterface;
use App\Repository\GameRoomRepository;
use App\Dto\GameRoom\CreateGameRoomDTO;
use App\WebSocket\Connection\ConnectionRegistry;
use App\WebSocket\Connection\GameRoomPlayersRegistry;
use App\WebSocket\Connection\MorpionGameRegistry;
use App\WebSocket\Connection\Puissance4GameRegistry;
use App\WebSocket\Contract\WebSocketHandlerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Handler WebSocket pour la création et la suppression de rooms de jeu.
 *
 * Gère la création d’une salle de jeu (ex: Morpion) et sa suppression, 
 * avec validation, persistance, notifications et sécurité sur les droits.
 */
class GameCRUDHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly GameRoomRepository $gameRoomRepository,
        private readonly GameRoomMapper $gameRoomMapper,
        private readonly ConnectionRegistry $registry,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly GameRoomPlayersRegistry $gameRoomPlayersRegistry,
        private readonly MorpionGameRegistry $morpionGameRegistry,
        private readonly Puissance4GameRegistry $puissance4GameRegistry // <-- Ajouté pour support multi-jeux
    ) {}

    /**
     * Indique si ce handler prend en charge le type de message reçu.
     */
    public function supports(string $type): bool
    {
        return in_array($type, ['game_room_create', 'game_room_delete'], true);
    }

    /**
     * Point d’entrée principal du handler : dispatch en fonction du type de message (create/delete).
     */
    public function handle(ConnectionInterface $conn, array $message): void
    {
        try {
            /** @var User|null $user */
            $user = $conn->user ?? null;
            $type = $message['type'] ?? null;
            $payload = $message['payload'] ?? [];

            if (!$user) {
                throw new \RuntimeException('Non authentifié.');
            }

            switch ($type) {
                case 'game_room_create':
                    $this->handleCreate($conn, $user, $payload);
                    break;
                case 'game_room_delete':
                    $this->handleDelete($conn, $user, $payload);
                    break;
                default:
                    $conn->send(json_encode([
                        'type' => 'game_room_error',
                        'message' => "Type non supporté: $type"
                    ]));
                    break;
            }
        } catch (\Throwable $e) {
            $conn->send(json_encode([
                'type' => 'game_room_error',
                'message' => $e->getMessage(),
            ]));
        }
    }

    /**
     * Création d’une salle de jeu (Morpion, Puissance4, etc.)
     */
    private function handleCreate(ConnectionInterface $conn, User $user, array $payload): void
    {
        $dto = new CreateGameRoomDTO();
        $dto->name = $payload['name'] ?? '';
        if (!empty($payload['game'])) {
            $dto->game = Game::tryFrom($payload['game']);
            if (!$dto->game) {
                $conn->send(json_encode([
                    'type' => 'game_room_error',
                    'message' => "Jeu inconnu : " . $payload['game']
                ]));
                return;
            }
        }
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $conn->send(json_encode([
                'type' => 'game_room_error',
                'message' => (string)$errors
            ]));
            return;
        }

        $gameRoom = new GameRoom();
        $gameRoom->setName($dto->name);
        $gameRoom->setGame($dto->game);
        $gameRoom->setCreator($user);

        $this->gameRoomRepository->save($gameRoom, true);

        $readDto = $this->gameRoomMapper->toReadDto($gameRoom);
        $json = $this->serializer->serialize($readDto, 'json', ['groups' => ['read:game_room']]);

        // Broadcast à tous les connectés
        foreach ($this->registry->getAllConnectedUserIds() as $userId) {
            if (!$userId instanceof Uuid) {
                $userId = Uuid::fromString($userId);
            }
            $targetConn = $this->registry->getConnection($userId);
            if ($targetConn) {
                $targetConn->send(json_encode([
                    'type' => 'game_room_created',
                    'room' => json_decode($json, true),
                ]));
            }
        }
    }

    /**
     * Suppression d’une salle de jeu (uniquement par le créateur, si la salle est vide).
     * Supprime aussi le state mémoire éventuel (pour Morpion, Puissance4...).
     */
    private function handleDelete(ConnectionInterface $conn, User $user, array $payload): void
    {
        $roomId = $payload['roomId'] ?? null;
        if (!$roomId) {
            $conn->send(json_encode([
                'type' => 'game_room_error',
                'message' => 'ID de room manquant.'
            ]));
            return;
        }

        /** @var GameRoom|null $room */
        $room = $this->gameRoomRepository->find($roomId);

        if (!$room) {
            $conn->send(json_encode([
                'type' => 'game_room_error',
                'message' => 'Partie introuvable.'
            ]));
            return;
        }

        if ($room->getCreator()?->getId() !== $user->getId()) {
            $conn->send(json_encode([
                'type' => 'game_room_error',
                'message' => 'Seul le créateur peut supprimer la partie.'
            ]));
            return;
        }

        // Vérifie qu’aucun joueur ou spectateur n’est encore présent dans la room
        $roomIdInt = (int)$roomId;
        $playerCount = $this->gameRoomPlayersRegistry->getPlayerCount($roomIdInt);
        $viewerCount = $this->gameRoomPlayersRegistry->getViewerCount($roomIdInt);

        if ($playerCount > 0 || $viewerCount > 0) {
            $conn->send(json_encode([
                'type' => 'game_room_error',
                'message' => "Impossible de supprimer : il y a encore $playerCount joueur(s) ou $viewerCount spectateur(s) dans la partie."
            ]));
            return;
        }

        $roomIdString = $room->getId();

        // Suppression effective de la room en base
        $this->gameRoomRepository->remove($room, true);

        // Nettoie la registry mémoire selon le type de jeu
        $gameType = $room->getGame()->value;
        if ($gameType === Game::Morpion->value) {
            $this->morpionGameRegistry->removeGame($roomIdInt);
        }
        if ($gameType === Game::Puissance4->value) {
            $this->puissance4GameRegistry->removeGame($roomIdInt);
        }

        // Broadcast à tous les connectés
        foreach ($this->registry->getAllConnectedUserIds() as $userId) {
            if (!$userId instanceof Uuid) {
                $userId = Uuid::fromString($userId);
            }
            $targetConn = $this->registry->getConnection($userId);
            if ($targetConn) {
                $targetConn->send(json_encode([
                    'type' => 'game_room_deleted',
                    'roomId' => $roomIdString,
                ]));
            }
        }
    }
}
