<?php

namespace App\WebSocket\Connection;

use App\Game\Morpion\MorpionGameState;

class MorpionGameRegistry
{
    /**
     * @var array<int, MorpionGameState> [roomId => état de jeu]
     */
    private array $games = [];

    public function createGame(int $roomId, MorpionGameState $state): void
    {
        $this->games[$roomId] = $state;
    }

    public function getGame(int $roomId): ?MorpionGameState
    {
        return $this->games[$roomId] ?? null;
    }

    public function hasGame(int $roomId): bool
    {
        return isset($this->games[$roomId]);
    }

    public function removeGame(int $roomId): void
    {
        unset($this->games[$roomId]);
    }

    public function reset(): void
    {
        $this->games = [];
    }
}
