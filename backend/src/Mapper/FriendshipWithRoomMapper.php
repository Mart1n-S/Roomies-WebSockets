<?php

namespace App\Mapper;

use App\Entity\User;
use App\Entity\Friendship;
use App\Mapper\GroupMapper;
use App\Dto\Friendship\FriendshipWithRoomReadDTO;
use App\Repository\RoomRepository;

class FriendshipWithRoomMapper
{
    public function __construct(
        private FriendshipMapper $friendshipMapper,
        private GroupMapper $groupMapper,
        private RoomRepository $roomRepository
    ) {}

    public function toReadDto(Friendship $friendship, User $currentUser): FriendshipWithRoomReadDTO
    {
        $dto = new FriendshipWithRoomReadDTO();

        // Infos de la friendship
        $baseDto = $this->friendshipMapper->toReadDto($friendship, $currentUser);
        $dto->id = $baseDto->id;
        $dto->status = $baseDto->status;
        $dto->createdAt = $baseDto->createdAt;
        $dto->friend = $baseDto->friend;

        // Récupère la room créée
        $room = $this->roomRepository->findPrivateRoomBetweenUsers(
            $friendship->getApplicant(),
            $friendship->getRecipient()
        );

        if (!$room) {
            throw new \LogicException('Room privée non trouvée après création.');
        }
        $roomDto = $this->groupMapper->toReadDto($room, $currentUser);

        if (!$roomDto->isGroup) {
            $other = $friendship->getApplicant() === $currentUser
                ? $friendship->getRecipient()
                : $friendship->getApplicant();

            $roomDto->name = $other->getPseudo() ?? 'Discussion privée';
        }

        $dto->room = $roomDto;

        return $dto;
    }
}
