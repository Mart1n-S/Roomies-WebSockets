<?php

namespace App\WebSocket\Handler;

use App\Entity\User;
use Ratchet\ConnectionInterface;
use App\Game\Morpion\MorpionGameState;
use App\WebSocket\Connection\MorpionGameRegistry;
use App\WebSocket\Connection\GameRoomPlayersRegistry;
use App\WebSocket\Contract\WebSocketHandlerInterface;

class MorpionRestartHandler implements WebSocketHandlerInterface
{
    private array $restartVotes = []; // [roomId => [uuidStr, ...]]

    public function __construct(
        private readonly MorpionGameRegistry $morpionGameRegistry,
        private readonly GameRoomPlayersRegistry $gameRoomPlayersRegistry,
    ) {}

    public function supports(string $type): bool
    {
        return $type === 'morpion_restart';
    }

    public function handle(ConnectionInterface $conn, array $message): void
    {
        try {
            $roomId = isset($message['roomId']) ? (int)$message['roomId'] : null;
            /** @var User|null $user */
            $user = $conn->user ?? null;

            if ($roomId === null || !$user) {
                throw new \RuntimeException("Paramètres invalides ou utilisateur non connecté.");
            }

            $uuid = $user->getId()->toRfc4122();
            if (!isset($this->restartVotes[$roomId])) {
                $this->restartVotes[$roomId] = [];
            }

            if (!in_array($uuid, $this->restartVotes[$roomId], true)) {
                $this->restartVotes[$roomId][] = $uuid;
            }

            $players = $this->gameRoomPlayersRegistry->getPlayerIds($roomId);

            // Tous les joueurs ont voté
            if (count($this->restartVotes[$roomId]) >= count($players) && count($players) === 2) {
                $this->morpionGameRegistry->createGame($roomId, new MorpionGameState($players[0], $players[1]));
                unset($this->restartVotes[$roomId]);

                $game = $this->morpionGameRegistry->getGame($roomId);

                $payload = [
                    'type' => 'morpion_update',
                    'roomId' => $roomId,
                    'board' => $game->getBoard(),
                    'currentPlayerIndex' => $game->getCurrentPlayerIndex(),
                    'winnerIndex' => $game->getWinnerIndex(),
                    'status' => $game->getStatus(),
                    'draw' => $game->isDraw(),
                    'lastMove' => null,
                ];

                // Broadcast à tous (joueurs + spectateurs)
                $playerConns = $this->gameRoomPlayersRegistry->getPlayerConnections($roomId);
                $viewerConns = $this->gameRoomPlayersRegistry->getViewersForRoom($roomId);
                $allConns = array_merge(array_values($playerConns), $viewerConns);

                foreach ($allConns as $c) {
                    $c->send(json_encode($payload));
                }
            } else {
                // Préviens juste le joueur qu’on attend l’autre
                $conn->send(json_encode([
                    'type' => 'morpion_restart_wait',
                    'roomId' => $roomId,
                ]));
            }
        } catch (\Throwable $e) {
            $conn->send(json_encode([
                'type' => 'morpion_restart_error',
                'message' => $e->getMessage(),
            ]));
        }
    }
}
