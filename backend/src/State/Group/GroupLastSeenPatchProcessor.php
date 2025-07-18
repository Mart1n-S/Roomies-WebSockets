<?php

namespace App\State\Group;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\RoomRepository;
use App\Repository\RoomUserRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

// TODO: A supprimer
final class GroupLastSeenPatchProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly RoomRepository $roomRepository,
        private readonly RoomUserRepository $roomUserRepository,
        private readonly TokenStorageInterface $tokenStorage
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        $token = $this->tokenStorage->getToken();
        $user = $token?->getUser();

        if (!$user instanceof UserInterface) {
            throw new \RuntimeException('Utilisateur non authentifié');
        }

        $roomId = $uriVariables['id'] ?? null;
        if (!$roomId) {
            throw new \InvalidArgumentException('ID de groupe requis');
        }

        $room = $this->roomRepository->find($roomId);
        if (!$room) {
            throw new \RuntimeException('Room introuvable');
        }

        $roomUser = $this->roomUserRepository->findOneBy([
            'room' => $room,
            'user' => $user
        ]);

        if (!$roomUser) {
            throw new \RuntimeException('Vous ne faites pas partie de ce groupe');
        }

        $roomUser->setLastSeenAt($data->lastSeenAt ?? new \DateTimeImmutable());
        $this->roomUserRepository->save($roomUser, true);

        return ['message' => 'Dernière visite mise à jour'];
    }
}
