<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Log\LoggerInterface;

class WebSocketAuthenticator
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly string $publicKeyPath,
        private readonly LoggerInterface $logger
    ) {}

    /**
     * Authentifie un utilisateur à partir d’un JWT RS256.
     *
     * @param string $token JWT transmis par le client
     * @return User|null L'utilisateur authentifié ou null en cas d’échec
     */
    public function authenticate(string $token): ?User
    {
        try {
            $publicKey = file_get_contents($this->publicKeyPath);

            if (!$publicKey) {
                $this->logger->error('Clé publique introuvable à : ' . $this->publicKeyPath);
                return null;
            }

            $this->logger->info('🛡️ Début authentification WebSocket');

            $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));

            $payload = (array) $decoded;

            $this->logger->debug('✅ JWT décodé avec succès', [
                'payload' => (array) $decoded
            ]);

            if (!isset($payload['purpose']) || $payload['purpose'] !== 'websocket') {
                $this->logger->warning('⚠️ Token non prévu pour WebSocket (purpose absent ou invalide)');
                return null;
            }

            $userId = isset($decoded->id) ? (string) $decoded->id : null;

            if (!$userId) {
                $this->logger->warning('❌ ID manquant dans le token JWT');
                return null;
            }

            /** @var User|null $user */
            $user = $this->em->getRepository(User::class)->find($userId);

            if (!$user) {
                $this->logger->warning("❌ Aucun utilisateur trouvé avec l'ID $userId");
                return null;
            }

            $this->logger->info("✅ Utilisateur authentifié WebSocket : {$user->getEmail()} (ID: {$user->getId()})");

            return $user;
        } catch (\Throwable $e) {
            $this->logger->warning('❌ Échec de l\'authentification WebSocket', [
                'exception' => $e,
                'token' => $token,
            ]);
            return null;
        }
    }
}
