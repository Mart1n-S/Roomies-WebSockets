<?php

namespace App\State\Friendship;

use App\Entity\User;
use App\Entity\Friendship;
use App\Mapper\FriendshipMapper;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\FriendshipRepository;
use App\Dto\Friendship\FriendshipReadDTO;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Fournit les relations d’amitié filtrées selon le paramètre "filter" (friends, sent, received).
 * 
 * @implements ProviderInterface<FriendshipReadDTO>
 */
final class FriendshipFilterProvider implements ProviderInterface
{
    public function __construct(
        private FriendshipRepository $repository,
        private TokenStorageInterface $tokenStorage,
        private FriendshipMapper $friendshipMapper
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): iterable
    {
        $token = $this->tokenStorage->getToken();

        if (!$token || !is_object($token->getUser())) {
            throw new AccessDeniedException('Authentification requise.');
        }

        /** @var User $user */
        $user = $token->getUser();

        $filter = $context['filters']['filter'] ?? 'friends';

        /** @var Friendship[] $friendships */
        $friendships = match ($filter) {
            'sent' => $this->repository->findPendingSentRequests($user),
            'received' => $this->repository->findPendingRequestsForUser($user),
            default => $this->repository->findConfirmedFriendshipsForUser($user),
        };

        return array_map(
            fn(Friendship $friendship) => $this->friendshipMapper->toReadDto($friendship, $user),
            $friendships
        );
    }
}
