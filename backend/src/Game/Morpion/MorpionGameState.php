<?php

namespace App\Game\Morpion;

use Symfony\Component\Uid\Uuid;

class MorpionGameState
{
    private array $board = []; // 9 cases: null | 'X' | 'O'
    /** @var Uuid[] */
    private array $players = []; // Ordonné : [joueur1, joueur2]
    private int $currentPlayerIndex = 0; // 0 ou 1
    private string $status = 'waiting'; // waiting | playing | win | draw
    private ?int $winnerIndex = null; // 0 ou 1 si win, sinon null

    public function __construct(Uuid $player1, ?Uuid $player2 = null)
    {
        $this->board = array_fill(0, 9, null);
        $this->players = [$player1];
        if ($player2) {
            $this->players[] = $player2;
            $this->status = 'playing';
        }
    }

    /**
     * Réinitialise la partie (grille vide, joueur 1 commence, statut playing)
     */
    public function reset(): void
    {
        $this->board = array_fill(0, 9, null);
        $this->currentPlayerIndex = 0;
        $this->status = count($this->players) === 2 ? 'playing' : 'waiting';
        $this->winnerIndex = null;
    }

    /**
     * Ajoute le second joueur et commence la partie
     */
    public function addPlayer(Uuid $player): void
    {
        if (count($this->players) < 2 && !$this->hasPlayer($player)) {
            $this->players[] = $player;
            $this->status = 'playing';
        }
    }

    /**
     * Retourne l’index du joueur (0 ou 1) ou null
     */
    public function getPlayerIndex(Uuid $player): ?int
    {
        foreach ($this->players as $i => $p) {
            if ($p->equals($player)) return $i;
        }
        return null;
    }

    public function getPlayerSymbol(Uuid $player): ?string
    {
        $index = $this->getPlayerIndex($player);
        return $index === 0 ? 'X' : ($index === 1 ? 'O' : null);
    }

    public function playMove(Uuid $player, int $position): bool
    {
        if ($this->status !== 'playing') return false;
        if ($position < 0 || $position > 8) return false;
        $playerIndex = $this->getPlayerIndex($player);
        if ($playerIndex !== $this->currentPlayerIndex) return false;
        if ($this->board[$position] !== null) return false;

        $symbol = $playerIndex === 0 ? 'X' : 'O';
        $this->board[$position] = $symbol;

        if ($this->isWin($symbol)) {
            $this->status = 'win';
            $this->winnerIndex = $playerIndex;
        } elseif ($this->isDraw()) {
            $this->status = 'draw';
        } else {
            $this->currentPlayerIndex = 1 - $this->currentPlayerIndex;
        }

        return true;
    }

    public function isWin(string $symbol): bool
    {
        $winCombos = [
            [0, 1, 2],
            [3, 4, 5],
            [6, 7, 8],
            [0, 3, 6],
            [1, 4, 7],
            [2, 5, 8],
            [0, 4, 8],
            [2, 4, 6]
        ];
        foreach ($winCombos as $combo) {
            if (
                $this->board[$combo[0]] === $symbol &&
                $this->board[$combo[1]] === $symbol &&
                $this->board[$combo[2]] === $symbol
            ) {
                return true;
            }
        }
        return false;
    }

    public function isDraw(): bool
    {
        return $this->status === 'playing' && count(array_filter($this->board, fn($v) => $v === null)) === 0;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getBoard(): array
    {
        return $this->board;
    }

    public function getCurrentPlayerIndex(): int
    {
        return $this->currentPlayerIndex;
    }

    public function getWinnerIndex(): ?int
    {
        return $this->winnerIndex;
    }

    public function getPlayers(): array
    {
        return $this->players;
    }

    public function hasPlayer(Uuid $player): bool
    {
        foreach ($this->players as $p) {
            if ($p->equals($player)) return true;
        }
        return false;
    }

    public function toArray(): array
    {
        return [
            'board' => $this->board,
            'status' => $this->status,
            'currentPlayerIndex' => $this->currentPlayerIndex,
            'winnerIndex' => $this->winnerIndex,
            'players' => array_map(fn($p) => $p->toRfc4122(), $this->players)
        ];
    }
}
