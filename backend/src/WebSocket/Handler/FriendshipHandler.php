<?php

namespace App\WebSocket\Handler;

use App\Dto\Friendship\FriendshipCreateDTO;
use App\State\Friendship\FriendshipCreateProcessor;
use App\WebSocket\Contract\WebSocketHandlerInterface;
use Ratchet\ConnectionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class FriendshipHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly FriendshipCreateProcessor $friendshipProcessor,
        private readonly SerializerInterface $serializer
    ) {}

    public function supports(string $type): bool
    {
        return $type === 'friend_request';
    }

    public function handle(ConnectionInterface $conn, array $message): void
    {
        try {
            if (!isset($conn->user) || !$conn->user instanceof UserInterface) {
                throw new \RuntimeException('Utilisateur non authentifié.');
            }

            $payload = $message['payload'] ?? null;
            if (!$payload || !isset($payload['friendCode'])) {
                throw new \InvalidArgumentException('Le champ friendCode est requis.');
            }

            $dto = new FriendshipCreateDTO();
            $dto->friendCode = $payload['friendCode'];

            $dtoOut = $this->friendshipProcessor->process($dto, $conn->user);

            $json = $this->serializer->serialize($dtoOut, 'json', ['groups' => ['read:friendship']]);

            $conn->send(json_encode([
                'type' => 'friend_request_success',
                'data' => json_decode($json, true),
            ]));
        } catch (\Throwable $e) {
            $conn->send(json_encode([
                'type' => 'friend_request_error',
                'message' => $e->getMessage(),
            ]));
        }
    }
}
