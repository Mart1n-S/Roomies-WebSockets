<?php

namespace App\Mapper;

use App\Entity\Room;
use App\Dto\User\UserReadDTO;
use App\Dto\Group\GroupReadDTO;
use App\Dto\Group\GroupReadMemberDTO;
use Symfony\Component\HttpFoundation\RequestStack;

class GroupMapper
{
    public function __construct(
        private RequestStack $requestStack,
        private string $avatarPublicPath
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
            $request = $this->requestStack->getCurrentRequest();
            $baseUrl = $request ? $request->getSchemeAndHttpHost() : '';

            $user = $roomUser->getUser();

            $userDto = new UserReadDTO();
            $userDto->pseudo = $user->getPseudo();

            $avatar = $user->getImageName() ?: 'default-avatar.png';
            $userDto->avatar = rtrim($baseUrl . $this->avatarPublicPath, '/') . '/' . $avatar;

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
