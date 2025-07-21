<?php

namespace App\WebSocket\Connection;

use App\Game\Puissance4\Puissance4GameState;

/**
 * Registre centralisant les parties en cours de Puissance 4 pour chaque room.
 *
 * Permet de créer, retrouver, supprimer ou réinitialiser les états de jeu pour les rooms Puissance 4.
 */
class Puissance4GameRegistry
{
    /**
     * @var array<int, Puissance4GameState> [roomId => état de jeu]
     */
    private array $games = [];

    /**
     * Enregistre une nouvelle partie de Puissance 4 pour une room.
     *
     * @param int $roomId
     * @param Puissance4GameState $state
     */
    public function createGame(int $roomId, Puissance4GameState $state): void
    {
        $this->games[$roomId] = $state;
    }

    /**
     * Récupère l’état de jeu associé à une room donnée.
     *
     * @param int $roomId
     * @return Puissance4GameState|null
     */
    public function getGame(int $roomId): ?Puissance4GameState
    {
        return $this->games[$roomId] ?? null;
    }

    /**
     * Vérifie si une partie existe pour cette room.
     *
     * @param int $roomId
     * @return bool
     */
    public function hasGame(int $roomId): bool
    {
        return isset($this->games[$roomId]);
    }

    /**
     * Supprime la partie associée à une room (fin de partie, suppression, etc.).
     *
     * @param int $roomId
     */
    public function removeGame(int $roomId): void
    {
        unset($this->games[$roomId]);
    }

    /**
     * Réinitialise complètement le registre (supprime toutes les parties).
     */
    public function reset(): void
    {
        $this->games = [];
    }
}
