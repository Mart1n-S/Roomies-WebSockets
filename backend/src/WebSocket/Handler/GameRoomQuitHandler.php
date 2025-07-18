<?php

namespace App\WebSocket\Handler;

use Ratchet\ConnectionInterface;
use Symfony\Component\Uid\Uuid;
use App\Repository\GameRoomRepository;
use App\WebSocket\Connection\ConnectionRegistry;
use App\WebSocket\Connection\GameRoomPlayersRegistry;
use App\WebSocket\Connection\MorpionGameRegistry;
use App\WebSocket\Contract\WebSocketHandlerInterface;
use App\Mapper\GameRoomMapper;
use Symfony\Component\Serializer\SerializerInterface;

class GameRoomQuitHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly GameRoomPlayersRegistry $gameRoomPlayersRegistry,
        private readonly MorpionGameRegistry $morpionGameRegistry,
        private readonly ConnectionRegistry $connectionRegistry,
        private readonly GameRoomRepository $gameRoomRepository,
        private readonly GameRoomMapper $gameRoomMapper,
        private readonly SerializerInterface $serializer,
    ) {}

    public function supports(string $type): bool
    {
        return $type === 'game_room_quit';
    }

    public function handle(ConnectionInterface $conn, array $message): void
    {
        $roomId = $message['roomId'] ?? null;
        $user = $conn->user ?? null;

        if (!$roomId || !$user) {
            $conn->send(json_encode([
                'type' => 'game_room_quit_error',
                'message' => 'Paramètres invalides',
            ]));
            return;
        }

        $roomId = (int) $roomId;
        $userId = $user->getId();

        // === VÉRIFIE SI C'ÉTAIT UN VIEWER ===
        $wasViewer = $this->gameRoomPlayersRegistry->isViewer($roomId, $conn);

        if ($wasViewer) {
            $this->gameRoomPlayersRegistry->removeViewer($roomId, $conn);
        } else {
            $this->gameRoomPlayersRegistry->removePlayer($roomId, $userId);

            // Si moins de 2 joueurs => reset la partie
            $players = $this->gameRoomPlayersRegistry->getPlayerIds($roomId);
            if (count($players) < 2) {
                $this->morpionGameRegistry->removeGame($roomId);
            }
        }

        // === NOTIFIE LES AUTRES JOUEURS/VIEWERS ===
        foreach ($this->gameRoomPlayersRegistry->getConnectionsForRoom($roomId) as $otherConn) {
            $otherConn->send(json_encode([
                'type' => 'game_room_player_left',
                'roomId' => $roomId,
                'friendCode' => $user->getFriendCode(),
                'wasViewer' => $wasViewer,
            ]));
        }

        // === BROADCAST ÉTAT MIS À JOUR ===
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
                    $targetConn->send(json_encode([
                        'type' => 'game_room_player_left',
                        'roomId' => $roomId,
                        'friendCode' => $user->getFriendCode(),
                        'wasViewer' => $wasViewer,
                    ]));

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
