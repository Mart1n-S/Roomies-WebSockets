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

    public function supports(string $type): bool
    {
        return in_array($type, ['group_add_member', 'group_leave', 'group_delete', 'group_update_settings', 'group_kick_member'], true);
    }

    public function handle(ConnectionInterface $conn, array $message): void
    {
        $user = $conn->user;
        if (!$user instanceof User) {
            $this->sendError($conn, 'Utilisateur non authentifié.');
            return;
        }

        $type = $message['type'] ?? null;

        match ($type) {
            'group_add_member' => $this->handleAddMember($conn, $user, $message),
            'group_leave' => $this->handleLeaveGroup($conn, $user, $message),
            'group_delete' => $this->handleDeleteGroup($conn, $user, $message),
            'group_update_settings' => $this->handleUpdateGroupSettings($conn, $user, $message),
            'group_kick_member' => $this->handleKickMember($conn, $user, $message),
            default => $this->sendError($conn, 'Type de message non supporté.'),
        };
    }

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

            if ($room->getMembers()->exists(fn($key, $member) => $member->getUser() === $friend)) {
                continue;
            }

            $friendship = $this->friendshipRepository->findFriendshipBetween($user, $friend);
            if (!$friendship || $friendship->getStatus()->value !== 'friend') {
                $this->sendError($conn, "Vous devez être ami avec $friendCode pour l’ajouter.");
                continue;
            }

            $roomUser = new RoomUser();
            $roomUser->setRoom($room);
            $roomUser->setUser($friend);
            $this->roomUserRepository->save($roomUser, true);
            $room->addMember($roomUser);
        }

        $this->broadcastGroupUpdate($room);
    }

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

        $membership = $room->getMembers()->filter(
            fn(RoomUser $member) => $member->getUser() === $user
        )->first();

        if (!$membership) {
            $this->sendError($conn, 'Vous ne faites pas partie de ce groupe.');
            return;
        }

        // Supprime les messages du membre si besoin ici (si géré ailleurs, ignorer)
        $messages = $this->messageRepository->findBy(['room' => $room, 'sender' => $user]);
        foreach ($messages as $message) {
            $this->messageRepository->remove($message, true);
        }


        $this->roomUserRepository->remove($membership, true);
        $room->removeMember($membership);

        $this->broadcastGroupUpdate($room);
    }

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

        // Notifie tous les membres
        foreach ($room->getMembers() as $member) {
            $memberConn = $this->registry->getConnection($member->getUser()->getId());
            if ($memberConn) {
                $memberConn->send(json_encode([
                    'type' => 'group_deleted',
                    'roomId' => $room->getId()->toRfc4122(),
                ]));
            }
        }

        // Supprime les RoomUser
        foreach ($room->getMembers() as $member) {
            $this->roomUserRepository->remove($member, true);
        }

        // Supprime la room
        $this->roomRepository->remove($room, true);
    }

    private function handleUpdateGroupSettings(ConnectionInterface $conn, User $user, array $message): void
    {
        $roomId = $message['roomId'] ?? null;
        $newName = $message['name'] ?? null;
        $roles = $message['roles'] ?? [];

        // Validation de base
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

        // Validation et nettoyage du nom
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

        // Vérification des droits
        $membership = $room->getMembers()->findFirst(fn($i, RoomUser $m) => $m->getUser() === $user);
        if (!$membership) {
            $this->sendError($conn, 'Vous ne faites pas partie de ce groupe.');
            return;
        }

        if ($membership->getRole() !== RoomRole::Owner) {
            $this->sendError($conn, 'Seul le propriétaire peut modifier les paramètres.');
            return;
        }

        // Mise à jour du nom
        $room->setName($newName);
        $this->roomRepository->save($room, true);

        // Traitement des rôles avec gestion d'erreurs détaillée
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
                // Conversion du string en enum RoomRole
                $roleEnum = RoomRole::from($newRole);

                // Vérification des rôles autorisés
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

                $roomUser->setRole($roleEnum); // Utilisation de l'enum directement
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

        // Droits : owner peut tout, admin peut kick user, pas owner, user ne peut rien
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

        if ($myRole === RoomRole::Owner) {
            // Owner peut tout sauf se kick lui-même (déjà géré)
            if ($targetRole === RoomRole::Owner) {
                $this->sendError($conn, "Impossible d'exclure le propriétaire.");
                return;
            }
        } elseif ($myRole === RoomRole::Admin) {
            // Admin peut kick seulement les users
            if ($targetRole !== RoomRole::User) {
                $this->sendError($conn, "Vous ne pouvez exclure que les membres ayant le rôle 'user'.");
                return;
            }
        } else {
            // User ne peut rien
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

        // Notifier le membre exclu
        $targetConn = $this->registry->getConnection($targetUser->getId());
        if ($targetConn) {
            $targetConn->send(json_encode([
                'type' => 'group_kicked',
                'roomId' => $room->getId()->toRfc4122(),
            ]));
        }

        // Broadcast maj du groupe à tous les membres restants
        $this->broadcastGroupUpdate($room);
    }


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

    private function sendError(ConnectionInterface $conn, string $message): void
    {
        $conn->send(json_encode([
            'type' => 'error',
            'message' => $message,
        ]));
    }
}
