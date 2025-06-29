<?php

namespace App\WebSocket\Contract;

use Ratchet\ConnectionInterface;

interface WebSocketHandlerInterface
{
    /**
     * Indique si le handler prend en charge le type de message donné.
     */
    public function supports(string $type): bool;

    /**
     * Traite le message WebSocket correspondant.
     *
     * @param ConnectionInterface $conn Connexion WebSocket du client
     * @param array $message Message WebSocket décodé (doit contenir au moins 'type' et 'payload')
     */
    public function handle(ConnectionInterface $conn, array $message): void;
}
