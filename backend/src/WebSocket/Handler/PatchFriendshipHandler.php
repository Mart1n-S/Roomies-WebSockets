<?php

namespace App\WebSocket\Handler;

use App\Entity\User;
use Ratchet\ConnectionInterface;
use App\WebSocket\Connection\ConnectionRegistry;
use App\State\WebSocket\Friendship\FriendshipPatchWebSocketProcessor;
use App\WebSocket\Contract\WebSocketHandlerInterface;

/**
 * Handler WebSocket pour la gestion des réponses à une demande d’amitié
 * (acceptation ou refus) en temps réel.
 *
 * - Valide et traite l’action (accepter ou refuser) sur une friendship via WebSocket.
 * - Notifie tous les membres concernés (demandeur et destinataire) du changement.
 * - Gère la création de la room privée lors d’une acceptation.
 */
class PatchFriendshipHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly FriendshipPatchWebSocketProcessor $processor,
        private readonly ConnectionRegistry $registry,
    ) {}

    /**
     * Indique que ce handler gère le type 'patch_friendship'.
     */
    public function supports(string $type): bool
    {
        return $type === 'patch_friendship';
    }

    /**
     * Traite l’action d’acceptation ou de refus d’une demande d’ami.
     *
     * - Valide l’utilisateur et les paramètres.
     * - Appelle le processor métier.
     * - Notifie l’utilisateur courant, son ami, et le demandeur selon le cas.
     */
    public function handle(ConnectionInterface $conn, array $message): void
    {
        try {
            // Vérifie que l’utilisateur est bien connecté/authentifié
            if (!isset($conn->user) || !$conn->user instanceof User) {
                throw new \RuntimeException('Utilisateur non authentifié.');
            }

            $user = $conn->user;
            $payload = $message['payload'] ?? [];
            $friendshipId = $payload['friendshipId'] ?? null;
            $action = $payload['action'] ?? null;

            // Validation stricte des paramètres attendus
            if (!$friendshipId || !in_array($action, ['accepter', 'refuser'], true)) {
                throw new \InvalidArgumentException('Paramètres invalides.');
            }

            // Exécute le traitement métier via le processor (update friendship, création room si besoin…)
            $result = $this->processor->process($user, $friendshipId, $action);

            // === Cas de l’acceptation ===
            if ($action === 'accepter' && $result) {
                $response = [
                    'type' => 'friendship_updated',
                    'friendship' => $result['friendship'],
                    'room' => $result['room'],
                ];

                // 1. Notifie l’utilisateur courant
                $conn->send(json_encode($response));

                // 2. Notifie l’autre membre de la nouvelle room privée (s’il est connecté)
                foreach ($result['room']->members as $memberDto) {
                    $friendDto = $memberDto->member;
                    if ($friendDto->friendCode !== $user->getFriendCode()) {
                        $friendEntity = $this->registry->getUserByFriendCode($friendDto->friendCode);
                        if ($friendEntity) {
                            $targetConn = $this->registry->getConnection($friendEntity->getId());
                            if ($targetConn) {
                                $targetConn->send(json_encode($response));
                            }
                        }
                    }
                }
            }

            // === Cas du refus ===
            if ($action === 'refuser' && isset($result['friendship'])) {
                $applicant = $result['friendship']->getApplicant();
                $targetConn = $this->registry->getConnection($applicant->getId());

                if ($targetConn) {
                    $targetConn->send(json_encode([
                        'type' => 'friendship_deleted',
                        'friendshipId' => $friendshipId,
                    ]));
                }
            }
        } catch (\Throwable $e) {
            // Feedback d’erreur au client émetteur en cas de souci
            $conn->send(json_encode([
                'type' => 'friendship_error',
                'message' => $e->getMessage(),
            ]));
        }
    }
}
