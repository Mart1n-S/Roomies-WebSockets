<?php

namespace App\WebSocket\Handler;

use App\Entity\User;
use Ratchet\ConnectionInterface;
use App\Service\AvatarUrlGeneratorService;
use App\WebSocket\Connection\GlobalChatRegistry;
use App\WebSocket\Contract\WebSocketHandlerInterface;

/**
 * Gère les événements du chat global (connexion, déconnexion, envoi de message).
 */
class GlobalChatHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly GlobalChatRegistry $registry,
        private readonly AvatarUrlGeneratorService $avatarUrlGenerator
    ) {}

    /**
     * Vérifie si le handler supporte le type de message reçu.
     *
     * @param string $type
     * @return bool
     */
    public function supports(string $type): bool
    {
        return in_array($type, ['global_message', 'global_chat_join', 'global_chat_leave'], true);
    }

    /**
     * Gère un message WebSocket entrant selon son type (join, leave, message).
     *
     * @param ConnectionInterface $conn
     * @param array $message
     * @return void
     */
    public function handle(ConnectionInterface $conn, array $message): void
    {
        /** @var User|null $user */
        $user = $conn->user ?? null;

        switch ($message['type']) {
            case 'global_chat_join':
                $this->registry->add($conn);
                $this->broadcastActiveUsers();
                $this->broadcastUserJoined($user, $conn);
                break;

            case 'global_chat_leave':
                $this->registry->remove($conn);
                $this->broadcastUserLeft($user);
                $this->broadcastActiveUsers();
                break;

            case 'global_message':
                if ($user && !empty($message['content'])) {
                    $payload = [
                        'type' => 'global_message',
                        'message' => [
                            'id' => uniqid('global_', true),
                            'content' => $message['content'],
                            'createdAt' => (new \DateTime())->format(\DateTime::ATOM),
                            'sender' => [
                                'friendCode' => $user->getFriendCode(),
                                'pseudo' => $user->getPseudo(),
                                'avatar' => $this->avatarUrlGenerator->generate($user),
                            ],
                            'roomId' => 'global',
                            'type' => 'Text',
                        ]
                    ];
                    $this->broadcast($payload);
                }
                break;
        }
    }

    /**
     * Diffuse à tous les clients connectés la liste des utilisateurs actifs.
     *
     * @return void
     */
    private function broadcastActiveUsers()
    {
        $users = [];
        foreach ($this->registry->all() as $conn) {
            /** @var User $user */
            $user = $conn->user;
            $users[] = [
                'friendCode' => $user->getFriendCode(),
                'pseudo' => $user->getPseudo(),
                'avatar' => $this->avatarUrlGenerator->generate($user),
            ];
        }
        $payload = [
            'type' => 'global_active_users',
            'users' => $users
        ];
        $this->broadcast($payload);
    }

    /**
     * Diffuse un événement indiquant qu’un utilisateur a rejoint le chat.
     *
     * @param User|null $user
     * @param ConnectionInterface|null $excludeConn Connexion à exclure de l’envoi (souvent le nouvel entrant)
     * @return void
     */
    private function broadcastUserJoined($user, $excludeConn = null)
    {
        if (!$user) return;
        $payload = [
            'type' => 'global_user_joined',
            'user' => [
                'friendCode' => $user->getFriendCode(),
                'pseudo' => $user->getPseudo(),
                'avatar' => $this->avatarUrlGenerator->generate($user),
            ]
        ];
        $this->broadcast($payload, $excludeConn);
    }

    /**
     * Diffuse un événement indiquant qu’un utilisateur a quitté le chat.
     *
     * @param User|null $user
     * @return void
     */
    private function broadcastUserLeft($user)
    {
        if (!$user) return;
        $payload = [
            'type' => 'global_user_left',
            'friendCode' => $user->getFriendCode()
        ];
        $this->broadcast($payload);
    }

    /**
     * Diffuse un message à tous les utilisateurs connectés,
     * sauf éventuellement une connexion spécifique à exclure.
     *
     * @param array $payload
     * @param ConnectionInterface|null $excludeConn
     * @return void
     */
    private function broadcast(array $payload, $excludeConn = null)
    {
        foreach ($this->registry->all() as $conn) {
            if ($conn !== $excludeConn) {
                $conn->send(json_encode($payload));
            }
        }
    }
}
