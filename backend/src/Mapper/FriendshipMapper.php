<?php

namespace App\Mapper;

use App\Entity\User;
use App\Entity\Friendship;
use App\Dto\User\UserReadDTO;
use App\Dto\Friendship\FriendshipReadDTO;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Service de mapping entre Friendship et FriendshipReadDTO.
 */
class FriendshipMapper
{
    public function __construct(
        private RequestStack $requestStack,
        private string $avatarPublicPath
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
    private function mapUser(User $user): UserReadDTO
    {
        $request = $this->requestStack->getCurrentRequest();
        $baseUrl = $request ? $request->getSchemeAndHttpHost() : '';

        $dto = new UserReadDTO();
        $dto->pseudo = $user->getPseudo();

        $avatar = $user->getImageName() ?: 'default-avatar.png';
        $dto->avatar = rtrim($baseUrl . $this->avatarPublicPath, '/') . '/' . $avatar;

        $dto->friendCode = $user->getFriendCode();

        return $dto;
    }
}
