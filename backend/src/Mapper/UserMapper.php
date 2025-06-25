<?php

namespace App\Mapper;

use App\Entity\User;
use App\Dto\User\UserReadDTO;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Mapper pour transformer un User en UserReadDTO.
 */
class UserMapper
{
    public function __construct(
        private RequestStack $requestStack,
        private string $avatarPublicPath
    ) {}

    /**
     * Transforme un utilisateur en UserReadDTO.
     *
     * @param User $user
     * @return UserReadDTO
     */
    public function toReadDto(User $user): UserReadDTO
    {
        $request = $this->requestStack->getCurrentRequest();
        $baseUrl = $request ? $request->getSchemeAndHttpHost() : '';

        $dto = new UserReadDTO();
        $dto->email = $user->getEmail();
        $dto->pseudo = $user->getPseudo();
        $dto->roles = $user->getRoles();
        $dto->friendCode = $user->getFriendCode();

        $avatarFile = $user->getImageName() ?: 'default-avatar.png';
        $dto->avatar = rtrim($baseUrl . $this->avatarPublicPath, '/') . '/' . $avatarFile;

        return $dto;
    }
}
