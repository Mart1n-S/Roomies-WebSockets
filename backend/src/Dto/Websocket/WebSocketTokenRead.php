<?php

namespace App\Dto\Websocket;

final class WebSocketTokenRead
{
    public string $token;
    public int $expiresAt;

    public function __construct(string $token, int $expiresAt)
    {
        $this->token = $token;
        $this->expiresAt = $expiresAt;
    }
}
