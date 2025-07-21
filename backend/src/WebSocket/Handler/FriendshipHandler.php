<?php

namespace App\WebSocket\Handler;

use App\Entity\User;
use App\Mapper\FriendshipMapper;
use Ratchet\ConnectionInterface;
use App\Repository\UserRepository;
use App\Service\PushNotificationService;
use App\Dto\Friendship\FriendshipCreateDTO;
use App\WebSocket\Connection\ConnectionRegistry;
use App\State\Friendship\FriendshipCreateProcessor;
use App\WebSocket\Contract\WebSocketHandlerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Handler WebSocket pour la gestion des demandes d’amis.
 *
 * Prend en charge l’envoi, la validation et la notification d’une demande d’ami en temps réel
 * pour l’émetteur et le destinataire (si connecté), et envoie une notification push si activé.
 */
class FriendshipHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly FriendshipCreateProcessor $friendshipProcessor,
        private readonly SerializerInterface $serializer,
        private readonly ConnectionRegistry $connectionRegistry,
        private readonly UserRepository $userRepository,
        private readonly FriendshipMapper $friendshipMapper,
        private readonly PushNotificationService $pushNotificationService // <-- Ajout ici
    ) {}

    /**
     * Indique si ce handler prend en charge le type de message reçu.
     *
     * @param string $type
     * @return bool
     */
    public function supports(string $type): bool
    {
        return $type === 'friend_request';
    }

    /**
     * Traite l’envoi d’une demande d’ami en temps réel + notification push.
     *
     * @param ConnectionInterface $conn      Connexion de l’émetteur
     * @param array $message                 Message reçu (doit contenir 'payload' et 'friendCode')
     */
    public function handle(ConnectionInterface $conn, array $message): void
    {
        try {
            // 1. Vérifie l’authentification de l’émetteur
            if (!isset($conn->user) || !$conn->user instanceof UserInterface) {
                throw new \RuntimeException('Utilisateur non authentifié.');
            }

            // 2. Extraction et validation du payload
            $payload = $message['payload'] ?? null;
            if (!$payload || !isset($payload['friendCode'])) {
                throw new \InvalidArgumentException('Le champ friendCode est requis.');
            }

            // 3. Construction du DTO utilisé par le processor
            $dto = new FriendshipCreateDTO();
            $dto->friendCode = $payload['friendCode'];

            // 4. Création de la demande d’ami via le processor métier (validation, persistance, etc.)
            $dtoOut = $this->friendshipProcessor->process($dto, $conn->user);

            // 5. Sérialisation pour l’émetteur (retourne la demande créée)
            $jsonData = $this->serializer->serialize($dtoOut, 'json', ['groups' => ['read:friendship']]);
            $decodedData = json_decode($jsonData, true);

            // 6. Notification de succès à l’émetteur (lui-même)
            $conn->send(json_encode([
                'type' => 'friend_request_success',
                'data' => $decodedData,
            ]));

            // 7. Notification en temps réel au destinataire (si connecté)
            $recipient = $dtoOut->friend;
            $friendCode = $recipient->friendCode ?? null;

            $targetUser = $this->connectionRegistry->getUserByFriendCode($friendCode);
            if ($targetUser) {
                $targetConn = $this->connectionRegistry->getConnection($targetUser->getId());

                if ($targetConn) {
                    // Prépare une version inversée pour le destinataire (émetteur dans le champ 'friend')
                    $dtoForRecipient = clone $dtoOut;
                    $dtoForRecipient->friend = $this->friendshipMapper->mapUser($conn->user);

                    $jsonRecipient = $this->serializer->serialize($dtoForRecipient, 'json', ['groups' => ['read:friendship']]);
                    $decodedRecipient = json_decode($jsonRecipient, true);

                    $targetConn->send(json_encode([
                        'type' => 'friend_request_received',
                        'data' => $decodedRecipient,
                    ]));
                }
            }

            // 8. ENVOI D'UNE NOTIFICATION PUSH AU DESTINATAIRE SI ACTIVÉ
            // On recherche l'utilisateur destinataire via le UserRepository (toujours fiable)
            $recipientUser = $this->userRepository->findOneBy(['friendCode' => $friendCode]);
            if (
                $recipientUser &&
                $recipientUser->isPushNotificationsEnabled() &&
                $recipientUser->getPushEndpoint() &&
                $recipientUser->getPushP256dh() &&
                $recipientUser->getPushAuth()
            ) {
                /** @var User $applicant */
                $payload = [
                    'title' => 'Nouvelle demande d\'ami',
                    'body'  => 'Quelqu’un souhaite devenir votre ami !',
                    'icon'  => '/roomies-icon-192.png',
                    'badge' => '/roomies-icon-192.png',
                    'url'   => '/dashboard',
                ];

                $this->pushNotificationService->sendNotification(
                    $recipientUser->getPushEndpoint(),
                    $recipientUser->getPushP256dh(),
                    $recipientUser->getPushAuth(),
                    $payload
                );
            }
        } catch (\Throwable $e) {
            // Gestion globale des erreurs (format, droits, backend...)
            $conn->send(json_encode([
                'type' => 'friend_request_error',
                'message' => $e->getMessage(),
            ]));
        }
    }
}
