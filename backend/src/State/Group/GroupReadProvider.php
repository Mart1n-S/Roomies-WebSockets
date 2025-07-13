<?php

namespace App\State\Group;

use App\Entity\Room;
use App\Entity\User;
use App\Enum\RoomRole;
use App\Entity\RoomUser;
use App\Mapper\GroupMapper;
use App\Dto\User\UserReadDTO;
use App\Dto\Group\GroupReadDTO;
use App\Repository\RoomRepository;
use ApiPlatform\Metadata\Operation;
use App\Dto\Group\GroupReadMemberDTO;
use ApiPlatform\State\ProviderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Fournisseur de données personnalisé pour récupérer les rooms de l’utilisateur connecté.
 *
 * @implements ProviderInterface<GroupReadDTO[]>
 */
class GroupReadProvider implements ProviderInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private RoomRepository $roomRepository,
        private GroupMapper $groupMapper
    ) {}

    /**
     * @return GroupReadDTO[]
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        /** @var User|null $user */
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('Utilisateur non authentifié.');
        }

        // Récupère toutes les discussions privées de l’utilisateur
        $rooms = $this->roomRepository->findPrivateRoomsByUser($user);

        $result = [];

        foreach ($rooms as $room) {
            $dto = $this->groupMapper->toReadDto($room);

            // Remplace le nom de la discussion privée par le pseudo de l'autre membre
            if (!$room->isGroup()) {
                $otherMember = $room->getMembers()->filter(
                    fn(RoomUser $ru) => $ru->getUser() !== $user
                )->first()?->getUser();

                $dto->name = $otherMember?->getPseudo() ?? 'Discussion privée';
            }

            $result[] = $dto;
        }

        return $result;
    }
}
