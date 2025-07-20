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

/**
 * Processor pour la suppression d'une relation d'amitié (Friendship).
 *
 * Supprime la relation entre deux utilisateurs et la room privée associée s'il y en a une.
 * S'assure que l'utilisateur a le droit d'effectuer cette suppression.
 */
class FriendshipDeleteProcessor implements ProcessorInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private FriendshipRepository $friendshipRepository,
        private RoomRepository $roomRepository,
        private RoomUserRepository $roomUserRepository,
        private MessageRepository $messageRepository
    ) {}

    /**
     * Traite la suppression de la relation d'amitié, en supprimant également la room privée liée.
     *
     * @param mixed $data                Non utilisé ici (la ressource à supprimer)
     * @param Operation $operation       L'opération API Platform (suppression)
     * @param array $uriVariables
     * @param array $context
     * @return JsonResponse
     * @throws AccessDeniedHttpException
     * @throws NotFoundHttpException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        /** @var User $user Utilisateur courant (authentifié via token) */
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('Utilisateur non authentifié.');
        }

        /** @var Friendship|null $previous Friendship à supprimer (récupérée via le contexte) */
        $previous = $context['previous_data'] ?? null;

        if (!$previous instanceof Friendship) {
            throw new NotFoundHttpException('Relation introuvable.');
        }

        /** @var Friendship|null $friendship Chargée depuis la base pour vérification de l'existence */
        $friendship = $this->friendshipRepository->find($previous->getId());

        // Vérifie que l'utilisateur courant est bien impliqué dans la relation
        if (!$friendship || ($friendship->getApplicant() !== $user && $friendship->getRecipient() !== $user)) {
            throw new AccessDeniedHttpException('Vous ne pouvez pas supprimer cette relation.');
        }

        // Récupère les deux utilisateurs concernés par la relation
        $userA = $friendship->getApplicant();
        $userB = $friendship->getRecipient();

        // Recherche la room privée associée à cette amitié (si elle existe)
        $room = $this->roomRepository->findPrivateRoomBetweenUsers($userA, $userB);

        if ($room) {
            // Supprime la room privée de la base
            $this->roomRepository->remove($room, true);
        }

        // Supprime la relation d’amitié de la base
        $this->friendshipRepository->remove($friendship, true);

        // Retourne une réponse HTTP 204 No Content
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
