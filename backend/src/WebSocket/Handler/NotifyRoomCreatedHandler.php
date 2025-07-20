<?php

namespace App\WebSocket\Handler;

use App\Repository\UserRepository;
use App\WebSocket\Contract\WebSocketHandlerInterface;
use App\WebSocket\Connection\ConnectionRegistry;
use Ratchet\ConnectionInterface;

/**
 * Handler WebSocket pour notifier en temps réel les membres d'un groupe de la création d'une nouvelle room.
 *
 * Lorsqu'une room est créée (ex : nouveau groupe), ce handler envoie un message
 * à chaque membre connecté (via leur friendCode), pour leur afficher la nouvelle room dans la sidebar.
 */
class NotifyRoomCreatedHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly ConnectionRegistry $registry
    ) {}

    /**
     * Prend en charge les messages de type 'notify_room_created'
     */
    public function supports(string $type): bool
    {
        return $type === 'notify_room_created';
    }

    /**
     * Pour chaque membre du groupe, s'il est connecté, lui envoie la notification de création de room.
     *
     * @param ConnectionInterface $conn   Connexion de l'émetteur de la requête
     * @param array $message              Doit contenir 'payload' avec 'room' et 'memberFriendCodes'
     */
    public function handle(ConnectionInterface $conn, array $message): void
    {
        $payload = $message['payload'] ?? null;

        // Vérifie la présence du payload requis
        if (!$payload || !isset($payload['room'], $payload['memberFriendCodes'])) {
            $conn->send(json_encode([
                'type' => 'error',
                'message' => 'Payload invalide pour notify_room_created'
            ]));
            return;
        }

        // Parcourt chaque code ami pour trouver le User associé et envoyer la notif s’il est connecté
        foreach ($payload['memberFriendCodes'] as $friendCode) {
            $user = $this->userRepository->findOneBy(['friendCode' => $friendCode]);

            if (!$user) {
                continue; // Si l’utilisateur n’existe pas (rare), on skip
            }

            $receiverConn = $this->registry->getConnection($user->getId());

            if ($receiverConn) {
                $receiverConn->send(json_encode([
                    'type' => 'room_created',
                    'room' => $payload['room']
                ]));
            }
        }

        // Confirme à l’émetteur que la notification a été traitée
        $conn->send(json_encode([
            'type' => 'room_notified',
            'message' => 'Notification envoyée aux membres connectés.'
        ]));
    }
}
