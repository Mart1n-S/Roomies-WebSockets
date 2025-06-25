<?php

namespace App\State\Friendship;

use App\Entity\User;
use App\Entity\Friendship;
use App\Repository\RoomRepository;
use ApiPlatform\Metadata\Operation;
use App\Repository\MessageRepository;
use App\Repository\RoomUserRepository;
use App\Repository\FriendshipRepository;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FriendshipDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private FriendshipRepository $friendshipRepository,
        private RoomRepository $roomRepository,
        private RoomUserRepository $roomUserRepository,
        private MessageRepository $messageRepository
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('Utilisateur non authentifié.');
        }

        /** @var Friendship|null $previous */
        $previous = $context['previous_data'] ?? null;

        if (!$previous instanceof Friendship) {
            throw new NotFoundHttpException('Relation introuvable.');
        }

        /** @var Friendship|null $friendship */
        $friendship = $this->friendshipRepository->find($previous->getId());

        if (!$friendship || ($friendship->getApplicant() !== $user && $friendship->getRecipient() !== $user)) {
            throw new AccessDeniedHttpException('Vous ne pouvez pas supprimer cette relation.');
        }

        // Récupération des utilisateurs
        $userA = $friendship->getApplicant();
        $userB = $friendship->getRecipient();

        $room = $this->roomRepository->findPrivateRoomBetweenUsers($userA, $userB);

        if ($room) {
            $this->roomRepository->remove($room, true);
        }

        // Suppression de la relation d’amitié
        $this->friendshipRepository->remove($friendship, true);

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
