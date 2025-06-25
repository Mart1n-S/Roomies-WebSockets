<?php

namespace App\State\User;

use App\Entity\User;
use App\Mapper\UserMapper;
use App\Dto\User\UserReadDTO;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Fournisseur de données pour l'API de lecture des informations utilisateur.
 *
 * @implements ProviderInterface<UserReadDTO>
 */
final class UserReadProvider implements ProviderInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private NormalizerInterface $normalizer,
        private UserMapper $userMapper,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): JsonResponse
    {
        $token = $this->tokenStorage->getToken();

        if (!$token || !is_object($token->getUser())) {
            throw new AccessDeniedException('Authentification requise.');
        }

        /** @var User $user */
        $user = $token->getUser();

        // Création du DTO
        $dto = $this->userMapper->toReadDto($user);

        return new JsonResponse($this->normalizer->normalize($dto, null, ['groups' => ['read:me']]), JsonResponse::HTTP_OK);
    }
}
