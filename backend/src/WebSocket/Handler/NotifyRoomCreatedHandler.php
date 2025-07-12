<?php

namespace App\WebSocket\Handler;

use App\Repository\UserRepository;
use App\WebSocket\Contract\WebSocketHandlerInterface;
use App\WebSocket\Connection\ConnectionRegistry;
use Ratchet\ConnectionInterface;

class NotifyRoomCreatedHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly ConnectionRegistry $registry
    ) {}

    public function supports(string $type): bool
    {
        return $type === 'notify_room_created';
    }

    public function handle(ConnectionInterface $conn, array $message): void
    {
        $payload = $message['payload'] ?? null;

        if (!$payload || !isset($payload['room'], $payload['memberFriendCodes'])) {
            $conn->send(json_encode([
                'type' => 'error',
                'message' => 'Payload invalide pour notify_room_created'
            ]));
            return;
        }

        foreach ($payload['memberFriendCodes'] as $friendCode) {
            $user = $this->userRepository->findOneBy(['friendCode' => $friendCode]);

            if (!$user) {
                continue;
            }

            $receiverConn = $this->registry->getConnection($user->getId());

            if ($receiverConn) {
                $receiverConn->send(json_encode([
                    'type' => 'room_created',
                    'room' => $payload['room']
                ]));
            }
        }

        // Répond à l’émetteur pour confirmer l’envoi
        $conn->send(json_encode([
            'type' => 'room_notified',
            'message' => 'Notification envoyée aux membres connectés.'
        ]));
    }
}
