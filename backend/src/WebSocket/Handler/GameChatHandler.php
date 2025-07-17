<?php

namespace App\WebSocket\Handler;

use App\Entity\User;
use Ratchet\ConnectionInterface;
use App\WebSocket\Contract\WebSocketHandlerInterface;
use App\Service\AvatarUrlGeneratorService;
use App\WebSocket\Connection\GameRoomPlayersRegistry;

class GameChatHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly GameRoomPlayersRegistry $registry,
        private readonly AvatarUrlGeneratorService $avatarUrlGenerator
    ) {}

    public function supports(string $type): bool
    {
        return $type === 'game_chat_message';
    }

    public function handle(ConnectionInterface $conn, array $message): void
    {
        /** @var User|null $user */
        $user = $conn->user ?? null;

        if (!$user || empty($message['roomId']) || empty($message['content'])) {
            return;
        }

        $roomId = (string) $message['roomId'];
        $userIdBin = $user->getId()->toBinary();

        // Sécurité : vérifier que l'utilisateur est bien dans la room
        $connections = $this->registry->getPlayerConnections((int) $roomId);
        if (!isset($connections[$userIdBin])) {
            return; // l'utilisateur n'est pas autorisé à envoyer dans cette room
        }

        $content = strip_tags($message['content']);

        $payload = [
            'type' => 'game_chat_message',
            'message' => [
                'id' => uniqid('game_', true),
                'content' => $content,
                'createdAt' => (new \DateTime())->format(\DateTime::ATOM),
                'sender' => [
                    'friendCode' => $user->getFriendCode(),
                    'pseudo' => $user->getPseudo(),
                    'avatar' => $this->avatarUrlGenerator->generate($user),
                ],
                'roomId' => $roomId,
                'type' => 'Text',
            ],
        ];

        $this->broadcastToRoom($roomId, $payload);
    }

    private function broadcastToRoom(string $roomId, array $payload): void
    {
        foreach ($this->registry->getConnectionsForRoom($roomId) as $conn) {
            $conn->send(json_encode($payload));
        }
    }
}
