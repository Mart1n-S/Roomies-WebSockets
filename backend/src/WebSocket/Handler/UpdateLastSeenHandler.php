<?php

namespace App\WebSocket\Handler;

use App\Entity\User;
use Ratchet\ConnectionInterface;
use App\Repository\RoomUserRepository;
use App\Repository\MessageRepository;
use App\WebSocket\Contract\WebSocketHandlerInterface;

/**
 * Handler WebSocket permettant de mettre à jour la date de "dernière visite" d'un utilisateur dans une room.
 *
 * Utilisé pour :
 * - Actualiser le champ lastSeenAt d’un membre dès qu’il ouvre la room côté front.
 * - Recalculer et retourner en direct le nombre de messages non lus pour ce membre.
 * - Garantir un affichage dynamique du badge "non lus" côté client.
 */
class UpdateLastSeenHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly RoomUserRepository $roomUserRepository,
        private readonly MessageRepository $messageRepository
    ) {}

    /**
     * Ce handler gère les messages de type 'update_last_seen'
     */
    public function supports(string $type): bool
    {
        return $type === 'update_last_seen';
    }

    /**
     * Met à jour le lastSeenAt du user pour la room, puis renvoie le nouveau nombre de messages non lus.
     *
     * @param ConnectionInterface $conn   Connexion WebSocket du client
     * @param array $message              Doit contenir 'roomId'
     */
    public function handle(ConnectionInterface $conn, array $message): void
    {
        try {
            /** @var User $user */
            $user = $conn->user ?? null;
            $roomId = $message['roomId'] ?? null;

            // Validation de la présence des infos requises
            if (!$user || !$roomId) {
                throw new \RuntimeException('Utilisateur ou roomId manquant.');
            }

            // Recherche l'association RoomUser (relation entre user et room)
            $roomUser = $this->roomUserRepository->findOneByUserAndRoomId($user, $roomId);

            if (!$roomUser) {
                throw new \RuntimeException("Tu ne fais pas partie de cette room.");
            }

            // 1. Met à jour la date de dernière visite
            $roomUser->setLastSeenAt(new \DateTimeImmutable());
            $this->roomUserRepository->save($roomUser, true);

            // 2. Recalcule le nombre de messages non lus pour ce user dans la room
            $unreadCount = $this->messageRepository->countUnreadForRoomUser($roomUser);

            // 3. Retourne au client le nouveau nombre de messages non lus
            $conn->send(json_encode([
                'type' => 'room_unread_updated',
                'roomId' => $roomId,
                'unreadCount' => (int) $unreadCount,
            ]));
        } catch (\Throwable $e) {
            // Silencieux en cas d’erreur : à adapter selon politique UX/Logs
        }
    }
}
