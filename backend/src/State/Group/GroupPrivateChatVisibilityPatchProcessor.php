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

class GroupPrivateChatVisibilityPatchProcessor implements ProcessorInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private RoomUserRepository $roomUserRepository
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        /** @var User|null $user */
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('Non authentifié');
        }

        $roomUserId = $uriVariables['id'];
        $roomUser = $this->roomUserRepository->findOneBy(['user' => $user, 'room' => $roomUserId]);

        if (!$roomUser) {
            throw new NotFoundHttpException('Association utilisateur-room introuvable');
        }

        if ($roomUser->getUser() !== $user) {
            throw new AccessDeniedHttpException('Vous ne pouvez modifier que vos propres discussions privées');
        }

        if ($roomUser->getRoom()->isGroup()) {
            throw new BadRequestHttpException('Impossible de modifier la visibilité d’un groupe');
        }

        if (!$data instanceof GroupPrivateChatVisibilityPatchDTO) {
            throw new \InvalidArgumentException('Données invalides');
        }

        $roomUser->setIsVisible($data->isVisible);
        $this->roomUserRepository->save($roomUser, true);

        return new JsonResponse(['message' => 'Visibilité mise à jour'], JsonResponse::HTTP_OK);
    }
}
