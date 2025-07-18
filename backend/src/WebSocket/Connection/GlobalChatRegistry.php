<?php

namespace App\WebSocket\Connection;

use Ratchet\ConnectionInterface;
use App\Entity\User;

/**
 * Singleton (service partagé) pour la gestion des connectés au chat global
 */
class GlobalChatRegistry
{
    /** @var array<int, ConnectionInterface> */
    private array $connections = [];

    public function add(ConnectionInterface $conn): void
    {
        $this->connections[spl_object_id($conn)] = $conn;
    }

    public function remove(ConnectionInterface $conn): void
    {
        // 1. Récupère le friendCode avant de supprimer
        $friendCode = isset($conn->user) ? $conn->user->getFriendCode() : null;

        // 2. Retire la connexion
        unset($this->connections[spl_object_id($conn)]);

        // 3. Notifie tous les autres connectés que cet utilisateur est parti
        if ($friendCode) {
            foreach ($this->connections as $otherConn) {
                $otherConn->send(json_encode([
                    'type' => 'global_user_left',
                    'friendCode' => $friendCode,
                ]));
            }
        }
    }

    /**
     * @return ConnectionInterface[]
     */
    public function all(): array
    {
        return $this->connections;
    }

    public function getActiveUsers(): array
    {
        $users = [];
        foreach ($this->connections as $conn) {
            if (isset($conn->user) && $conn->user instanceof User) {
                $users[] = $conn->user;
            }
        }
        return $users;
    }
}
