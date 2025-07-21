<?php

namespace App\WebSocket\Handler;

use App\Entity\User;
use Ratchet\ConnectionInterface;
use App\Game\Puissance4\Puissance4GameState;
use App\WebSocket\Connection\Puissance4GameRegistry;
use App\WebSocket\Connection\GameRoomPlayersRegistry;
use App\WebSocket\Contract\WebSocketHandlerInterface;

/**
 * Handler WebSocket pour la gestion du redémarrage d'une partie de Puissance 4 (replay).
 *
 * Gère un système de vote : la partie n’est relancée que si tous les deux joueurs cliquent sur "Rejouer".
 * Diffuse le nouvel état de jeu à tous les joueurs/spectateurs dès redémarrage.
 */
class Puissance4RestartHandler implements WebSocketHandlerInterface
{
    // Stocke les votes de redémarrage par roomId (uuidStr des joueurs ayant voté)
    private array $restartVotes = []; // [roomId => [uuidStr, ...]]

    public function __construct(
        private readonly Puissance4GameRegistry $puissance4GameRegistry,
        private readonly GameRoomPlayersRegistry $gameRoomPlayersRegistry,
    ) {}

    /**
     * Prend en charge les messages de type 'puissance4_restart'
     */
    public function supports(string $type): bool
    {
        return $type === 'puissance4_restart';
    }

    /**
     * Gère la logique de vote et de redémarrage :
     * - Si tous les joueurs ont voté : nouvelle partie, broadcast état initial.
     * - Sinon : feedback au joueur qui a voté.
     */
    public function handle(ConnectionInterface $conn, array $message): void
    {
        try {
            $roomId = isset($message['roomId']) ? (int)$message['roomId'] : null;
            /** @var User|null $user */
            $user = $conn->user ?? null;

            // Vérification des paramètres
            if ($roomId === null || !$user) {
                throw new \RuntimeException("Paramètres invalides ou utilisateur non connecté.");
            }

            $uuid = $user->getId()->toRfc4122();

            // Enregistre le vote (si pas déjà voté)
            if (!isset($this->restartVotes[$roomId])) {
                $this->restartVotes[$roomId] = [];
            }
            if (!in_array($uuid, $this->restartVotes[$roomId], true)) {
                $this->restartVotes[$roomId][] = $uuid;
            }

            // Récupère la liste des joueurs de la room
            $players = $this->gameRoomPlayersRegistry->getPlayerIds($roomId);

            // Si tous les joueurs ont voté ET il y a exactement 2 joueurs (Puissance 4)
            if (count($this->restartVotes[$roomId]) >= count($players) && count($players) === 2) {
                // Création d’un nouvel état de jeu
                $this->puissance4GameRegistry->createGame($roomId, new Puissance4GameState($players[0], $players[1]));
                unset($this->restartVotes[$roomId]); // Reset les votes

                $game = $this->puissance4GameRegistry->getGame($roomId);

                $payload = [
                    'type' => 'puissance4_update',
                    'roomId' => $roomId,
                    'board' => $game->getBoard(),
                    'currentPlayerIndex' => $game->getCurrentPlayerIndex(),
                    'winnerIndex' => $game->getWinnerIndex(),
                    'status' => $game->getStatus(),
                    'draw' => $game->isDraw(),
                    'lastMove' => null,
                ];

                // Broadcast état initial à tous (joueurs + spectateurs)
                $playerConns = $this->gameRoomPlayersRegistry->getPlayerConnections($roomId);
                $viewerConns = $this->gameRoomPlayersRegistry->getViewersForRoom($roomId);
                $allConns = array_merge(array_values($playerConns), $viewerConns);

                foreach ($allConns as $c) {
                    $c->send(json_encode($payload));
                }
            } else {
                // Tous les joueurs n’ont pas encore voté, on notifie l’attente
                $conn->send(json_encode([
                    'type' => 'puissance4_restart_wait',
                    'roomId' => $roomId,
                ]));
            }
        } catch (\Throwable $e) {
            // Feedback d’erreur au joueur
            $conn->send(json_encode([
                'type' => 'puissance4_restart_error',
                'message' => $e->getMessage(),
            ]));
        }
    }
}
