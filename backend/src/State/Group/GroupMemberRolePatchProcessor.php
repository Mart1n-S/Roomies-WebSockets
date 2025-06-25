<?php

namespace App\State\Group;

use App\Entity\User;
use App\Enum\RoomRole;
use App\Mapper\GroupMapper;
use App\Dto\Group\GroupReadDTO;
use App\Repository\RoomRepository;
use ApiPlatform\Metadata\Operation;
use App\Repository\RoomUserRepository;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GroupMemberRolePatchProcessor implements ProcessorInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private RoomRepository $roomRepository,
        private RoomUserRepository $roomUserRepository,
        private GroupMapper $groupMapper
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): GroupReadDTO
    {
        /** @var User|null $user */
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('Non authentifié');
        }

        $groupId = $uriVariables['groupId'];
        $memberId = $data->memberId;

        $room = $this->roomRepository->find($groupId);
        if (!$room || !$room->isGroup()) {
            throw new BadRequestHttpException('Groupe introuvable.');
        }

        $actorMembership = $this->roomUserRepository->findOneBy([
            'room' => $room,
            'user' => $user,
        ]);

        if (!$actorMembership || $actorMembership->getRole() !== RoomRole::Owner) {
            throw new AccessDeniedHttpException('Seul le propriétaire du groupe peut modifier les rôles des membres.');
        }

        $member = $this->roomUserRepository->find($memberId);
        if (!$member || $member->getRoom()->getId() !== $room->getId()) {
            throw new BadRequestHttpException('Membre introuvable dans ce groupe.');
        }

        if ($member->getUser()->getId() === $user->getId()) {
            throw new BadRequestHttpException('Impossible de modifier votre propre rôle.');
        }

        $newRole = match ($data->role) {
            'admin' => RoomRole::Admin,
            'user'  => RoomRole::User,
            default => throw new BadRequestHttpException('Rôle invalide.')
        };

        $member->setRole($newRole);

        $this->roomUserRepository->save($member, true);

        return $this->groupMapper->toReadDto($room);
    }
}
