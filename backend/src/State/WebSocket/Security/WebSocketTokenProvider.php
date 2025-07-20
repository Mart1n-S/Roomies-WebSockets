<?php

namespace App\State\WebSocket\Security;

use App\Entity\User;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Dto\Websocket\WebSocketTokenRead;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Provider pour générer un token JWT temporaire dédié à l’authentification WebSocket.
 *
 * Sert à sécuriser la connexion WebSocket côté frontend,
 * en fournissant un JWT très court (120s) avec un payload spécifique.
 */
class WebSocketTokenProvider implements ProviderInterface
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
        private TokenStorageInterface $tokenStorage
    ) {}

    /**
     * Fournit un token JWT temporaire pour un utilisateur authentifié.
     *
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return WebSocketTokenRead|null    Contient le token JWT et sa date d’expiration, ou null si non connecté
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): WebSocketTokenRead|null
    {
        // Récupère l’utilisateur courant via le token de sécurité
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user instanceof User) {
            // Pas d’utilisateur connecté, retourne null
            return null;
        }

        // Génère un JWT court (2 min) avec un payload spécifique 'purpose'
        $exp = time() + 120;
        $token = $this->jwtManager->createFromPayload($user, [
            'purpose' => 'websocket',
            'exp' => $exp,
        ]);

        // Retourne le DTO contenant le token et sa date d’expiration
        return new WebSocketTokenRead($token, $exp);
    }
}
