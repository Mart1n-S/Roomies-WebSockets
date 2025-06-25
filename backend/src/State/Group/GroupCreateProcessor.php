<?php

namespace App\State\Group;

use App\Entity\User;
use App\Mapper\GroupMapper;
use App\Enum\FriendshipStatus;
use App\Dto\Group\GroupReadDTO;
use App\Dto\Group\GroupCreateDTO;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use ApiPlatform\Metadata\Operation;
use App\Service\RoomFactoryService;
use App\Repository\FriendshipRepository;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GroupCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private UserRepository $userRepository,
        private RoomRepository $roomRepository,
        private FriendshipRepository $friendshipRepository,
        private RoomFactoryService $roomFactoryService,
        private GroupMapper $groupMapper
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): GroupReadDTO
    {
        /** @var User|null $creator */
        $creator = $this->tokenStorage->getToken()?->getUser();

        if (!$creator instanceof User) {
            throw new \RuntimeException('Utilisateur non authentifié.');
        }

        /** @var GroupCreateDTO $data */
        $uniqueUsers = [];
        $invalidCodes = [];

        foreach ($data->members as $code) {
            if (isset($uniqueUsers[$code])) {
                continue; // doublon déjà traité
            }

            $user = $this->userRepository->findOneBy(['friendCode' => $code]);

            if (!$user || $user === $creator) {
                $invalidCodes[] = $code;
                continue;
            }

            $friendship = $this->friendshipRepository->findFriendshipBetween($creator, $user);

            if (!$friendship || $friendship->getStatus() !== FriendshipStatus::Friend) {
                $invalidCodes[] = $code;
                continue;
            }

            $uniqueUsers[$code] = $user;
        }

        $users = array_values($uniqueUsers);

        if (!empty($invalidCodes)) {
            throw new BadRequestHttpException(
                'Les utilisateurs suivants ne sont pas valides ou ne sont pas vos amis : ' . implode(', ', $invalidCodes)
            );
        }

        if (count($users) < 2) {
            throw new BadRequestHttpException('Vous devez inviter au moins deux amis pour créer un groupe.');
        }

        $room = $this->roomFactoryService->createRoom(true, $creator, $users, $data->name);

        return $this->groupMapper->toReadDto($room);
    }
}
