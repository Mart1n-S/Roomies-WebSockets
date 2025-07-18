<?php

namespace App\Mapper;

use App\Entity\Room;
use App\Dto\User\UserReadDTO;
use App\Dto\Group\GroupReadDTO;
use App\Dto\Group\GroupReadMemberDTO;
use App\Service\AvatarUrlGeneratorService;

class GroupMapper
{
    public function __construct(
        private AvatarUrlGeneratorService $avatarUrlGeneratorService
    ) {}

    /**
     * Transforme une Room (groupe) en GroupReadDTO
     *
     * @param Room $room
     * @return GroupReadDTO
     */
    public function toReadDto(Room $room): GroupReadDTO
    {
        $dto = new GroupReadDTO();
        $dto->id = $room->getId()->toRfc4122();
        $dto->name = $room->getName() ?? '';
        $dto->isGroup = $room->isGroup();
        $dto->createdAt = $room->getCreatedAt();

        foreach ($room->getMembers() as $roomUser) {

            $user = $roomUser->getUser();

            $userDto = new UserReadDTO();
            $userDto->pseudo = $user->getPseudo();

            $userDto->avatar = $this->avatarUrlGeneratorService->generate($user);

            $userDto->friendCode = $user->getFriendCode();

            $memberDto = new GroupReadMemberDTO();
            $memberDto->id = $roomUser->getId();
            $memberDto->member = $userDto;
            $memberDto->isVisible = $roomUser->isVisible();
            $memberDto->role = strtolower($roomUser->getRole()->name);

            $dto->members[] = $memberDto;
        }

        return $dto;
    }
}
