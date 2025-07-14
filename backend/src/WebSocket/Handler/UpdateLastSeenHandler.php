<?php

namespace App\WebSocket\Handler;

use App\Entity\User;
use Ratchet\ConnectionInterface;
use App\Repository\RoomUserRepository;
use App\Repository\MessageRepository;
use App\WebSocket\Contract\WebSocketHandlerInterface;

class UpdateLastSeenHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly RoomUserRepository $roomUserRepository,
        private readonly MessageRepository $messageRepository
    ) {}

    public function supports(string $type): bool
    {
        return $type === 'update_last_seen';
    }

    public function handle(ConnectionInterface $conn, array $message): void
    {
        try {
            /** @var User $user */
            $user = $conn->user ?? null;
            $roomId = $message['roomId'] ?? null;

            if (!$user || !$roomId) {
                throw new \RuntimeException('Utilisateur ou roomId manquant.');
            }

            $roomUser = $this->roomUserRepository->findOneByUserAndRoomId($user, $roomId);

            if (!$roomUser) {
                throw new \RuntimeException("Tu ne fais pas partie de cette room.");
            }

            // Update lastSeenAt
            $roomUser->setLastSeenAt(new \DateTimeImmutable());
            $this->roomUserRepository->save($roomUser, true);

            // Recalcul du nombre de messages non lus
            $unreadCount = $this->messageRepository->countUnreadForRoomUser($roomUser);

            // Renvoi au client
            $conn->send(json_encode([
                'type' => 'room_unread_updated',
                'roomId' => $roomId,
                'unreadCount' => (int) $unreadCount,
            ]));
        } catch (\Throwable $e) {
            $conn->send(json_encode([
                'type' => 'error',
                'message' => 'Erreur mise à jour lastSeen: ' . $e->getMessage(),
            ]));
        }
    }
}
