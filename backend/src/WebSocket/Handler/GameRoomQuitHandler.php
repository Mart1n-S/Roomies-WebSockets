<?php

namespace App\WebSocket\Handler;

use Ratchet\ConnectionInterface;
use Symfony\Component\Uid\Uuid;
use App\Entity\GameRoom;
use App\Enum\Game;
use App\Repository\GameRoomRepository;
use App\WebSocket\Connection\ConnectionRegistry;
use App\WebSocket\Connection\GameRoomPlayersRegistry;
use App\WebSocket\Connection\MorpionGameRegistry;
use App\WebSocket\Connection\Puissance4GameRegistry;
use App\WebSocket\Contract\WebSocketHandlerInterface;
use App\Mapper\GameRoomMapper;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Handler WebSocket pour la gestion de la sortie d’un utilisateur d’une salle de jeu (quit room).
 *
 * Peut gérer aussi bien la sortie d’un joueur que d’un spectateur.
 * Notifie tous les autres clients connectés à la room, remet l’état de la partie à zéro si besoin,
 * et diffuse l’état global mis à jour de la room à tous les clients (joueurs et viewers).
 */
class GameRoomQuitHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly GameRoomPlayersRegistry $gameRoomPlayersRegistry,
        private readonly MorpionGameRegistry $morpionGameRegistry,
        private readonly Puissance4GameRegistry $puissance4GameRegistry, // Ajouté
        private readonly ConnectionRegistry $connectionRegistry,
        private readonly GameRoomRepository $gameRoomRepository,
        private readonly GameRoomMapper $gameRoomMapper,
        private readonly SerializerInterface $serializer,
    ) {}

    /**
     * Indique si ce handler gère le type de message 'game_room_quit'.
     */
    public function supports(string $type): bool
    {
        return $type === 'game_room_quit';
    }

    /**
     * Gère la sortie d’un utilisateur (joueur ou viewer) d’une salle de jeu.
     * 
     * @param ConnectionInterface $conn   Connexion sortante
     * @param array $message              Doit contenir 'roomId'
     */
    public function handle(ConnectionInterface $conn, array $message): void
    {
        $roomId = $message['roomId'] ?? null;
        $user = $conn->user ?? null;

        // Vérification des paramètres
        if (!$roomId || !$user) {
            $conn->send(json_encode([
                'type' => 'game_room_quit_error',
                'message' => 'Paramètres invalides',
            ]));
            return;
        }

        $roomId = (int) $roomId;
        $userId = $user->getId();

        // === 1. Vérifie si l'utilisateur était un viewer (spectateur)
        $wasViewer = $this->gameRoomPlayersRegistry->isViewer($roomId, $conn);

        if ($wasViewer) {
            // Supprime le viewer de la registry
            $this->gameRoomPlayersRegistry->removeViewer($roomId, $conn);
        } else {
            // Sinon, c'était un joueur : suppression
            $this->gameRoomPlayersRegistry->removePlayer($roomId, $userId);

            // Si la partie est incomplète (moins de 2 joueurs), reset la partie du bon jeu
            $players = $this->gameRoomPlayersRegistry->getPlayerIds($roomId);

            // On doit vérifier le type de jeu !
            $room = $this->gameRoomRepository->find($roomId);
            if ($room instanceof GameRoom) {
                $gameType = $room->getGame()->value;
                if (count($players) < 2) {
                    if ($gameType === Game::Morpion->value) {
                        $this->morpionGameRegistry->removeGame($roomId);
                    } elseif ($gameType === Game::Puissance4->value) {
                        $this->puissance4GameRegistry->removeGame($roomId);
                    }
                }
            }
        }

        // === 2. Notifie tous les autres joueurs et spectateurs de la room du départ de ce membre
        foreach ($this->gameRoomPlayersRegistry->getConnectionsForRoom($roomId) as $otherConn) {
            $otherConn->send(json_encode([
                'type' => 'game_room_player_left',
                'roomId' => $roomId,
                'friendCode' => $user->getFriendCode(),
                'wasViewer' => $wasViewer,
            ]));
        }

        // === 3. Broadcast de l’état mis à jour (joueurs + spectateurs + état de la room)
        $room = $this->gameRoomRepository->find($roomId);
        if ($room) {
            $roomDto = $this->gameRoomMapper->toReadDto($room);
            $playersCount = $this->gameRoomPlayersRegistry->getPlayerCount($roomId);
            $viewerCount = $this->gameRoomPlayersRegistry->getViewerCount($roomId);

            $jsonRoom = $this->serializer->serialize($roomDto, 'json', ['groups' => ['read:game_room']]);

            foreach ($this->connectionRegistry->getAllConnectedUserIds() as $userId) {
                if (!$userId instanceof Uuid) {
                    $userId = Uuid::fromString($userId);
                }
                $targetConn = $this->connectionRegistry->getConnection($userId);
                if ($targetConn) {
                    // Notifie le départ pour affichage sur le front
                    $targetConn->send(json_encode([
                        'type' => 'game_room_player_left',
                        'roomId' => $roomId,
                        'friendCode' => $user->getFriendCode(),
                        'wasViewer' => $wasViewer,
                    ]));

                    // Notifie l’état global (compteurs, données room) à tous les clients
                    $targetConn->send(json_encode([
                        'type' => 'game_room_players_update',
                        'roomId' => $roomId,
                        'playersCount' => $playersCount,
                        'viewerCount' => $viewerCount,
                        'room' => json_decode($jsonRoom, true),
                    ]));
                }
            }
        }
    }
}
