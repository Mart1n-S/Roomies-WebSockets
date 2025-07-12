<?php

namespace App\State\WebSocket\Group;

use App\Entity\User;
use App\Mapper\GroupMapper;
use App\Repository\RoomUserRepository;
use App\Dto\Group\GroupReadDTO;

/**
 * Fournit les groupes (rooms de type groupe) auxquels appartient un utilisateur.
 */
final class GroupReadProvider
{
    public function __construct(
        private readonly RoomUserRepository $roomUserRepository,
        private readonly GroupMapper $groupMapper
    ) {}

    /**
     * Retourne tous les groupes (rooms avec isGroup = true) de l’utilisateur.
     *
     * @param User $user
     * @return GroupReadDTO[]
     */
    public function getGroupsForUser(User $user): array
    {
        $roomUsers = $this->roomUserRepository->findGroupsForUser($user);

        $rooms = array_map(fn($ru) => $ru->getRoom(), $roomUsers);

        return array_map(
            fn($room) => $this->groupMapper->toReadDto($room),
            $rooms
        );
    }
}
