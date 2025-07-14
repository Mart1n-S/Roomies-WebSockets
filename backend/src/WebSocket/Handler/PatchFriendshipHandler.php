<?php

namespace App\WebSocket\Handler;

use App\Entity\User;
use Ratchet\ConnectionInterface;
use App\WebSocket\Connection\ConnectionRegistry;
use App\State\WebSocket\Friendship\FriendshipPatchWebSocketProcessor;
use App\WebSocket\Contract\WebSocketHandlerInterface;

class PatchFriendshipHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly FriendshipPatchWebSocketProcessor $processor,
        private readonly ConnectionRegistry $registry,
    ) {}

    public function supports(string $type): bool
    {
        return $type === 'patch_friendship';
    }

    public function handle(ConnectionInterface $conn, array $message): void
    {
        try {
            if (!isset($conn->user) || !$conn->user instanceof User) {
                throw new \RuntimeException('Utilisateur non authentifié.');
            }

            $user = $conn->user;
            $payload = $message['payload'] ?? [];
            $friendshipId = $payload['friendshipId'] ?? null;
            $action = $payload['action'] ?? null;

            if (!$friendshipId || !in_array($action, ['accepter', 'refuser'], true)) {
                throw new \InvalidArgumentException('Paramètres invalides.');
            }

            $result = $this->processor->process($user, $friendshipId, $action);

            if ($action === 'accepter' && $result) {
                $response = [
                    'type' => 'friendship_updated',
                    'friendship' => $result['friendship'],
                    'room' => $result['room'],
                ];

                // 1. Envoi à l'utilisateur courant
                $conn->send(json_encode($response));

                // 2. Envoi à l’autre membre de la room
                foreach ($result['room']->members as $memberDto) {
                    $friendDto = $memberDto->member;

                    if ($friendDto->friendCode !== $user->getFriendCode()) {
                        $friendEntity = $this->registry->getUserByFriendCode($friendDto->friendCode);
                        if ($friendEntity) {
                            $targetConn = $this->registry->getConnection($friendEntity->getId());
                            if ($targetConn) {
                                $targetConn->send(json_encode($response));
                            }
                        }
                    }
                }
            }

            if ($action === 'refuser' && isset($result['friendship'])) {
                $applicant = $result['friendship']->getApplicant();
                $targetConn = $this->registry->getConnection($applicant->getId());

                if ($targetConn) {
                    $targetConn->send(json_encode([
                        'type' => 'friendship_deleted',
                        'friendshipId' => $friendshipId,
                    ]));
                }
            }
        } catch (\Throwable $e) {
            $conn->send(json_encode([
                'type' => 'friendship_error',
                'message' => $e->getMessage(),
            ]));
        }
    }
}
