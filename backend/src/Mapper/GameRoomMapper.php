<?php

namespace App\Mapper;

use App\Entity\User;
use App\Entity\GameRoom;
use App\Dto\User\UserReadDTO;
use App\Dto\GameRoom\ReadGameRoomDTO;
use App\Service\AvatarUrlGeneratorService;

class GameRoomMapper
{
    public function __construct(
        private AvatarUrlGeneratorService $avatarUrlGeneratorService
    ) {}

    /**
     * Transforme une game room en ReadGameRoomDTO.
     *
     * @param GameRoom $gameRoom
     * @return ReadGameRoomDTO
     */
    public function toReadDto(GameRoom $gameRoom): ReadGameRoomDTO
    {

        $dto = new ReadGameRoomDTO();
        $dto->id = $gameRoom->getId();
        $dto->name = $gameRoom->getName();
        $dto->game = $gameRoom->getGame()->value;
        $dto->creator = $this->mapUser($gameRoom->getCreator());
        $dto->createdAt = $gameRoom->getCreatedAt();

        return $dto;
    }

    public function mapUser(User $user): UserReadDTO
    {
        $dto = new UserReadDTO();
        $dto->pseudo = $user->getPseudo();

        $dto->avatar = $this->avatarUrlGeneratorService->generate($user);

        $dto->friendCode = $user->getFriendCode();

        return $dto;
    }
}
