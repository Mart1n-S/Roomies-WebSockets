<?php

namespace App\State\Group;

use App\Entity\User;
use App\Entity\RoomUser;
use App\Mapper\GroupMapper;
use App\Dto\Group\GroupReadDTO;
use App\Repository\RoomRepository;
use ApiPlatform\Metadata\Operation;
use App\Repository\MessageRepository;
use ApiPlatform\State\ProviderInterface;
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
        private GroupMapper $groupMapper,
        private MessageRepository $messageRepository
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

            // Récupération du RoomUser correspondant à l'utilisateur connecté
            $roomUser = $room->getMembers()->filter(
                fn(RoomUser $ru) => $ru->getUser() === $user
            )->first();

            if ($roomUser) {
                $unreadCount = $this->messageRepository->countUnreadForRoomUser($roomUser);
                $dto->unreadCount = $unreadCount;
            }

            // Pour les discussions privées, on remplace le nom
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
