<?php

namespace App\WebSocket\Handler;

use App\Entity\User;
use Ratchet\ConnectionInterface;
use App\Repository\UserRepository;
use App\WebSocket\Connection\ConnectionRegistry;
use App\WebSocket\Contract\WebSocketHandlerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SendMessageHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly ConnectionRegistry $registry
    ) {}

    public function supports(string $type): bool
    {
        return $type === 'send_message';
    }

    public function handle(ConnectionInterface $conn, array $message): void
    {
        try {
            if (!isset($conn->user) || !$conn->user instanceof UserInterface) {
                throw new \RuntimeException('Utilisateur non authentifié.');
            }

            $payload = $message['payload'] ?? null;
            if (!$payload || !isset($payload['friendCode'], $payload['content'])) {
                throw new \InvalidArgumentException('Le friendCode et le contenu du message sont requis.');
            }

            /** @var User $receiver */
            $receiver = $this->userRepository->findOneBy(['friendCode' => $payload['friendCode']]);

            if (!$receiver) {
                throw new \RuntimeException("Destinataire introuvable.");
            }

            $receiverConn = $this->registry->getConnection($receiver->getId());

            /** @var User $user */
            $user = $conn->user;

            if ($receiverConn) {
                $receiverConn->send(json_encode([
                    'type' => 'message_received',
                    'from' => $user->getFriendCode(),
                    'content' => $payload['content']
                ]));
            }

            $conn->send(json_encode([
                'type' => 'message_sent',
                'message' => 'Message envoyé avec succès.'
            ]));
        } catch (\Throwable $e) {
            $conn->send(json_encode([
                'type' => 'send_message_error',
                'message' => $e->getMessage()
            ]));
        }
    }
}
