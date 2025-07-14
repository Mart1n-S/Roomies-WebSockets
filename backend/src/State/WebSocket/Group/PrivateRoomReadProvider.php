<?php

namespace App\State\WebSocket\Group;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

use App\Mapper\GroupMapper;
use App\Repository\RoomRepository;
use App\Repository\MessageRepository;
use App\Dto\Group\GroupReadDTO;
use App\Entity\RoomUser;

final class PrivateRoomReadProvider
{
    public function __construct(
        private RoomRepository $roomRepository,
        private GroupMapper $groupMapper,
        private MessageRepository $messageRepository,
        private EntityManagerInterface $em
    ) {}

    /**
     * Retourne les discussions privées avec les compteurs de messages non lus.
     *
     * @param User $user
     * @return GroupReadDTO[]
     */
    public function getPrivateRoomsForUser(User $user): array
    {
        $rooms = $this->roomRepository->findPrivateRoomsByUser($user);
        $result = [];

        foreach ($rooms as $room) {
            $dto = $this->groupMapper->toReadDto($room);

            // Récupération du RoomUser (membre courant)
            /** @var RoomUser|null $roomUser */
            $roomUser = $room->getMembers()->filter(
                fn(RoomUser $ru) => $ru->getUser() === $user
            )->first();

            if (!$roomUser) {
                continue;
            }

            $this->em->refresh($roomUser);


            // Calcul des messages non lus
            $unreadCount = $this->messageRepository->countUnreadForRoomUser($roomUser);

            $dto->unreadCount = $unreadCount;
            $dto->isGroup = false;

            // Nom = pseudo de l'autre membre
            $otherMember = $room->getMembers()->filter(
                fn(RoomUser $ru) => $ru->getUser() !== $user
            )->first()?->getUser();

            $dto->name = $otherMember?->getPseudo() ?? 'Discussion privée';

            $result[] = $dto;
        }

        return $result;
    }
}
