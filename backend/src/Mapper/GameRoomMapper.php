<?php

namespace App\Mapper;

use App\Entity\User;
use App\Entity\GameRoom;
use App\Dto\User\UserReadDTO;
use App\Dto\GameRoom\ReadGameRoomDTO;
use App\Service\AvatarUrlGeneratorService;

/**
 * Mapper pour transformer une entité GameRoom en DTO prêt à exposer via l'API.
 *
 * Permet d'assurer un formatage cohérent des données côté frontend,
 * notamment pour l'affichage des rooms de jeu.
 */
class GameRoomMapper
{
    public function __construct(
        private AvatarUrlGeneratorService $avatarUrlGeneratorService
    ) {}

    /**
     * Transforme une GameRoom en ReadGameRoomDTO pour lecture par le frontend.
     *
     * @param GameRoom $gameRoom La room de jeu à transformer
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

    /**
     * Transforme un utilisateur en UserReadDTO, avec URL d'avatar générée.
     *
     * @param User $user
     * @return UserReadDTO
     */
    public function mapUser(User $user): UserReadDTO
    {
        $dto = new UserReadDTO();
        $dto->pseudo = $user->getPseudo();
        $dto->avatar = $this->avatarUrlGeneratorService->generate($user);
        $dto->friendCode = $user->getFriendCode();

        return $dto;
    }
}
