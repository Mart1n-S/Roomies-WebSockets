<?php

namespace App\WebSocket\Handler;

use App\Mapper\GameRoomMapper;
use Ratchet\ConnectionInterface;
use App\Repository\GameRoomRepository;
use App\WebSocket\Connection\GameRoomPlayersRegistry;
use App\WebSocket\Contract\WebSocketHandlerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class GameRoomListHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly GameRoomRepository $gameRoomRepository,
        private readonly GameRoomMapper $gameRoomMapper,
        private readonly GameRoomPlayersRegistry $gameRoomPlayersRegistry,
        private readonly SerializerInterface $serializer
    ) {}

    public function supports(string $type): bool
    {
        return $type === 'game_room_list';
    }

    public function handle(ConnectionInterface $conn, array $message): void
    {
        try {
            $rooms = $this->gameRoomRepository->findAll();

            $result = [];
            foreach ($rooms as $room) {
                $dto = $this->gameRoomMapper->toReadDto($room);

                // Serialize DTO
                $jsonRoom = $this->serializer->serialize($dto, 'json', ['groups' => ['read:game_room']]);
                $arrRoom = json_decode($jsonRoom, true);

                // Ajoute le playersCount (toujours un INT)
                $arrRoom['playersCount'] = $this->gameRoomPlayersRegistry->getPlayerCount($room->getId());

                $result[] = $arrRoom;
            }

            $conn->send(json_encode([
                'type' => 'game_room_list',
                'rooms' => $result,
            ]));
        } catch (\Throwable $e) {
            $conn->send(json_encode([
                'type' => 'game_room_error',
                'message' => $e->getMessage(),
            ]));
        }
    }
}
