<?php

namespace App\WebSocket\Handler;

use App\Entity\User;
use Symfony\Component\Uid\Uuid;
use Ratchet\ConnectionInterface;
use App\WebSocket\Connection\ConnectionRegistry;
use App\WebSocket\Connection\MorpionGameRegistry;
use App\WebSocket\Connection\GameRoomPlayersRegistry;
use App\WebSocket\Contract\WebSocketHandlerInterface;

class MorpionPlayHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly MorpionGameRegistry $morpionGameRegistry,
        private readonly GameRoomPlayersRegistry $gameRoomPlayersRegistry,
        private readonly ConnectionRegistry $connectionRegistry,
    ) {}

    public function supports(string $type): bool
    {
        return $type === 'morpion_play';
    }

    public function handle(ConnectionInterface $conn, array $message): void
    {
        try {
            $roomId = isset($message['roomId']) ? (int)$message['roomId'] : null;
            $position = isset($message['position']) ? (int)$message['position'] : null;
            /** @var User|null $user */
            $user = $conn->user ?? null;

            if ($roomId === null || $position === null || !$user) {
                throw new \RuntimeException("Paramètres invalides ou utilisateur non connecté.");
            }

            // Vérifie que la partie existe pour cette room
            if (!$this->morpionGameRegistry->hasGame($roomId)) {
                throw new \RuntimeException("Il faut attendre un autre joueur pour commencer la partie.");
            }

            $game = $this->morpionGameRegistry->getGame($roomId);

            // Récupère l'ordre des joueurs (UUID) dans la room
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

            // C’est à lui de jouer ?
            if ($game->getCurrentPlayerIndex() !== $userIndex) {
                throw new \RuntimeException("Ce n'est pas votre tour.");
            }

            // Joue le coup (peut throw si invalide)
            $game->playMove($user->getId(), $position);

            // Prépare la réponse à tous les joueurs et spectateurs de la room
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

            // Récupère toutes les connexions : joueurs + spectateurs
            $playerConns = $this->gameRoomPlayersRegistry->getPlayerConnections($roomId);
            $viewerConns = $this->gameRoomPlayersRegistry->getViewersForRoom($roomId);
            $allConns = array_merge(array_values($playerConns), $viewerConns);

            foreach ($allConns as $c) {
                $c->send(json_encode($payload));
            }

            // Si la partie est finie (victoire ou nul), on la supprime
            if ($game->getWinnerIndex() !== null || $game->isDraw()) {
                $this->morpionGameRegistry->removeGame($roomId);
            }
        } catch (\Throwable $e) {
            $conn->send(json_encode([
                'type' => 'morpion_play_error',
                'message' => $e->getMessage(),
            ]));
        }
    }
}
