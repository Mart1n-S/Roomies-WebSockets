<?php

namespace App\WebSocket\Handler;

use App\Entity\User;
use App\Enum\RoomRole;
use App\Entity\RoomUser;
use App\Mapper\GroupMapper;
use Ratchet\ConnectionInterface;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use App\Repository\MessageRepository;
use App\Repository\RoomUserRepository;
use App\Repository\FriendshipRepository;
use App\WebSocket\Connection\ConnectionRegistry;
use App\WebSocket\Contract\WebSocketHandlerInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Handler WebSocket pour toutes les opérations d’administration de groupes.
 *
 * Gère l’ajout, le retrait, la suppression, la modification des membres et des paramètres d’un groupe,
 * avec vérifications de droits, feedback en temps réel et notifications personnalisées.
 */
class AdministrationGroupHandler implements WebSocketHandlerInterface
{
    public function __construct(
        private readonly RoomRepository $roomRepository,
        private readonly RoomUserRepository $roomUserRepository,
        private readonly MessageRepository $messageRepository,
        private readonly UserRepository $userRepository,
        private readonly FriendshipRepository $friendshipRepository,
        private readonly GroupMapper $groupMapper,
        private readonly SerializerInterface $serializer,
        private readonly ConnectionRegistry $registry
    ) {}

    /**
     * Indique si ce handler prend en charge le type de message reçu.
     *
     * @param string $type
     * @return bool
     */
    public function supports(string $type): bool
    {
        return in_array($type, [
            'group_add_member',
            'group_leave',
            'group_delete',
            'group_update_settings',
            'group_kick_member'
        ], true);
    }

    /**
     * Point d’entrée principal du handler : dispatch en fonction du type de message.
     *
     * @param ConnectionInterface $conn
     * @param array $message
     */
    public function handle(ConnectionInterface $conn, array $message): void
    {
        $user = $conn->user;
        if (!$user instanceof User) {
            $this->sendError($conn, 'Utilisateur non authentifié.');
            return;
        }

        $type = $message['type'] ?? null;

        // Dispatch vers la bonne méthode selon le type d’action
        match ($type) {
            'group_add_member'      => $this->handleAddMember($conn, $user, $message),
            'group_leave'           => $this->handleLeaveGroup($conn, $user, $message),
            'group_delete'          => $this->handleDeleteGroup($conn, $user, $message),
            'group_update_settings' => $this->handleUpdateGroupSettings($conn, $user, $message),
            'group_kick_member'     => $this->handleKickMember($conn, $user, $message),
            default                 => $this->sendError($conn, 'Type de message non supporté.'),
        };
    }

    /**
     * Ajoute un ou plusieurs membres à un groupe, après vérifications.
     */
    private function handleAddMember(ConnectionInterface $conn, User $user, array $message): void
    {
        $roomId = $message['roomId'] ?? null;
        $friendCodes = $message['friendCodes'] ?? $message['friendCode'] ?? null;

        if (!$roomId || !$friendCodes) {
            $this->sendError($conn, 'Paramètres invalides.');
            return;
        }

        $friendCodes = is_array($friendCodes) ? $friendCodes : [$friendCodes];
        $room = $this->roomRepository->find($roomId);

        if (!$room || !$room->isGroup()) {
            $this->sendError($conn, 'Groupe introuvable.');
            return;
        }

        // Vérifie que l'utilisateur courant est membre du groupe
        if (!$room->getMembers()->exists(fn($key, $member) => $member->getUser() === $user)) {
            $this->sendError($conn, 'Vous ne faites pas partie de ce groupe.');
            return;
        }

        foreach ($friendCodes as $friendCode) {
            $friend = $this->userRepository->findOneBy(['friendCode' => $friendCode]);

            if (!$friend) {
                $this->sendError($conn, "Ami avec le code $friendCode introuvable.");
                continue;
            }

            // Ignore déjà membre
            if ($room->getMembers()->exists(fn($key, $member) => $member->getUser() === $friend)) {
                continue;
            }

            // Vérifie la relation d’amitié
            $friendship = $this->friendshipRepository->findFriendshipBetween($user, $friend);
            if (!$friendship || $friendship->getStatus()->value !== 'friend') {
                $this->sendError($conn, "Vous devez être ami avec $friendCode pour l’ajouter.");
                continue;
            }

            // Ajoute le nouveau membre au groupe
            $roomUser = new RoomUser();
            $roomUser->setRoom($room);
            $roomUser->setUser($friend);
            $this->roomUserRepository->save($roomUser, true);
            $room->addMember($roomUser);
        }

        $this->broadcastGroupUpdate($room);
    }

    /**
     * Permet à un membre de quitter le groupe (suppression des messages si besoin).
     */
    private function handleLeaveGroup(ConnectionInterface $conn, User $user, array $message): void
    {
        $roomId = $message['roomId'] ?? null;

        if (!$roomId) {
            $this->sendError($conn, 'Paramètres invalides.');
            return;
        }

        $room = $this->roomRepository->find($roomId);

        if (!$room || !$room->isGroup()) {
            $this->sendError($conn, 'Groupe introuvable.');
            return;
        }

        // Cherche la relation RoomUser correspondante
        $membership = $room->getMembers()->filter(
            fn(RoomUser $member) => $member->getUser() === $user
        )->first();

        if (!$membership) {
            $this->sendError($conn, 'Vous ne faites pas partie de ce groupe.');
            return;
        }

        // Suppression des messages du membre dans ce groupe
        $messages = $this->messageRepository->findBy(['room' => $room, 'sender' => $user]);
        foreach ($messages as $message) {
            $this->messageRepository->remove($message, true);
        }

        // Retire le membre
        $this->roomUserRepository->remove($membership, true);
        $room->removeMember($membership);

        $this->broadcastGroupUpdate($room);
    }

    /**
     * Supprime un groupe (opération réservée au propriétaire).
     * Notifie tous les membres en temps réel.
     */
    private function handleDeleteGroup(ConnectionInterface $conn, User $user, array $message): void
    {
        $roomId = $message['roomId'] ?? null;

        if (!$roomId) {
            $this->sendError($conn, 'Paramètres invalides.');
            return;
        }

        $room = $this->roomRepository->find($roomId);

        if (!$room || !$room->isGroup()) {
            $this->sendError($conn, 'Groupe introuvable.');
            return;
        }

        $membership = $room->getMembers()->filter(
            fn(RoomUser $member) => $member->getUser() === $user
        )->first();

        if (!$membership) {
            $this->sendError($conn, 'Vous ne faites pas partie de ce groupe.');
            return;
        }

        if ($membership->getRole() !== RoomRole::Owner) {
            $this->sendError($conn, 'Seul le propriétaire peut supprimer le groupe.');
            return;
        }

        // Notification à tous les membres avant suppression
        foreach ($room->getMembers() as $member) {
            $memberConn = $this->registry->getConnection($member->getUser()->getId());
            if ($memberConn) {
                $memberConn->send(json_encode([
                    'type' => 'group_deleted',
                    'roomId' => $room->getId()->toRfc4122(),
                ]));
            }
        }

        // Suppression des RoomUser
        foreach ($room->getMembers() as $member) {
            $this->roomUserRepository->remove($member, true);
        }

        // Suppression de la room
        $this->roomRepository->remove($room, true);
    }

    /**
     * Permet au propriétaire de modifier les paramètres d’un groupe
     * (nom et rôles des membres).
     */
    private function handleUpdateGroupSettings(ConnectionInterface $conn, User $user, array $message): void
    {
        $roomId = $message['roomId'] ?? null;
        $newName = $message['name'] ?? null;
        $roles = $message['roles'] ?? [];

        // Vérification des paramètres de base
        if (!$roomId) {
            $this->sendError($conn, 'ID de salon manquant.');
            return;
        }

        if (!$newName) {
            $this->sendError($conn, 'Nom du serveur manquant.');
            return;
        }

        if (!is_array($roles)) {
            $this->sendError($conn, 'Format des rôles invalide.');
            return;
        }

        // Nettoyage et validation du nom
        $newName = strip_tags(trim($newName));
        if (empty($newName)) {
            $this->sendError($conn, 'Le nom du serveur est obligatoire.');
            return;
        }

        if (strlen($newName) > 30) {
            $this->sendError($conn, 'Le nom ne doit pas dépasser 30 caractères.');
            return;
        }

        $room = $this->roomRepository->find($roomId);
        if (!$room) {
            $this->sendError($conn, 'Groupe introuvable.');
            return;
        }

        if (!$room->isGroup()) {
            $this->sendError($conn, 'Ce salon n\'est pas un groupe.');
            return;
        }

        // Vérification des droits (doit être owner)
        $membership = $room->getMembers()->findFirst(fn($i, RoomUser $m) => $m->getUser() === $user);
        if (!$membership) {
            $this->sendError($conn, 'Vous ne faites pas partie de ce groupe.');
            return;
        }

        if ($membership->getRole() !== RoomRole::Owner) {
            $this->sendError($conn, 'Seul le propriétaire peut modifier les paramètres.');
            return;
        }

        // Mise à jour du nom du groupe
        $room->setName($newName);
        $this->roomRepository->save($room, true);

        // Traitement des changements de rôles membres (avec gestion d’erreurs détaillée)
        $errors = [];
        foreach ($roles as $index => $roleUpdate) {
            $friendCode = $roleUpdate['friendCode'] ?? null;
            $newRole = $roleUpdate['role'] ?? null;

            if (!$friendCode) {
                $errors[] = "Entrée #$index: Code ami manquant";
                continue;
            }

            if (!$newRole) {
                $errors[] = "Membre $friendCode: Rôle manquant";
                continue;
            }

            try {
                // Conversion en enum RoomRole (attention ValueError si rôle non valide)
                $roleEnum = RoomRole::from($newRole);

                // Autorise seulement les rôles 'user' et 'admin'
                if (!in_array($roleEnum, [RoomRole::User, RoomRole::Admin])) {
                    continue;
                }

                $memberUser = $this->userRepository->findOneBy(['friendCode' => $friendCode]);
                if (!$memberUser) {
                    $errors[] = "Membre $friendCode: Utilisateur introuvable";
                    continue;
                }

                $roomUser = $room->getMembers()->findFirst(fn($i, RoomUser $m) => $m->getUser() === $memberUser);
                if (!$roomUser) {
                    $errors[] = "Membre $friendCode: Ne fait pas partie du groupe";
                    continue;
                }

                if ($roomUser->getRole() === RoomRole::Owner) {
                    $errors[] = "Membre $friendCode: Impossible de modifier le propriétaire";
                    continue;
                }

                $roomUser->setRole($roleEnum);
                $this->roomUserRepository->save($roomUser, true);
            } catch (\ValueError $e) {
                $errors[] = "Membre $friendCode: Rôle '$newRole' invalide";
                continue;
            }
        }

        if (!empty($errors)) {
            $this->sendError($conn, 'Modifications appliquées avec certaines erreurs: ' . implode(', ', $errors));
        }

        $this->broadcastGroupUpdate($room);
    }

    /**
     * Exclut un membre du groupe (avec contrôle fin des droits : owner/admin/user).
     * Notifie le membre exclu en direct.
     */
    private function handleKickMember(ConnectionInterface $conn, User $user, array $message): void
    {
        $roomId = $message['roomId'] ?? null;
        $friendCode = $message['friendCode'] ?? null;

        if (!$roomId || !$friendCode) {
            $this->sendError($conn, 'Paramètres invalides.');
            return;
        }

        $room = $this->roomRepository->find($roomId);

        if (!$room || !$room->isGroup()) {
            $this->sendError($conn, 'Groupe introuvable.');
            return;
        }

        $me = $room->getMembers()->findFirst(fn($i, RoomUser $ru) => $ru->getUser() === $user);
        if (!$me) {
            $this->sendError($conn, 'Vous ne faites pas partie de ce groupe.');
            return;
        }

        // Contrôle de permission en fonction du rôle
        $targetUser = $this->userRepository->findOneBy(['friendCode' => $friendCode]);
        if (!$targetUser) {
            $this->sendError($conn, "Membre introuvable.");
            return;
        }

        $targetMembership = $room->getMembers()->findFirst(fn($i, RoomUser $ru) => $ru->getUser() === $targetUser);
        if (!$targetMembership) {
            $this->sendError($conn, "Ce membre ne fait pas partie du groupe.");
            return;
        }

        if ($me->getUser() === $targetUser) {
            $this->sendError($conn, "Impossible de vous exclure vous-même.");
            return;
        }

        $myRole = $me->getRole();
        $targetRole = $targetMembership->getRole();

        // Owner peut tout sauf exclure un autre owner (protection supplémentaire)
        if ($myRole === RoomRole::Owner) {
            if ($targetRole === RoomRole::Owner) {
                $this->sendError($conn, "Impossible d'exclure le propriétaire.");
                return;
            }
        } elseif ($myRole === RoomRole::Admin) {
            if ($targetRole !== RoomRole::User) {
                $this->sendError($conn, "Vous ne pouvez exclure que les membres ayant le rôle 'user'.");
                return;
            }
        } else {
            $this->sendError($conn, "Vous n'avez pas la permission d'exclure un membre.");
            return;
        }

        // Suppression des messages du membre exclu (optionnel)
        $messages = $this->messageRepository->findBy(['room' => $room, 'sender' => $targetUser]);
        foreach ($messages as $message) {
            $this->messageRepository->remove($message, true);
        }

        $this->roomUserRepository->remove($targetMembership, true);
        $room->removeMember($targetMembership);

        // Notifie le membre exclu en temps réel
        $targetConn = $this->registry->getConnection($targetUser->getId());
        if ($targetConn) {
            $targetConn->send(json_encode([
                'type' => 'group_kicked',
                'roomId' => $room->getId()->toRfc4122(),
            ]));
        }

        // Mise à jour en broadcast du groupe pour tous les membres restants
        $this->broadcastGroupUpdate($room);
    }

    /**
     * Envoie à tous les membres du groupe un état à jour (DTO group_updated).
     *
     * @param $room
     */
    private function broadcastGroupUpdate($room): void
    {
        $groupDto = $this->groupMapper->toReadDto($room);
        $json = $this->serializer->serialize($groupDto, 'json', ['groups' => ['read:group']]);

        foreach ($room->getMembers() as $membership) {
            $member = $membership->getUser();
            $memberConn = $this->registry->getConnection($member->getId());

            if ($memberConn) {
                $memberConn->send(json_encode([
                    'type' => 'group_updated',
                    'room' => json_decode($json, true),
                ]));
            }
        }
    }

    /**
     * Envoie un message d’erreur à la connexion courante.
     *
     * @param ConnectionInterface $conn
     * @param string $message
     */
    private function sendError(ConnectionInterface $conn, string $message): void
    {
        $conn->send(json_encode([
            'type' => 'error',
            'message' => $message,
        ]));
    }
}
