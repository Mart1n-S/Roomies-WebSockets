<?php

namespace App\WebSocket\Connection;

use App\Entity\User;
use Symfony\Component\Uid\Uuid;
use Ratchet\ConnectionInterface;

class ConnectionRegistry
{
    /** @var array<string, ConnectionInterface> */
    private array $connections = [];

    public function register(Uuid $userId, ConnectionInterface $conn): void
    {
        $this->connections[$userId->toBinary()] = $conn;
    }

    public function unregister(Uuid $userId): void
    {
        unset($this->connections[$userId->toBinary()]);
    }

    public function getConnection(Uuid $userId): ?ConnectionInterface
    {
        return $this->connections[$userId->toBinary()] ?? null;
    }

    public function getAllConnectedUserIds(): array
    {
        return array_keys($this->connections);
    }

    public function isConnected(Uuid $userId): bool
    {
        return isset($this->connections[$userId->toBinary()]);
    }

    public function getUserByFriendCode(string $friendCode): ?User
    {
        foreach ($this->connections as $conn) {
            if (isset($conn->user) && $conn->user instanceof User) {
                if ($conn->user->getFriendCode() === $friendCode) {
                    return $conn->user;
                }
            }
        }

        return null;
    }
}
