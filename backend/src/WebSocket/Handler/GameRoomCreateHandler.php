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
use App\WebSocket\Contract\WebSocketHandlerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GameRoomCreateHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly GameRoomRepository $gameRoomRepository,
        private readonly GameRoomMapper $gameRoomMapper,
        private readonly ConnectionRegistry $registry,
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {}

    public function supports(string $type): bool
    {
        return $type === 'game_room_create';
    }

    public function handle(ConnectionInterface $conn, array $message): void
    {
        try {
            /** @var User $user */
            $user = $conn->user ?? null;
            $payload = $message['payload'] ?? [];

            if (!$user) {
                throw new \RuntimeException('Non authentifié.');
            }

            // Hydrate DTO
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
            // Validation
            $errors = $this->validator->validate($dto);
            if (count($errors) > 0) {
                $conn->send(json_encode([
                    'type' => 'game_room_error',
                    'message' => (string)$errors
                ]));
                return;
            }

            // Création GameRoom
            $gameRoom = new GameRoom();
            $gameRoom->setName($dto->name);
            $gameRoom->setGame($dto->game);
            $gameRoom->setCreator($user);

            $this->gameRoomRepository->save($gameRoom, true);

            // Map DTO
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
        } catch (\Throwable $e) {
            $conn->send(json_encode([
                'type' => 'game_room_error',
                'message' => $e->getMessage(),
            ]));
        }
    }
}
