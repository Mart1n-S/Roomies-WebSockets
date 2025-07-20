<?php

namespace App\Game\Morpion;

use Symfony\Component\Uid\Uuid;

/**
 * Classe représentant l'état d'une partie de Morpion (Tic Tac Toe).
 *
 * Gère la logique métier : état de la grille, joueurs, tour de jeu, statut de la partie, etc.
 * Fournit toutes les méthodes nécessaires à la gestion d'une partie côté backend.
 */
class MorpionGameState
{
    /** @var array Grille de jeu (9 cases) contenant 'X', 'O' ou null */
    private array $board = []; // 9 cases: null | 'X' | 'O'
    /** @var Uuid[] Liste ordonnée des joueurs (UUID), index 0 = joueur 1, index 1 = joueur 2 */
    private array $players = [];
    /** @var int Index du joueur courant (0 ou 1) */
    private int $currentPlayerIndex = 0;
    /** @var string Statut de la partie : 'waiting' | 'playing' | 'win' | 'draw' */
    private string $status = 'waiting';
    /** @var int|null Index du gagnant (0 ou 1) si victoire, null sinon */
    private ?int $winnerIndex = null;

    /**
     * Initialise une partie de Morpion.
     *
     * @param Uuid $player1 Joueur 1 (créateur de la partie)
     * @param Uuid|null $player2 Joueur 2 (optionnel, sinon partie en attente)
     */
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
     * Réinitialise la partie (grille vide, joueur 1 commence, statut adapté).
     */
    public function reset(): void
    {
        $this->board = array_fill(0, 9, null);
        $this->currentPlayerIndex = 0;
        $this->status = count($this->players) === 2 ? 'playing' : 'waiting';
        $this->winnerIndex = null;
    }

    /**
     * Ajoute un second joueur à la partie et démarre la partie si possible.
     *
     * @param Uuid $player
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
     *
     * @param Uuid $player
     * @return int|null
     */
    public function getPlayerIndex(Uuid $player): ?int
    {
        foreach ($this->players as $i => $p) {
            if ($p->equals($player)) return $i;
        }
        return null;
    }

    /**
     * Retourne le symbole associé au joueur ('X' ou 'O'), ou null si non participant.
     *
     * @param Uuid $player
     * @return string|null
     */
    public function getPlayerSymbol(Uuid $player): ?string
    {
        $index = $this->getPlayerIndex($player);
        return $index === 0 ? 'X' : ($index === 1 ? 'O' : null);
    }

    /**
     * Joue un coup pour le joueur à la position donnée.
     *
     * Vérifie toutes les conditions : statut, position, tour, case vide, etc.
     * Gère la victoire, le match nul, ou passe au joueur suivant.
     *
     * @param Uuid $player
     * @param int $position
     * @return bool True si le coup est valide et joué, false sinon.
     */
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

    /**
     * Vérifie si le symbole donné a gagné la partie.
     *
     * @param string $symbol 'X' ou 'O'
     * @return bool True si victoire, false sinon
     */
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

    /**
     * Vérifie si la partie est nulle (toutes les cases sont remplies sans victoire).
     *
     * @return bool
     */
    public function isDraw(): bool
    {
        return $this->status === 'playing' && count(array_filter($this->board, fn($v) => $v === null)) === 0;
    }

    /**
     * Retourne le statut actuel de la partie.
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Retourne l'état actuel du plateau de jeu.
     *
     * @return array
     */
    public function getBoard(): array
    {
        return $this->board;
    }

    /**
     * Retourne l’index du joueur dont c’est le tour.
     *
     * @return int
     */
    public function getCurrentPlayerIndex(): int
    {
        return $this->currentPlayerIndex;
    }

    /**
     * Retourne l’index du gagnant, ou null si pas de gagnant.
     *
     * @return int|null
     */
    public function getWinnerIndex(): ?int
    {
        return $this->winnerIndex;
    }

    /**
     * Retourne la liste des joueurs (UUID).
     *
     * @return Uuid[]
     */
    public function getPlayers(): array
    {
        return $this->players;
    }

    /**
     * Vérifie si un joueur (UUID) participe à la partie.
     *
     * @param Uuid $player
     * @return bool
     */
    public function hasPlayer(Uuid $player): bool
    {
        foreach ($this->players as $p) {
            if ($p->equals($player)) return true;
        }
        return false;
    }

    /**
     * Retourne l’état de la partie sous forme de tableau sérialisable.
     *
     * @return array
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
