<?php

namespace App\Game\Puissance4;

use Symfony\Component\Uid\Uuid;

/**
 * Classe représentant l'état d'une partie de Puissance 4 (Connect Four).
 * Gère la logique métier : grille, joueurs, tour, victoire, nul, etc.
 */
class Puissance4GameState
{
    /** @var array Grille de jeu (6 lignes × 7 colonnes), cases = null | 'R' | 'Y' */
    private array $board = [];
    /** @var Uuid[] Liste ordonnée des joueurs (UUID), index 0 = joueur 1 (Rouge), 1 = joueur 2 (Jaune) */
    private array $players = [];
    /** @var int Index du joueur courant (0 ou 1) */
    private int $currentPlayerIndex = 0;
    /** @var string Statut de la partie : 'waiting' | 'playing' | 'win' | 'draw' */
    private string $status = 'waiting';
    /** @var int|null Index du gagnant (0 ou 1) si victoire, null sinon */
    private ?int $winnerIndex = null;

    /**
     * Initialise une partie de Puissance 4.
     */
    public function __construct(Uuid $player1, ?Uuid $player2 = null)
    {
        $this->board = array_fill(0, 6, array_fill(0, 7, null));
        $this->players = [$player1];
        if ($player2) {
            $this->players[] = $player2;
            $this->status = 'playing';
        }
    }

    /**
     * Réinitialise la partie (grille vide, joueur 1 commence, statut adapté).
     */
    public function reset(): void
    {
        $this->board = array_fill(0, 6, array_fill(0, 7, null));
        $this->currentPlayerIndex = 0;
        $this->status = count($this->players) === 2 ? 'playing' : 'waiting';
        $this->winnerIndex = null;
    }

    /**
     * Ajoute un second joueur à la partie et démarre la partie si possible.
     */
    public function addPlayer(Uuid $player): void
    {
        if (count($this->players) < 2 && !$this->hasPlayer($player)) {
            $this->players[] = $player;
            $this->status = 'playing';
        }
    }

    /**
     * Récupère l’index du joueur (0 ou 1), ou null si absent.
     */
    public function getPlayerIndex(Uuid $player): ?int
    {
        foreach ($this->players as $i => $p) {
            if ($p->equals($player)) return $i;
        }
        return null;
    }

    /**
     * Retourne le symbole associé au joueur ('R' ou 'Y'), ou null si non participant.
     */
    public function getPlayerSymbol(Uuid $player): ?string
    {
        $index = $this->getPlayerIndex($player);
        return $index === 0 ? 'R' : ($index === 1 ? 'Y' : null);
    }

    /**
     * Joue un coup pour le joueur dans la colonne donnée (pose en bas disponible).
     * Retourne true si le coup a été joué, false sinon.
     */
    public function playMove(Uuid $player, int $col): bool
    {
        if ($this->status !== 'playing') return false;
        if ($col < 0 || $col > 6) return false;
        $playerIndex = $this->getPlayerIndex($player);
        if ($playerIndex !== $this->currentPlayerIndex) return false;

        // Cherche la 1re case libre depuis le bas
        for ($row = 5; $row >= 0; --$row) {
            if ($this->board[$row][$col] === null) {
                $symbol = $playerIndex === 0 ? 'R' : 'Y'; // R = Rouge, Y = Jaune
                $this->board[$row][$col] = $symbol;

                if ($this->isWin($symbol, $row, $col)) {
                    $this->status = 'win';
                    $this->winnerIndex = $playerIndex;
                } elseif ($this->isDraw()) {
                    $this->status = 'draw';
                } else {
                    $this->currentPlayerIndex = 1 - $this->currentPlayerIndex;
                }
                return true;
            }
        }
        return false; // colonne pleine
    }

    /**
     * Vérifie si le symbole donné a gagné la partie en posant en ($row, $col).
     */
    public function isWin(string $symbol, int $row, int $col): bool
    {
        $directions = [
            [0, 1],   // horizontal
            [1, 0],   // vertical
            [1, 1],   // diagonale \
            [1, -1],  // diagonale /
        ];
        foreach ($directions as [$dr, $dc]) {
            $count = 1;
            // Vers l'avant
            for ($k = 1; $k < 4; ++$k) {
                $r = $row + $dr * $k;
                $c = $col + $dc * $k;
                if ($r < 0 || $r > 5 || $c < 0 || $c > 6 || $this->board[$r][$c] !== $symbol) break;
                $count++;
            }
            // Vers l'arrière
            for ($k = 1; $k < 4; ++$k) {
                $r = $row - $dr * $k;
                $c = $col - $dc * $k;
                if ($r < 0 || $r > 5 || $c < 0 || $c > 6 || $this->board[$r][$c] !== $symbol) break;
                $count++;
            }
            if ($count >= 4) return true;
        }
        return false;
    }

    /**
     * Vérifie si la partie est nulle (plateau plein sans victoire).
     */
    public function isDraw(): bool
    {
        if ($this->status !== 'playing') return false;
        foreach ($this->board[0] as $cell) if ($cell === null) return false;
        return true;
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

    /**
     * Retourne l’état de la partie sous forme de tableau sérialisable.
     */
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
