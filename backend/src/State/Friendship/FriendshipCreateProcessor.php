<?php

namespace App\State\Friendship;

use App\Entity\User;
use App\Entity\Friendship;
use App\Enum\FriendshipStatus;
use App\Mapper\FriendshipMapper;
use App\Repository\UserRepository;
use App\Repository\FriendshipRepository;
use App\Dto\Friendship\FriendshipReadDTO;
use App\Dto\Friendship\FriendshipCreateDTO;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class FriendshipCreateProcessor
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly FriendshipRepository $friendshipRepository,
        private readonly FriendshipMapper $friendshipMapper
    ) {}

    /**
     * Gère la création d’une demande d’amis à partir d’un code ami.
     *
     * @param FriendshipCreateDTO $data
     * @param User $user
     * @return FriendshipReadDTO
     */
    public function process(FriendshipCreateDTO $data, User $user): FriendshipReadDTO
    {
        $recipient = $this->userRepository->findOneBy(['friendCode' => $data->friendCode]);

        if (!$recipient) {
            throw new BadRequestHttpException('Utilisateur introuvable.');
        }

        if ($recipient === $user) {
            throw new BadRequestHttpException('Vous ne pouvez pas vous ajouter vous-même.');
        }

        $existing = $this->friendshipRepository->findFriendshipBetween($user, $recipient);

        if ($existing && $existing->getStatus() === FriendshipStatus::Friend) {
            throw new BadRequestHttpException('Vous êtes déjà amis avec cet utilisateur.');
        }

        if ($existing && $existing->getApplicant() === $user && $existing->getStatus() === FriendshipStatus::Pending) {
            throw new BadRequestHttpException('Une demande d’amis est déjà en attente pour cet utilisateur.');
        }

        if ($existing && $existing->getRecipient() === $user && $existing->getStatus() === FriendshipStatus::Pending) {
            throw new BadRequestHttpException('Cet utilisateur vous a déjà envoyé une demande d’amis.');
        }

        $friendship = new Friendship();
        $friendship->setApplicant($user);
        $friendship->setRecipient($recipient);
        $friendship->setStatus(FriendshipStatus::Pending);

        $this->friendshipRepository->save($friendship, true);

        return $this->friendshipMapper->toReadDto($friendship);
    }
}
