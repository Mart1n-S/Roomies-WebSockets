<?php

namespace App\WebSocket\Handler;

use App\Repository\UserRepository;
use App\WebSocket\Connection\ConnectionRegistry;
use App\WebSocket\Contract\WebSocketHandlerInterface;
use Ratchet\ConnectionInterface;

/**
 * Handler WebSocket pour vérifier le statut "en ligne" (online/offline) d'une liste d'amis.
 *
 * Ce handler permet au frontend de demander l'état de connexion de plusieurs users via leurs friendCodes.
 * Pour chaque code ami fourni, il renvoie au client un message contenant le friendCode et un booléen "online".
 */
class RequestStatusHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly ConnectionRegistry $registry,
        private readonly UserRepository $userRepository,
    ) {}

    /**
     * Déclare gérer le type de message 'request_status'
     */
    public function supports(string $type): bool
    {
        return $type === 'request_status';
    }

    /**
     * Pour chaque friendCode envoyé, retourne au client si l'utilisateur correspondant est en ligne.
     *
     * @param ConnectionInterface $conn   Connexion WebSocket du client demandeur
     * @param array $message              Message WebSocket contenant 'payload.friendCodes'
     */
    public function handle(ConnectionInterface $conn, array $message): void
    {
        try {
            $payload = $message['payload'] ?? [];

            // Validation du format de la requête
            if (!isset($payload['friendCodes']) || !is_array($payload['friendCodes'])) {
                throw new \InvalidArgumentException('Liste des friendCodes manquante ou invalide.');
            }

            // Pour chaque code ami, recherche le User et envoie le statut online/offline
            foreach ($payload['friendCodes'] as $friendCode) {
                if (!is_string($friendCode)) {
                    continue; // Sécurité : on ignore si ce n'est pas une string
                }

                $user = $this->userRepository->findOneBy(['friendCode' => $friendCode]);

                if (!$user) {
                    continue; // User non trouvé = aucun message envoyé
                }

                $isOnline = $this->registry->isConnected($user->getId());

                // Réponse envoyée au client pour chaque user testé
                $conn->send(json_encode([
                    'type' => 'user-status',
                    'friendCode' => $friendCode,
                    'online' => $isOnline
                ]));
            }
        } catch (\Throwable $e) {
            // Gestion d’erreur : message explicite côté client
            $conn->send(json_encode([
                'type' => 'error',
                'message' => 'Erreur dans request_status : ' . $e->getMessage(),
            ]));
        }
    }
}
