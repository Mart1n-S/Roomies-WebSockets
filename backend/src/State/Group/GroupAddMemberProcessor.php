<?php

namespace App\State\Group;

use App\Entity\User;
use App\Entity\RoomUser;
use App\Mapper\GroupMapper;
use App\Dto\Group\GroupReadDTO;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use ApiPlatform\Metadata\Operation;
use App\Dto\Group\GroupAddMemberDTO;
use App\Repository\RoomUserRepository;
use App\Repository\FriendshipRepository;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GroupAddMemberProcessor implements ProcessorInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private RoomRepository $roomRepository,
        private RoomUserRepository $roomUserRepository,
        private UserRepository $userRepository,
        private FriendshipRepository $friendshipRepository,
        private GroupMapper $groupMapper
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): GroupReadDTO
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user instanceof User) {
            throw new \RuntimeException('Utilisateur non authentifié.');
        }

        $roomId = $uriVariables['id'] ?? null;
        $room = $this->roomRepository->find($roomId);

        if (!$room || !$room->isGroup()) {
            throw new NotFoundHttpException('Groupe introuvable.');
        }

        // Vérifie que le user est membre du groupe
        if (!$room->getMembers()->exists(fn($key, $member) => $member->getUser() === $user)) {
            throw new BadRequestHttpException('Vous ne faites pas partie de ce groupe.');
        }

        /** @var GroupAddMemberDTO $data */
        $friend = $this->userRepository->findOneBy(['friendCode' => $data->friendCode]);

        if (!$friend) {
            throw new BadRequestHttpException('Utilisateur avec ce code ami introuvable.');
        }

        // Vérifie si déjà membre
        if ($room->getMembers()->exists(fn($key, $member) => $member->getUser() === $friend)) {
            throw new BadRequestHttpException('Cet utilisateur est déjà membre du groupe.');
        }

        // Vérifie la relation d’amitié
        $friendship = $this->friendshipRepository->findFriendshipBetween($user, $friend);
        if (!$friendship || $friendship->getStatus()->value !== 'friend') {
            throw new BadRequestHttpException('Vous devez être ami avec cet utilisateur pour l’ajouter.');
        }

        // Ajout du membre
        $roomUser = new RoomUser();
        $roomUser->setRoom($room);
        $roomUser->setUser($friend);

        $this->roomUserRepository->save($roomUser, true);

        $room->addMember($roomUser);

        return $this->groupMapper->toReadDto($room);
    }
}
