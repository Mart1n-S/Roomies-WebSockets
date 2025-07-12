<?php

namespace App\State\WebSocket\Security;

use App\Entity\User;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Websocket\WebSocketTokenRead;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class WebSocketTokenProvider implements ProviderInterface
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
        private TokenStorageInterface $tokenStorage
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): WebSocketTokenRead|null
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user instanceof User) {
            return null;
        }

        // Génère un token standard avec payload de Lexik (username, id, iat, exp)
        $token = $this->jwtManager->createFromPayload($user, [
            'purpose' => 'websocket',
            'exp' => time() + 120,
        ]);

        return new WebSocketTokenRead($token, time() + 120);
    }
}
