<?php

namespace App\State\Group;

use App\Entity\User;
use App\Entity\RoomUser;
use App\Mapper\GroupMapper;
use App\Dto\Group\GroupReadDTO;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use ApiPlatform\Metadata\Operation;
use App\Dto\Group\GroupAddMemberDTO;
use App\Repository\RoomUserRepository;
use App\Repository\FriendshipRepository;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Processor pour l'ajout d'un membre à un groupe (room de type groupe).
 *
 * Vérifie toutes les conditions métier (authentification, appartenance au groupe, amitié),
 * puis ajoute le membre et retourne l'état à jour du groupe.
 */
class GroupAddMemberProcessor implements ProcessorInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private RoomRepository $roomRepository,
        private RoomUserRepository $roomUserRepository,
        private UserRepository $userRepository,
        private FriendshipRepository $friendshipRepository,
        private GroupMapper $groupMapper
    ) {}

    /**
     * Traite l'ajout d'un nouveau membre dans un groupe existant.
     *
     * @param mixed $data                DTO contenant le code ami à ajouter
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return GroupReadDTO              DTO du groupe mis à jour
     * @throws \RuntimeException|NotFoundHttpException|BadRequestHttpException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): GroupReadDTO
    {
        /** @var User $user Utilisateur courant (doit être authentifié) */
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user instanceof User) {
            throw new \RuntimeException('Utilisateur non authentifié.');
        }

        // Récupère la room par son ID et vérifie que c'est bien un groupe
        $roomId = $uriVariables['id'] ?? null;
        $room = $this->roomRepository->find($roomId);

        if (!$room || !$room->isGroup()) {
            throw new NotFoundHttpException('Groupe introuvable.');
        }

        // Vérifie que l'utilisateur courant est membre du groupe
        if (!$room->getMembers()->exists(fn($key, $member) => $member->getUser() === $user)) {
            throw new BadRequestHttpException('Vous ne faites pas partie de ce groupe.');
        }

        /** @var GroupAddMemberDTO $data DTO avec le code ami à ajouter */
        $friend = $this->userRepository->findOneBy(['friendCode' => $data->friendCode]);

        if (!$friend) {
            throw new BadRequestHttpException('Utilisateur avec ce code ami introuvable.');
        }

        // Vérifie si le nouvel utilisateur est déjà membre du groupe
        if ($room->getMembers()->exists(fn($key, $member) => $member->getUser() === $friend)) {
            throw new BadRequestHttpException('Cet utilisateur est déjà membre du groupe.');
        }

        // Vérifie que l'utilisateur courant est bien ami avec la personne à ajouter
        $friendship = $this->friendshipRepository->findFriendshipBetween($user, $friend);
        if (!$friendship || $friendship->getStatus()->value !== 'friend') {
            throw new BadRequestHttpException('Vous devez être ami avec cet utilisateur pour l’ajouter.');
        }

        // Création et sauvegarde du nouveau membre
        $roomUser = new RoomUser();
        $roomUser->setRoom($room);
        $roomUser->setUser($friend);

        $this->roomUserRepository->save($roomUser, true);

        // Ajout au groupe en mémoire (dans l'entité)
        $room->addMember($roomUser);

        // Retourne un DTO à jour du groupe (avec nouveau membre)
        return $this->groupMapper->toReadDto($room);
    }
}
