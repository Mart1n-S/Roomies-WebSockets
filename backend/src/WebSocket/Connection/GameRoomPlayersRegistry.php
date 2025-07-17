<?php

namespace App\WebSocket\Connection;

use Ratchet\ConnectionInterface;
use Symfony\Component\Uid\Uuid;

class GameRoomPlayersRegistry
{
    /**
     * @var array<int, Uuid[]> Ordre d’arrivée des joueurs par room
     */
    private array $playerOrderByRoom = [];

    /**
     * @var array<int, array<string, ConnectionInterface>> [ roomId => [ userIdBin => ConnectionInterface ] ]
     */
    private array $playersByRoom = [];

    /**
     * @var array<int, ConnectionInterface[]> Liste des spectateurs par room
     */
    private array $viewerConnectionsByRoom = [];

    // --- JOUEURS ---

    public function addPlayer(int $roomId, Uuid $userId, ConnectionInterface $conn): void
    {
        $bin = $userId->toBinary();

        if (
            !isset($this->playerOrderByRoom[$roomId]) ||
            !array_filter($this->playerOrderByRoom[$roomId], fn(Uuid $u) => $u->equals($userId))
        ) {
            $this->playerOrderByRoom[$roomId][] = $userId;
        }

        $this->playersByRoom[$roomId][$bin] = $conn;
    }

    public function removePlayer(int $roomId, Uuid $userId): void
    {
        $bin = $userId->toBinary();
        unset($this->playersByRoom[$roomId][$bin]);

        if (empty($this->playersByRoom[$roomId])) {
            unset($this->playersByRoom[$roomId]);
        }

        // Retire de l’ordre aussi
        if (isset($this->playerOrderByRoom[$roomId])) {
            $this->playerOrderByRoom[$roomId] = array_values(array_filter(
                $this->playerOrderByRoom[$roomId],
                fn(Uuid $u) => !$u->equals($userId)
            ));

            if (empty($this->playerOrderByRoom[$roomId])) {
                unset($this->playerOrderByRoom[$roomId]);
            }
        }
    }

    /**
     * Liste ordonnée des userId (UUID) de la room.
     */
    public function getPlayerIds(int $roomId): array
    {
        return $this->playerOrderByRoom[$roomId] ?? [];
    }

    /**
     * Récupère les connections (userId bin => Connexion)
     */
    public function getPlayerConnections(int $roomId): array
    {
        return $this->playersByRoom[$roomId] ?? [];
    }

    /**
     * Récupère une connexion par son userId (UUID)
     */
    public function getConnectionsForRoom(string|int $roomId): array
    {
        return array_values($this->playersByRoom[(int) $roomId] ?? []);
    }

    public function getPlayerCount(int $roomId): int
    {
        return count($this->playerOrderByRoom[$roomId] ?? []);
    }

    // --- SPECTATEURS ---

    public function addViewer(int $roomId, ConnectionInterface $conn): void
    {
        $this->viewerConnectionsByRoom[$roomId][] = $conn;
    }

    public function removeViewer(int $roomId, ConnectionInterface $conn): void
    {
        if (!isset($this->viewerConnectionsByRoom[$roomId])) {
            return;
        }

        $this->viewerConnectionsByRoom[$roomId] = array_filter(
            $this->viewerConnectionsByRoom[$roomId],
            fn(ConnectionInterface $c) => $c !== $conn
        );

        if (empty($this->viewerConnectionsByRoom[$roomId])) {
            unset($this->viewerConnectionsByRoom[$roomId]);
        }
    }

    public function getViewersForRoom(int $roomId): array
    {
        return $this->viewerConnectionsByRoom[$roomId] ?? [];
    }

    public function getViewerCount(int $roomId): int
    {
        return count($this->viewerConnectionsByRoom[$roomId] ?? []);
    }

    /**
     * Vérifie si la connexion est un spectateur dans la room.
     */
    public function isViewer(int $roomId, ConnectionInterface $conn): bool
    {
        if (!isset($this->viewerConnectionsByRoom[$roomId])) {
            return false;
        }

        foreach ($this->viewerConnectionsByRoom[$roomId] as $viewerConn) {
            if ($viewerConn === $conn) {
                return true;
            }
        }

        return false;
    }

    // --- RESET ---

    public function reset(): void
    {
        $this->playersByRoom = [];
        $this->playerOrderByRoom = [];
        $this->viewerConnectionsByRoom = [];
    }
}
