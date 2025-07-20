<?php

namespace App\WebSocket\Handler;

use App\Entity\User;
use Symfony\Component\Uid\Uuid;
use Ratchet\ConnectionInterface;
use App\WebSocket\Connection\ConnectionRegistry;
use App\WebSocket\Connection\MorpionGameRegistry;
use App\WebSocket\Connection\GameRoomPlayersRegistry;
use App\WebSocket\Contract\WebSocketHandlerInterface;

/**
 * Handler WebSocket pour la gestion des coups joués au Morpion en temps réel.
 *
 * - Vérifie l'authentification et l'autorisation du joueur.
 * - Valide le tour de jeu et la position.
 * - Met à jour l'état de la partie et le diffuse à tous les clients connectés (joueurs & spectateurs).
 * - Nettoie la partie en mémoire si elle est terminée (victoire ou nul).
 */
class MorpionPlayHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly MorpionGameRegistry $morpionGameRegistry,
        private readonly GameRoomPlayersRegistry $gameRoomPlayersRegistry,
        private readonly ConnectionRegistry $connectionRegistry,
    ) {}

    /**
     * Indique si ce handler gère le type 'morpion_play'.
     */
    public function supports(string $type): bool
    {
        return $type === 'morpion_play';
    }

    /**
     * Gère la réception d'un coup de Morpion via WebSocket.
     * 
     * - Valide les paramètres et le joueur.
     * - Vérifie l’état de la partie et le tour.
     * - Joue le coup, diffuse le nouvel état à tous.
     * - Supprime la partie de la registry si elle est terminée.
     */
    public function handle(ConnectionInterface $conn, array $message): void
    {
        try {
            $roomId = isset($message['roomId']) ? (int)$message['roomId'] : null;
            $position = isset($message['position']) ? (int)$message['position'] : null;
            /** @var User|null $user */
            $user = $conn->user ?? null;

            // Validation des paramètres et de l’utilisateur
            if ($roomId === null || $position === null || !$user) {
                throw new \RuntimeException("Paramètres invalides ou utilisateur non connecté.");
            }

            // Vérifie que la partie existe
            if (!$this->morpionGameRegistry->hasGame($roomId)) {
                throw new \RuntimeException("Il faut attendre un autre joueur pour commencer la partie.");
            }

            $game = $this->morpionGameRegistry->getGame($roomId);

            // Vérifie que l'utilisateur est joueur dans cette partie
            $players = $this->gameRoomPlayersRegistry->getPlayerIds($roomId); // array<Uuid>
            $userIndex = null;
            foreach ($players as $ix => $uuid) {
                if ($uuid instanceof Uuid && $uuid->equals($user->getId())) {
                    $userIndex = $ix; // 0 = joueur 1 (X), 1 = joueur 2 (O)
                    break;
                }
            }

            if ($userIndex === null) {
                throw new \RuntimeException("Vous n'êtes pas joueur dans cette partie.");
            }

            // Vérifie que c’est bien à ce joueur de jouer
            if ($game->getCurrentPlayerIndex() !== $userIndex) {
                throw new \RuntimeException("Ce n'est pas votre tour.");
            }

            // Joue le coup (déclenche l’update du board, la vérification de win/draw…)
            $game->playMove($user->getId(), $position);

            // Prépare la réponse à tous (état de la partie après le coup)
            $payload = [
                'type' => 'morpion_update',
                'roomId' => $roomId,
                'board' => $game->getBoard(),
                'currentPlayerIndex' => $game->getCurrentPlayerIndex(),
                'winnerIndex' => $game->getWinnerIndex(),
                'status' => $game->getStatus(),
                'draw' => $game->isDraw(),
                'lastMove' => $position,
            ];

            // Diffuse à tous les joueurs ET spectateurs de la room
            $playerConns = $this->gameRoomPlayersRegistry->getPlayerConnections($roomId);
            $viewerConns = $this->gameRoomPlayersRegistry->getViewersForRoom($roomId);
            $allConns = array_merge(array_values($playerConns), $viewerConns);

            foreach ($allConns as $c) {
                $c->send(json_encode($payload));
            }

            // Si la partie est terminée (victoire ou nul), supprime l’état en mémoire (cleanup)
            if ($game->getWinnerIndex() !== null || $game->isDraw()) {
                $this->morpionGameRegistry->removeGame($roomId);
            }
        } catch (\Throwable $e) {
            // Feedback d’erreur personnalisé pour le joueur qui a tenté le coup
            $conn->send(json_encode([
                'type' => 'morpion_play_error',
                'message' => $e->getMessage(),
            ]));
        }
    }
}
