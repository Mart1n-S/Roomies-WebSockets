<?php

namespace App\State\Friendship;

use App\Entity\User;
use App\Entity\Friendship;
use App\Enum\FriendshipStatus;
use App\Mapper\FriendshipMapper;
use App\Repository\UserRepository;
use ApiPlatform\Metadata\Operation;
use App\Repository\FriendshipRepository;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\Friendship\FriendshipReadDTO;
use App\Dto\Friendship\FriendshipCreateDTO;
use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @implements ProcessorInterface<FriendshipCreateDTO, FriendshipReadDTO>
 */
final class FriendshipCreateProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: PersistProcessor::class)]
        private ProcessorInterface $persistProcessor,
        private UserRepository $userRepository,
        private FriendshipRepository $friendshipRepository,
        private TokenStorageInterface $tokenStorage,
        private FriendshipMapper $friendshipMapper
    ) {}

    /**
     * Gère la création d’une demande d’amis à partir d’un code ami.
     * 
     * Cas traités :
     * - Le code n'existe pas
     * - Le code est le sien
     * - Déjà amis
     * - Demande déjà envoyée ou reçue
     *
     * @param FriendshipCreateDTO $data Données du formulaire (code ami)
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return FriendshipReadDTO
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): FriendshipReadDTO
    {
        $token = $this->tokenStorage->getToken();

        if (!$token || !is_object($token->getUser())) {
            throw new AccessDeniedException('Authentification requise.');
        }

        /** @var User $user */
        $user = $token->getUser();

        $recipient = $this->userRepository->findOneBy(['friendCode' => $data->friendCode]);

        if (!$recipient) {
            throw new BadRequestHttpException('Utilisateur introuvable.');
        }

        if ($recipient === $user) {
            throw new BadRequestHttpException('Vous ne pouvez pas vous ajouter vous-même.');
        }

        $existing = $this->friendshipRepository->findFriendshipBetween($user, $recipient);

        if ($existing && $existing->getStatus() === FriendshipStatus::Friend) {
            throw new BadRequestHttpException('Vous êtes déjà amis avec cet utilisateur.');
        }

        if ($existing && $existing->getApplicant() === $user && $existing->getStatus() === FriendshipStatus::Pending) {
            throw new BadRequestHttpException('Une demande d’amis est déjà en attente pour cet utilisateur.');
        }

        if ($existing && $existing->getRecipient() === $user && $existing->getStatus() === FriendshipStatus::Pending) {
            throw new BadRequestHttpException('Cet utilisateur vous a déjà envoyé une demande d’amis.');
        }

        $friendship = new Friendship();
        $friendship->setApplicant($user);
        $friendship->setRecipient($recipient);
        $friendship->setStatus(FriendshipStatus::Pending);


        // Persistance
        $this->persistProcessor->process($friendship, $operation, $uriVariables, $context);

        return $this->friendshipMapper->toReadDto($friendship);
    }
}
