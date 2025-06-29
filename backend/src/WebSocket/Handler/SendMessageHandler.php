<?php

namespace App\WebSocket\Handler;

use App\Entity\User;
use Ratchet\ConnectionInterface;
use App\Repository\UserRepository;
use App\WebSocket\WebSocketServer;
use App\WebSocket\Contract\WebSocketHandlerInterface;
// TODO: PHASE de TEST
class SendMessageHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly WebSocketServer $webSocketServer
    ) {}

    public function supports(string $type): bool
    {
        return $type === 'send_message';
    }

    public function handle(ConnectionInterface $conn, array $message): void
    {
        $payload = $message['payload'] ?? [];

        $friendCode = $payload['friendCode'] ?? null;
        $content = $payload['content'] ?? null;

        if (!$friendCode || !$content) {
            $conn->send(json_encode([
                'type' => 'send_message_error',
                'message' => 'Code ami et message requis.'
            ]));
            return;
        }

        if (!isset($conn->user) || !$conn->user instanceof User) {
            $conn->send(json_encode([
                'type' => 'send_message_error',
                'message' => 'Utilisateur non authentifié.'
            ]));
            return;
        }

        $recipient = $this->userRepository->findOneBy(['friendCode' => $friendCode]);

        if (!$recipient) {
            $conn->send(json_encode([
                'type' => 'send_message_error',
                'message' => 'Aucun utilisateur avec ce code ami.'
            ]));
            return;
        }

        $recipientConn = $this->webSocketServer->getConnectionForUserId($recipient->getId());

        if ($recipientConn) {
            $recipientConn->send(json_encode([
                'type' => 'incoming_message',
                'from' => [
                    'id' => $conn->user->getId(),
                    'pseudo' => $conn->user->getPseudo(),
                ],
                'content' => $content,
            ]));
        }

        $conn->send(json_encode([
            'type' => 'send_message_success',
            'message' => 'Message envoyé à ' . $recipient->getPseudo()
        ]));
    }
}
