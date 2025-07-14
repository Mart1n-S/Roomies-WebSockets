<?php

namespace App\State\Message;

use App\Entity\User;
use App\Mapper\MessageMapper;
use App\Repository\RoomRepository;
use ApiPlatform\Metadata\Operation;
use App\Dto\Message\MessageReadDTO;
use App\Repository\MessageRepository;
use ApiPlatform\State\ProviderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use ApiPlatform\State\Pagination\PaginatorInterface;
use ApiPlatform\State\Pagination\TraversablePaginator;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @implements ProviderInterface<MessageReadDTO[]|PaginatorInterface>
 */
class MessageReadProvider implements ProviderInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private MessageRepository $messageRepository,
        private MessageMapper $messageMapper,
        private RequestStack $requestStack,
        private RoomRepository $roomRepository,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): iterable
    {
        /** @var User|null $user */
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user instanceof User) {
            throw new AccessDeniedHttpException('Utilisateur non authentifié.');
        }

        $request = $this->requestStack->getCurrentRequest();
        $roomId = $request?->query->get('roomId');

        if (!$roomId) {
            throw new \InvalidArgumentException('Le paramètre "roomId" est requis.');
        }

        // Vérifie si le user a bien accès à la room
        $room = $this->roomRepository->findOneByIdAndUser($roomId, $user);
        if (!$room) {
            throw new AccessDeniedHttpException("Vous n’avez pas accès à cette conversation.");
        }

        // Lecture pagination (par défaut 1/40)
        $page = (int) $request->query->get('page', 1);
        $itemsPerPage = (int) $request->query->get('itemsPerPage', 40);

        // Récupération messages
        $messages = $this->messageRepository->findPaginatedByRoomId($roomId, $user, $page, $itemsPerPage);

        // Mapping
        $dtos = array_map(fn($m) => $this->messageMapper->toReadDTO($m), $messages);

        // Nombre total de messages pour cette room
        $total = $this->messageRepository->countMessagesByRoomId($roomId, $user);

        return new TraversablePaginator(
            new \ArrayIterator($dtos),
            $page,
            $itemsPerPage,
            $total
        );
    }
}
