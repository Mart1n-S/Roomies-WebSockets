<?php

namespace App\State\Group;

use App\Entity\User;
use App\Mapper\GroupMapper;
use App\Enum\FriendshipStatus;
use App\Dto\Group\GroupReadDTO;
use App\Dto\Group\GroupCreateDTO;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use ApiPlatform\Metadata\Operation;
use App\Service\RoomFactoryService;
use App\Repository\FriendshipRepository;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Processor pour la création d’un groupe (room de type groupe).
 *
 * Vérifie les membres à inviter (uniquement amis valides, pas de doublons, pas soi-même)
 * puis crée le groupe et retourne un DTO à jour.
 */
class GroupCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private UserRepository $userRepository,
        private RoomRepository $roomRepository,
        private FriendshipRepository $friendshipRepository,
        private RoomFactoryService $roomFactoryService,
        private GroupMapper $groupMapper
    ) {}

    /**
     * Traite la création d'un groupe.
     *
     * @param mixed $data        DTO de création (nom, liste des codes amis)
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return GroupReadDTO      DTO du groupe créé
     * @throws BadRequestHttpException si conditions non remplies
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): GroupReadDTO
    {
        /** @var User|null $creator Utilisateur courant (doit être authentifié) */
        $creator = $this->tokenStorage->getToken()?->getUser();

        if (!$creator instanceof User) {
            throw new \RuntimeException('Utilisateur non authentifié.');
        }

        /** @var GroupCreateDTO $data DTO avec nom et membres (friendCodes) */
        $uniqueUsers = [];   // Liste des utilisateurs à inviter (sans doublons)
        $invalidCodes = [];  // Liste des codes amis non valides ou non amis

        // Parcourt chaque code ami fourni pour valider et constituer la liste finale
        foreach ($data->members as $code) {
            if (isset($uniqueUsers[$code])) {
                continue; // doublon déjà traité
            }

            $user = $this->userRepository->findOneBy(['friendCode' => $code]);

            // Vérifie l'existence et que ce n'est pas le créateur lui-même
            if (!$user || $user === $creator) {
                $invalidCodes[] = $code;
                continue;
            }

            // Vérifie que le créateur et ce user sont amis
            $friendship = $this->friendshipRepository->findFriendshipBetween($creator, $user);

            if (!$friendship || $friendship->getStatus() !== FriendshipStatus::Friend) {
                $invalidCodes[] = $code;
                continue;
            }

            // Ajoute à la liste si tout est OK
            $uniqueUsers[$code] = $user;
        }

        $users = array_values($uniqueUsers);

        // Si au moins un membre est invalide, erreur explicite
        if (!empty($invalidCodes)) {
            throw new BadRequestHttpException(
                'Les utilisateurs suivants ne sont pas valides ou ne sont pas vos amis : ' . implode(', ', $invalidCodes)
            );
        }

        // Il faut au moins deux amis pour créer un groupe (en plus du créateur)
        if (count($users) < 2) {
            throw new BadRequestHttpException('Vous devez inviter au moins deux amis pour créer un groupe.');
        }

        // Crée la room/groupe via le service dédié (avec créateur et membres)
        $room = $this->roomFactoryService->createRoom(true, $creator, $users, $data->name);

        // Retourne le DTO prêt à envoyer au frontend
        return $this->groupMapper->toReadDto($room);
    }
}
