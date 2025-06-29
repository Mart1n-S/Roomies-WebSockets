<?php

namespace App\WebSocket\Router;

use App\WebSocket\Contract\WebSocketHandlerInterface;
use Ratchet\ConnectionInterface;

class MessageRouter
{
    /** @var WebSocketHandlerInterface[] */
    private array $handlers;

    public function __construct(iterable $handlers)
    {
        $this->handlers = iterator_to_array($handlers);
        echo "🔎 Handlers enregistrés :\n";
        foreach ($this->handlers as $handler) {
            echo " - " . get_class($handler) . "\n";
        }
    }

    /**
     * Route un message WebSocket vers le handler approprié.
     */
    public function handle(ConnectionInterface $conn, string $raw): void
    {
        try {
            $message = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
            $type = $message['type'] ?? null;

            if (!$type) {
                throw new \InvalidArgumentException("Le champ 'type' est requis dans le message.");
            }

            foreach ($this->handlers as $handler) {
                if ($handler->supports($type)) {
                    $handler->handle($conn, $message);
                    return;
                }
            }

            $conn->send(json_encode([
                'type' => 'error',
                'message' => "Aucun handler pour le type '$type'."
            ]));
        } catch (\Throwable $e) {
            $conn->send(json_encode([
                'type' => 'error',
                'message' => $e->getMessage(),
            ]));
        }
    }
}
