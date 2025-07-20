<?php

namespace App\WebSocket\Connection;

use App\Game\Morpion\MorpionGameState;

/**
 * Registre centralisant les parties en cours de Morpion pour chaque room.
 *
 * Permet de créer, retrouver, supprimer ou réinitialiser les états de jeu pour les rooms Morpion.
 */
class MorpionGameRegistry
{
    /**
     * @var array<int, MorpionGameState> [roomId => état de jeu]
     * Contient l’état de chaque partie de Morpion en cours, indexée par l’ID de la room.
     */
    private array $games = [];

    /**
     * Enregistre une nouvelle partie de Morpion pour une room.
     *
     * @param int $roomId
     * @param MorpionGameState $state
     */
    public function createGame(int $roomId, MorpionGameState $state): void
    {
        $this->games[$roomId] = $state;
    }

    /**
     * Récupère l’état de jeu associé à une room donnée.
     *
     * @param int $roomId
     * @return MorpionGameState|null
     */
    public function getGame(int $roomId): ?MorpionGameState
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
