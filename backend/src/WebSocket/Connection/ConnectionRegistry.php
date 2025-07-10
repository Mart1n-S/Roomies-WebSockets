<?php

namespace App\WebSocket\Connection;

use Ratchet\ConnectionInterface;
use Symfony\Component\Uid\Uuid;

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
}
