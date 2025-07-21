<?php

namespace App\WebSocket\Handler;

use App\Entity\User;
use Ratchet\ConnectionInterface;
use Symfony\Component\Uid\Uuid;
use App\WebSocket\Connection\Puissance4GameRegistry;
use App\WebSocket\Connection\GameRoomPlayersRegistry;
use App\WebSocket\Connection\ConnectionRegistry;
use App\WebSocket\Contract\WebSocketHandlerInterface;

/**
 * Handler WebSocket pour jouer un coup à Puissance 4 en temps réel.
 */
class Puissance4PlayHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly Puissance4GameRegistry $puissance4GameRegistry,
        private readonly GameRoomPlayersRegistry $gameRoomPlayersRegistry,
        private readonly ConnectionRegistry $connectionRegistry,
    ) {}

    public function supports(string $type): bool
    {
        return $type === 'puissance4_play';
    }

    public function handle(ConnectionInterface $conn, array $message): void
    {
        try {
            $roomId = isset($message['roomId']) ? (int)$message['roomId'] : null;
            $col = isset($message['col']) ? (int)$message['col'] : null;
            /** @var User|null $user */
            $user = $conn->user ?? null;

            if ($roomId === null) {
                throw new \RuntimeException("Le paramètre 'roomId' est requis.");
            }
            if (!$user || !$user->getId() instanceof Uuid) {
                throw new \RuntimeException("Utilisateur non connecté ou ID invalide.");
            }
            if ($col === null) {
                throw new \RuntimeException("Le paramètre 'col' est requis.");
            }

            if ($roomId === null || $col === null || !$user) {
                throw new \RuntimeException("Paramètres invalides ou utilisateur non connecté.");
            }

            // Vérifie que la partie existe
            if (!$this->puissance4GameRegistry->hasGame($roomId)) {
                throw new \RuntimeException("Il faut attendre un autre joueur pour commencer la partie.");
            }

            $game = $this->puissance4GameRegistry->getGame($roomId);

            // Vérifie que l'utilisateur est joueur dans cette partie
            $players = $this->gameRoomPlayersRegistry->getPlayerIds($roomId);
            $userIndex = null;
            foreach ($players as $ix => $uuid) {
                if ($uuid instanceof Uuid && $uuid->equals($user->getId())) {
                    $userIndex = $ix;
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

            // Joue le coup (pose dans la colonne)
            $ok = $game->playMove($user->getId(), $col);
            if (!$ok) {
                throw new \RuntimeException("Coup invalide ou colonne pleine.");
            }

            // Récupère la ligne où le pion vient d'être posé pour le front (optionnel)
            $lastRow = null;
            for ($r = 0; $r < 6; $r++) {
                if ($game->getBoard()[$r][$col] !== null) {
                    $lastRow = $r;
                    break;
                }
            }

            // Prépare la réponse à tous
            $payload = [
                'type' => 'puissance4_update',
                'roomId' => $roomId,
                'board' => $game->getBoard(),
                'currentPlayerIndex' => $game->getCurrentPlayerIndex(),
                'winnerIndex' => $game->getWinnerIndex(),
                'status' => $game->getStatus(),
                'draw' => $game->isDraw(),
                'lastMove' => ['row' => $lastRow, 'col' => $col],
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
                $this->puissance4GameRegistry->removeGame($roomId);
            }
        } catch (\Throwable $e) {
            $conn->send(json_encode([
                'type' => 'puissance4_play_error',
                'message' => $e->getMessage(),
            ]));
        }
    }
}
