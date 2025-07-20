<?php

namespace App\WebSocket\Handler;

use App\Entity\User;
use App\Mapper\GameRoomMapper;
use Symfony\Component\Uid\Uuid;
use Ratchet\ConnectionInterface;
use App\Game\Morpion\MorpionGameState;
use App\Repository\GameRoomRepository;
use App\WebSocket\Connection\ConnectionRegistry;
use App\WebSocket\Connection\MorpionGameRegistry;
use App\WebSocket\Connection\GameRoomPlayersRegistry;
use App\WebSocket\Contract\WebSocketHandlerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Handler WebSocket pour la gestion de l’entrée dans une salle de jeu (en tant que joueur ou spectateur).
 *
 * Gère la logique d’entrée dans une GameRoom, la notification de tous les participants, 
 * le démarrage de la partie (pour Morpion), et l’envoi d’état global en temps réel.
 */
class GameRoomJoinHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly GameRoomRepository $gameRoomRepository,
        private readonly GameRoomPlayersRegistry $gameRoomPlayersRegistry,
        private readonly ConnectionRegistry $connectionRegistry,
        private readonly GameRoomMapper $gameRoomMapper,
        private readonly MorpionGameRegistry $morpionGameRegistry,
        private readonly SerializerInterface $serializer,
    ) {}

    /**
     * Prend en charge les messages 'game_room_join' (joueur) et 'game_room_watch' (spectateur).
     */
    public function supports(string $type): bool
    {
        return in_array($type, ['game_room_join', 'game_room_watch'], true);
    }

    /**
     * Handler principal : gestion de l’entrée en tant que joueur ou spectateur.
     */
    public function handle(ConnectionInterface $conn, array $message): void
    {
        try {
            /** @var User $user */
            $user = $conn->user ?? null;
            $roomId = isset($message['roomId']) ? (int) $message['roomId'] : null;
            $type = $message['type'] ?? null;

            if (!$user) throw new \RuntimeException('Utilisateur non authentifié.');
            if (!$roomId) throw new \InvalidArgumentException('roomId requis');

            $room = $this->gameRoomRepository->find($roomId);
            if (!$room) throw new \RuntimeException("Room non trouvée");

            $userId = $user->getId();
            $orderedPlayerIds = $this->gameRoomPlayersRegistry->getPlayerIds($roomId);

            if ($type === 'game_room_watch') {
                // --- Gestion entrée spectateur ---
                $this->gameRoomPlayersRegistry->addViewer($roomId, $conn);

                // Préparation des infos joueurs (affichage du front, symboles X/O pour Morpion)
                $symbolByIndex = ['X', 'O'];
                $playersArr = [];
                foreach ($orderedPlayerIds as $ix => $playerId) {
                    $player = $this->gameRoomRepository->getEntityManager()
                        ->getRepository(User::class)->find($playerId);
                    if ($player) {
                        $dto = $this->gameRoomMapper->mapUser($player);
                        $arr = json_decode($this->serializer->serialize($dto, 'json', ['groups' => ['read:user']]), true);
                        $arr['playerIndex'] = $ix;
                        $arr['symbol'] = $symbolByIndex[$ix] ?? null;
                        $playersArr[] = $arr;
                    }
                }

                // Récupération de l’état de la partie Morpion si existant
                $gameState = null;
                $game = $this->morpionGameRegistry->getGame($roomId);
                if ($game) {
                    $gameState = [
                        'board' => $game->getBoard(),
                        'currentPlayerIndex' => $game->getCurrentPlayerIndex(),
                        'winnerIndex' => $game->getWinnerIndex(),
                        'draw' => $game->isDraw(),
                        'status' => $game->getStatus(),
                    ];
                }

                // Réponse personnalisée au spectateur
                $conn->send(json_encode([
                    'type' => 'game_room_viewing',
                    'roomId' => $roomId,
                    'message' => 'Vous observez cette partie.',
                    'players' => $playersArr,
                    'morpion' => $gameState,
                ]));

                // Mise à jour globale
                $this->broadcastRoomUpdate($roomId, count($orderedPlayerIds));
                return;
            }

            // --- Gestion entrée joueur ---
            $alreadyPlayer = $this->hasUserInPlayers($userId, $orderedPlayerIds);
            if (count($orderedPlayerIds) >= 2 && !$alreadyPlayer) {
                $conn->send(json_encode([
                    'type' => 'game_room_join_error',
                    'message' => "La room est déjà complète."
                ]));
                return;
            }

            // Ajoute le joueur à la GameRoomPlayersRegistry
            $this->gameRoomPlayersRegistry->addPlayer($roomId, $userId, $conn);
            $orderedPlayerIds = $this->gameRoomPlayersRegistry->getPlayerIds($roomId);

            // Prépare l’affichage pour le front (index & symbole Morpion)
            $symbolByIndex = ['X', 'O'];
            $playerEntities = [];
            foreach ($orderedPlayerIds as $ix => $playerId) {
                if ($playerId->equals($userId)) {
                    $playerEntities[] = ['entity' => $user, 'index' => $ix, 'symbol' => $symbolByIndex[$ix] ?? null];
                } else {
                    $player = $this->gameRoomRepository->getEntityManager()
                        ->getRepository(User::class)->find($playerId);
                    if ($player) {
                        $playerEntities[] = ['entity' => $player, 'index' => $ix, 'symbol' => $symbolByIndex[$ix] ?? null];
                    }
                }
            }

            $playersArr = [];
            foreach ($playerEntities as $entry) {
                $dto = $this->gameRoomMapper->mapUser($entry['entity']);
                $arr = json_decode($this->serializer->serialize($dto, 'json', ['groups' => ['read:user']]), true);
                $arr['playerIndex'] = $entry['index'];
                $arr['symbol'] = $entry['symbol'];
                $playersArr[] = $arr;
            }

            // Réponse à l’utilisateur courant
            $conn->send(json_encode([
                'type' => 'game_room_joined',
                'roomId' => $roomId,
                'players' => $playersArr,
            ]));

            // Notification des autres joueurs
            $myBinId = $userId->toBinary();
            foreach ($this->gameRoomPlayersRegistry->getPlayerConnections($roomId) as $otherUserIdBin => $otherConn) {
                if ($otherUserIdBin !== $myBinId) {
                    $ix = array_search($userId->toRfc4122(), array_map(fn($u) => $u->toRfc4122(), $orderedPlayerIds));
                    $dto = $this->gameRoomMapper->mapUser($user);
                    $arr = json_decode($this->serializer->serialize($dto, 'json', ['groups' => ['read:user']]), true);
                    $arr['playerIndex'] = $ix;
                    $arr['symbol'] = $symbolByIndex[$ix] ?? null;

                    $otherConn->send(json_encode([
                        'type' => 'game_room_player_joined',
                        'roomId' => $roomId,
                        'player' => $arr,
                    ]));
                }
            }

            // Notification des spectateurs
            foreach ($this->gameRoomPlayersRegistry->getViewersForRoom($roomId) as $viewerConn) {
                $ix = array_search($userId->toRfc4122(), array_map(fn($u) => $u->toRfc4122(), $orderedPlayerIds));
                $dto = $this->gameRoomMapper->mapUser($user);
                $arr = json_decode($this->serializer->serialize($dto, 'json', ['groups' => ['read:user']]), true);
                $arr['playerIndex'] = $ix;
                $arr['symbol'] = $symbolByIndex[$ix] ?? null;

                $viewerConn->send(json_encode([
                    'type' => 'game_room_player_joined',
                    'roomId' => $roomId,
                    'player' => $arr,
                    'wasViewer' => true, // aide le front à différencier le contexte
                ]));
            }

            // Broadcast état global à tous les connectés
            $this->broadcastRoomUpdate($roomId, count($orderedPlayerIds));

            // Si deux joueurs présents, démarre la partie (création état Morpion)
            if (count($orderedPlayerIds) === 2 && !$this->morpionGameRegistry->hasGame($roomId)) {
                $this->morpionGameRegistry->createGame($roomId, new MorpionGameState(
                    $orderedPlayerIds[0],
                    $orderedPlayerIds[1]
                ));
            }
        } catch (\Throwable $e) {
            $conn->send(json_encode([
                'type' => 'game_room_join_error',
                'message' => $e->getMessage(),
            ]));
        }
    }

    /**
     * Diffuse un état global (joueurs, spectateurs, room) à tous les clients connectés.
     */
    private function broadcastRoomUpdate(int $roomId, int $playersCount): void
    {
        $roomDto = $this->gameRoomMapper->toReadDto(
            $this->gameRoomRepository->find($roomId)
        );

        $jsonRoom = $this->serializer->serialize($roomDto, 'json', ['groups' => ['read:game_room']]);
        $viewerCount = $this->gameRoomPlayersRegistry->getViewerCount($roomId);

        foreach ($this->connectionRegistry->getAllConnectedUserIds() as $userId) {
            $targetConn = $this->connectionRegistry->getConnection(Uuid::fromString((string) $userId));
            if ($targetConn) {
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

    /**
     * Vérifie si l’utilisateur est déjà joueur dans la room.
     */
    private function hasUserInPlayers(Uuid $userId, array $players): bool
    {
        foreach ($players as $p) {
            if ($p instanceof Uuid && $p->equals($userId)) return true;
        }
        return false;
    }
}
