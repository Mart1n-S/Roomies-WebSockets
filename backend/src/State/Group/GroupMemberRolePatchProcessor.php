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

/**
 * Processor pour modifier le rôle d’un membre dans un groupe (room de type groupe).
 *
 * Seul le propriétaire du groupe peut changer le rôle des autres membres (admin/user).
 * Empêche toute auto-modification de rôle et vérifie l'appartenance au groupe.
 */
class GroupMemberRolePatchProcessor implements ProcessorInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private RoomRepository $roomRepository,
        private RoomUserRepository $roomUserRepository,
        private GroupMapper $groupMapper
    ) {}

    /**
     * Modifie le rôle d’un membre du groupe.
     *
     * @param mixed $data                Doit contenir 'memberId' et 'role' ('admin' ou 'user')
     * @param Operation $operation
     * @param array $uriVariables        Doit contenir 'groupId'
     * @param array $context
     * @return GroupReadDTO              DTO à jour du groupe
     * @throws AccessDeniedHttpException|BadRequestHttpException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): GroupReadDTO
    {
        /** @var User|null $user Utilisateur courant (doit être owner du groupe) */
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('Non authentifié');
        }

        // Récupère le groupe et l'ID du membre à modifier
        $groupId = $uriVariables['groupId'];
        $memberId = $data->memberId;

        $room = $this->roomRepository->find($groupId);
        if (!$room || !$room->isGroup()) {
            throw new BadRequestHttpException('Groupe introuvable.');
        }

        // Vérifie que l'utilisateur courant est bien owner du groupe
        $actorMembership = $this->roomUserRepository->findOneBy([
            'room' => $room,
            'user' => $user,
        ]);

        if (!$actorMembership || $actorMembership->getRole() !== RoomRole::Owner) {
            throw new AccessDeniedHttpException('Seul le propriétaire du groupe peut modifier les rôles des membres.');
        }

        // Récupère le membre à modifier, s'assure qu'il appartient bien au groupe
        $member = $this->roomUserRepository->find($memberId);
        if (!$member || $member->getRoom()->getId() !== $room->getId()) {
            throw new BadRequestHttpException('Membre introuvable dans ce groupe.');
        }

        // Empêche le propriétaire de changer son propre rôle
        if ($member->getUser()->getId() === $user->getId()) {
            throw new BadRequestHttpException('Impossible de modifier votre propre rôle.');
        }

        // Détermine le nouveau rôle à appliquer
        $newRole = match ($data->role) {
            'admin' => RoomRole::Admin,
            'user'  => RoomRole::User,
            default => throw new BadRequestHttpException('Rôle invalide.')
        };

        // Met à jour et sauvegarde le rôle du membre
        $member->setRole($newRole);

        $this->roomUserRepository->save($member, true);

        // Retourne le DTO du groupe mis à jour
        return $this->groupMapper->toReadDto($room);
    }
}
