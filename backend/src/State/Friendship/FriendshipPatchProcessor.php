<?php

namespace App\State\Friendship;

use App\Entity\User;
use App\Entity\Friendship;
use App\Enum\FriendshipStatus;
use App\Mapper\FriendshipMapper;
use ApiPlatform\Metadata\Operation;
use App\Service\RoomFactoryService;
use App\Repository\FriendshipRepository;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Friendship\FriendshipPatchDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @implements ProcessorInterface<FriendshipPatchDTO, Friendship>
 */
final class FriendshipPatchProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: PersistProcessor::class)]
        private ProcessorInterface $persistProcessor,
        private TokenStorageInterface $tokenStorage,
        private FriendshipRepository $friendshipRepository,
        private NormalizerInterface $normalizer,
        private FriendshipMapper $friendshipTransformer,
        private RoomFactoryService $roomFactoryService,
    ) {}

    /**
     * Traite la réponse à une demande d'ami (acceptation ou rejet).
     *
     * @param FriendshipPatchDTO $data Données de la requête
     * @param Operation $operation Métadonnées de l'opération API Platform
     * @param array $uriVariables Variables d'URI (non utilisées ici)
     * @param array $context Contexte d'exécution
     * @return JsonResponse
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('Utilisateur non authentifié.');
        }

        $previous = $context['previous_data'] ?? null;

        if (!$previous instanceof Friendship) {
            throw new \RuntimeException('Demande invalide.');
        }

        /** @var Friendship|null $friendship */
        $friendship = $this->friendshipRepository->find($previous->getId());

        if (!$friendship || $friendship->getRecipient() !== $user) {
            throw new AccessDeniedHttpException('Vous ne pouvez pas modifier cette demande.');
        }

        if (!in_array($friendship->getStatus(), [FriendshipStatus::Pending])) {
            throw new BadRequestHttpException('Cette demande a déjà été traitée.');
        }

        if (!in_array($data->action, ['accepter', 'refuser'], true)) {
            throw new BadRequestHttpException("Action non valide. Utilisez 'accepter' ou 'refuser'.");
        }

        if ($data->action === 'accepter') {
            // 1. Mise à jour de la relation
            $friendship->setStatus(FriendshipStatus::Friend);
            $this->friendshipRepository->save($friendship, true);

            // 2. Création de la Room privée
            $this->roomFactoryService->createRoom(false, $friendship->getApplicant(), [$friendship->getRecipient()]);

            return new JsonResponse($this->normalizer->normalize($this->friendshipTransformer->toReadDto($friendship, $user), null, ['groups' => ['read:friendship']]), JsonResponse::HTTP_OK);
        }

        // Si refusé, on supprime l'entité
        $this->friendshipRepository->remove($friendship, true);

        return new JsonResponse([
            'message' => 'Demande d\'ami refusée.'
        ], JsonResponse::HTTP_OK);
    }
}
