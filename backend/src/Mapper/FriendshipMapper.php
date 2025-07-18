<?php

namespace App\Mapper;

use App\Entity\User;
use App\Entity\Friendship;
use App\Dto\User\UserReadDTO;
use App\Dto\Friendship\FriendshipReadDTO;
use App\Service\AvatarUrlGeneratorService;

/**
 * Service de mapping entre Friendship et FriendshipReadDTO.
 */
class FriendshipMapper
{
    public function __construct(
        private AvatarUrlGeneratorService $avatarUrlGeneratorService
    ) {}

    /**
     * Transforme un Friendship en FriendshipReadDTO.
     *
     * @param Friendship $friendship
     * @return FriendshipReadDTO
     */
    public function toReadDto(Friendship $friendship, User $currentUser): FriendshipReadDTO
    {
        $dto = new FriendshipReadDTO();
        $dto->id = $friendship->getId();
        $dto->status = $friendship->getStatus();
        $dto->createdAt = $friendship->getCreatedAt();

        if (!$currentUser instanceof User) {
            throw new \LogicException('Utilisateur non connecté dans le mapper.');
        }

        if ($friendship->getApplicant() === $currentUser) {
            $dto->friend = $this->mapUser($friendship->getRecipient());
        } else {
            $dto->friend = $this->mapUser($friendship->getApplicant());
        }

        return $dto;
    }


    /**
     * Transforme un User en UserReadDTO pour l'inclure dans un FriendshipReadDTO.
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
