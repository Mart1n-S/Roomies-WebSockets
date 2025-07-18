<?php

namespace App\State\WebSocket\Friendship;

use App\Entity\User;
use App\Enum\FriendshipStatus;
use App\Repository\FriendshipRepository;
use App\Service\RoomFactoryService;
use App\Mapper\FriendshipWithRoomMapper;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class FriendshipPatchWebSocketProcessor
{
    public function __construct(
        private readonly FriendshipRepository $friendshipRepository,
        private readonly RoomFactoryService $roomFactoryService,
        private readonly FriendshipWithRoomMapper $friendshipWithRoomMapper,
    ) {}

    /**
     * Accepte ou refuse une demande d'ami via WebSocket.
     *
     * @param User $user L'utilisateur authentifié
     * @param string $friendshipId ID de la relation
     * @param string $action 'accepter' ou 'refuser'
     * @return array|null Retourne friendship + room en cas d’acceptation, sinon null
     */
    public function process(User $user, string $friendshipId, string $action): ?array
    {
        $friendship = $this->friendshipRepository->find($friendshipId);

        if (!$friendship || $friendship->getRecipient() !== $user) {
            throw new AccessDeniedHttpException('Demande d’amis introuvable ou interdite.');
        }

        if ($friendship->getStatus() !== FriendshipStatus::Pending) {
            throw new BadRequestHttpException('Cette demande a déjà été traitée.');
        }

        if (!in_array($action, ['accepter', 'refuser'], true)) {
            throw new BadRequestHttpException("Action non valide. Utilisez 'accepter' ou 'refuser'.");
        }

        if ($action === 'accepter') {
            // 1. Met à jour le statut
            $friendship->setStatus(FriendshipStatus::Friend);
            $this->friendshipRepository->save($friendship, true);

            // 2. Crée la room privée
            $this->roomFactoryService->createRoom(
                false,
                $friendship->getApplicant(),
                [$friendship->getRecipient()]
            );

            // 3. Utilise le mapper enrichi pour inclure la room créée
            $dto = $this->friendshipWithRoomMapper->toReadDto($friendship, $user);

            return [
                'friendship' => $dto,
                'room' => $dto->room
            ];
        }

        // Refus = suppression
        $this->friendshipRepository->remove($friendship, true);
        return [
            'friendship' => $friendship
        ];
    }
}
