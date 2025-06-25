<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Room;
use App\Entity\RoomUser;
use App\Enum\RoomRole;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;

class RoomFactoryService
{
    public function __construct(
        private EntityManagerInterface $em,
        private RoomRepository $roomRepository
    ) {}

    /**
     * Crée une room (privée ou de groupe).
     * Si une room privée entre les deux utilisateurs existe déjà, elle est retournée.
     *
     * @param bool $isGroup True = room de groupe, False = room privée
     * @param User $creator Le créateur de la room (toujours présent)
     * @param User[] $members Liste des autres membres à ajouter (facultatif)
     * @param string|null $groupName Nom du groupe (obligatoire si c'est un groupe)
     * @return Room La room créée ou existante
     */
    public function createRoom(bool $isGroup, User $creator, array $members = [], ?string $groupName = null): Room
    {
        // Si c'est une room privée et il y a un seul membre, on vérifie si elle existe déjà
        if (!$isGroup && count($members) === 1) {
            $existingRoom = $this->roomRepository->findPrivateRoomBetweenUsers($creator, $members[0]);
            if ($existingRoom !== null) {
                return $existingRoom;
            }
        }

        $room = new Room();
        $room->setIsGroup($isGroup);

        if ($isGroup && $groupName !== null) {
            $room->setName($groupName);
        }

        $this->em->persist($room);

        // Ajout du créateur avec rôle OWNER
        $this->addRoomUser($room, $creator, RoomRole::Owner);

        foreach ($members as $member) {
            if ($member === $creator) {
                continue;
            }

            $this->addRoomUser($room, $member, RoomRole::User);
        }

        $this->em->flush();

        return $room;
    }

    /**
     * Associe un utilisateur à une room avec un rôle donné.
     */
    private function addRoomUser(Room $room, User $user, RoomRole $role): void
    {
        $roomUser = new RoomUser();
        $roomUser->setRoom($room);
        $roomUser->setUser($user);
        $roomUser->setRole($role);

        $this->em->persist($roomUser);

        $room->addMember($roomUser);
    }
}
