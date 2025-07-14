<?php

namespace App\WebSocket\Handler;

use App\Repository\UserRepository;
use App\WebSocket\Connection\ConnectionRegistry;
use App\WebSocket\Contract\WebSocketHandlerInterface;
use Ratchet\ConnectionInterface;

class RequestStatusHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly ConnectionRegistry $registry,
        private readonly UserRepository $userRepository,
    ) {}

    public function supports(string $type): bool
    {
        return $type === 'request_status';
    }

    public function handle(ConnectionInterface $conn, array $message): void
    {
        try {
            $payload = $message['payload'] ?? [];

            if (!isset($payload['friendCodes']) || !is_array($payload['friendCodes'])) {
                throw new \InvalidArgumentException('Liste des friendCodes manquante ou invalide.');
            }

            foreach ($payload['friendCodes'] as $friendCode) {
                if (!is_string($friendCode)) {
                    continue;
                }

                $user = $this->userRepository->findOneBy(['friendCode' => $friendCode]);

                if (!$user) {
                    continue;
                }

                $isOnline = $this->registry->isConnected($user->getId());

                $conn->send(json_encode([
                    'type' => 'user-status',
                    'friendCode' => $friendCode,
                    'online' => $isOnline
                ]));
            }
        } catch (\Throwable $e) {
            $conn->send(json_encode([
                'type' => 'error',
                'message' => 'Erreur dans request_status : ' . $e->getMessage(),
            ]));
        }
    }
}
