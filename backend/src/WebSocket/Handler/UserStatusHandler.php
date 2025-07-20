<?php

namespace App\WebSocket\Handler;

use Ratchet\ConnectionInterface;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use App\Repository\FriendshipRepository;
use App\WebSocket\Connection\ConnectionRegistry;
use App\WebSocket\Contract\WebSocketHandlerInterface;

/**
 * Handler WebSocket pour la gestion des statuts "en ligne" des utilisateurs (amis et groupes).
 *
 * ⚠️ Ce handler ne gère pas directement un type de message WebSocket
 *     (supports() retourne false), mais sert d'utilitaire pour notifier les amis
 *     et les membres de groupe d’un changement de présence (connexion/déconnexion).
 *
 * Principales responsabilités :
 * - Notifier les amis ou membres de groupes lorsque le statut de l'utilisateur change.
 * - Envoyer au client la liste de ses amis ou membres de groupes actuellement connectés.
 */
class UserStatusHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly ConnectionRegistry $registry,
        private readonly UserRepository $userRepository,
        private readonly FriendshipRepository $friendshipRepository,
        private readonly RoomRepository $roomRepository,
    ) {}

    /**
     * Ce handler n’est pas appelé via le routeur WebSocket classique.
     */
    public function supports(string $type): bool
    {
        // On ne l'appelle pas manuellement, mais on veut pouvoir l’enregistrer
        return false;
    }

    public function handle(ConnectionInterface $conn, array $message): void
    {
        // Pas utilisé ici.
    }

    /**
     * Notifie tous les amis de l’utilisateur (ayant une amitié confirmée)
     * que son statut (online/offline) a changé.
     *
     * @param ConnectionInterface $conn   Connexion de l'utilisateur dont le statut change
     * @param bool $online                true si connecté, false si déconnecté
     */
    public function notifyFriendsAboutStatusChange(ConnectionInterface $conn, bool $online): void
    {
        $user = $conn->user;
        if (!$user) return;

        $friendships = $this->friendshipRepository->findConfirmedFriendshipsForUser($user);

        foreach ($friendships as $friendship) {
            $friend = $friendship->getApplicant() === $user
                ? $friendship->getRecipient()
                : $friendship->getApplicant();

            $receiverConn = $this->registry->getConnection($friend->getId());
            if ($receiverConn) {
                $receiverConn->send(json_encode([
                    'type' => 'user-status',
                    'friendCode' => $user->getFriendCode(),
                    'online' => $online
                ]));
            }
        }
    }

    /**
     * Envoie à l'utilisateur la liste de ses amis actuellement connectés.
     * Utile pour l'affichage initial (au login).
     *
     * @param ConnectionInterface $conn   Connexion de l'utilisateur demandeur
     */
    public function sendOnlineFriendsList(ConnectionInterface $conn): void
    {
        $user = $conn->user;
        if (!$user) return;

        $friendships = $this->friendshipRepository->findConfirmedFriendshipsForUser($user);

        $onlineFriendCodes = [];
        foreach ($friendships as $friendship) {
            $friend = $friendship->getApplicant() === $user
                ? $friendship->getRecipient()
                : $friendship->getApplicant();
            if ($this->registry->isConnected($friend->getId())) {
                $onlineFriendCodes[] = $friend->getFriendCode();
            }
        }

        $conn->send(json_encode([
            'type' => 'bulk-status',
            'onlineFriends' => $onlineFriendCodes
        ]));
    }

    /**
     * Notifie tous les membres de groupes partagés que l'utilisateur vient de se connecter/déconnecter.
     * (Evite les doublons grâce à un SplObjectStorage)
     *
     * @param ConnectionInterface $conn   Connexion de l'utilisateur dont le statut change
     * @param bool $online                true si connecté, false si déconnecté
     */
    public function notifyGroupMembersAboutStatusChange(ConnectionInterface $conn, bool $online): void
    {
        $user = $conn->user;
        if (!$user) return;

        $rooms = $this->roomRepository->findGroupsForUser($user);
        $sentTo = new \SplObjectStorage();

        foreach ($rooms as $room) {
            foreach ($room->getMembers() as $membership) {
                $member = $membership->getUser();

                // Ne pas s’auto-notifier
                if ($member->getId() === $user->getId()) {
                    continue;
                }

                $receiverConn = $this->registry->getConnection($member->getId());

                // Notifie une seule fois chaque connexion, même si plusieurs rooms partagées
                if ($receiverConn && !$sentTo->contains($receiverConn)) {
                    $receiverConn->send(json_encode([
                        'type' => 'user-status',
                        'friendCode' => $user->getFriendCode(),
                        'online' => $online
                    ]));

                    $sentTo->attach($receiverConn);
                }
            }
        }
    }

    /**
     * Envoie à l'utilisateur la liste de tous les membres des groupes partagés qui sont actuellement connectés.
     * (Pour affichage initial au login sur la liste des membres de chaque serveur par exemple)
     *
     * @param ConnectionInterface $conn   Connexion de l'utilisateur demandeur
     */
    public function sendOnlineGroupMembersList(ConnectionInterface $conn): void
    {
        $user = $conn->user;
        if (!$user) return;

        $rooms = $this->roomRepository->findGroupsForUser($user);
        $onlineCodes = [];

        foreach ($rooms as $room) {
            foreach ($room->getMembers() as $membership) {
                $member = $membership->getUser();

                if ($this->registry->isConnected($member->getId())) {
                    $onlineCodes[] = $member->getFriendCode();
                }
            }
        }

        $conn->send(json_encode([
            'type' => 'bulk-status',
            'onlineFriends' => array_values(array_unique($onlineCodes))
        ]));
    }
}
