<?php

namespace App\Mapper;

use App\Entity\User;
use App\Dto\User\UserReadDTO;
use App\Service\AvatarUrlGeneratorService;

/**
 * Mapper pour transformer un User en UserReadDTO.
 */
class UserMapper
{
    public function __construct(
        private AvatarUrlGeneratorService $avatarUrlGenerator,
    ) {}

    /**
     * Transforme un utilisateur en UserReadDTO.
     *
     * @param User $user
     * @return UserReadDTO
     */
    public function toReadDto(User $user): UserReadDTO
    {

        $dto = new UserReadDTO();
        $dto->email = $user->getEmail();
        $dto->pseudo = $user->getPseudo();
        $dto->roles = $user->getRoles();
        $dto->friendCode = $user->getFriendCode();
        $dto->pushNotificationsEnabled = $user->isPushNotificationsEnabled();

        $dto->avatar = $this->avatarUrlGenerator->generate($user);

        return $dto;
    }
}
