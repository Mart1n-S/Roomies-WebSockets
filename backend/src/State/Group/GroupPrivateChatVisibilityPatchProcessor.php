<?php

namespace App\State\Group;

use App\Entity\User;
use ApiPlatform\Metadata\Operation;
use App\Repository\RoomUserRepository;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Dto\Group\GroupPrivateChatVisibilityPatchDTO;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Processor pour modifier la visibilité d’une discussion privée côté utilisateur.
 *
 * Permet à un utilisateur de masquer ou ré-afficher une discussion privée
 * (pas applicable sur les groupes).
 */
class GroupPrivateChatVisibilityPatchProcessor implements ProcessorInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private RoomUserRepository $roomUserRepository
    ) {}

    /**
     * Traite la modification de la visibilité d'une discussion privée (roomUser.isVisible).
     *
     * @param mixed $data                   Doit être une instance de GroupPrivateChatVisibilityPatchDTO
     * @param Operation $operation
     * @param array $uriVariables           Doit contenir l'identifiant de roomUser ('id')
     * @param array $context
     * @return JsonResponse                 Message de confirmation
     * @throws AccessDeniedHttpException|NotFoundHttpException|BadRequestHttpException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        /** @var User|null $user Utilisateur courant */
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('Non authentifié');
        }

        // Récupère le lien utilisateur-room
        $roomUserId = $uriVariables['id'];
        $roomUser = $this->roomUserRepository->findOneBy(['user' => $user, 'room' => $roomUserId]);

        if (!$roomUser) {
            throw new NotFoundHttpException('Association utilisateur-room introuvable');
        }

        // Vérifie que l'utilisateur ne modifie que sa propre discussion privée
        if ($roomUser->getUser() !== $user) {
            throw new AccessDeniedHttpException('Vous ne pouvez modifier que vos propres discussions privées');
        }

        // Impossible de modifier la visibilité sur un groupe (seulement discussion privée)
        if ($roomUser->getRoom()->isGroup()) {
            throw new BadRequestHttpException('Impossible de modifier la visibilité d’un groupe');
        }

        // Vérifie le format du DTO attendu
        if (!$data instanceof GroupPrivateChatVisibilityPatchDTO) {
            throw new \InvalidArgumentException('Données invalides');
        }

        // Met à jour la visibilité et sauvegarde
        $roomUser->setIsVisible($data->isVisible);
        $this->roomUserRepository->save($roomUser, true);

        return new JsonResponse(['message' => 'Visibilité mise à jour'], JsonResponse::HTTP_OK);
    }
}
