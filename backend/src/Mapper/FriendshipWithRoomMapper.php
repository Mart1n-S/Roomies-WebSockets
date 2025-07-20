<?php

namespace App\Mapper;

use App\Entity\User;
use App\Entity\Friendship;
use App\Dto\Friendship\FriendshipWithRoomReadDTO;
use App\Repository\RoomRepository;

/**
 * Mapper pour combiner une Friendship et la Room privée associée dans un DTO enrichi.
 *
 * Utilisé pour retourner à la fois les infos d'amitié et la room privée créée
 * lors de l'acceptation d'une demande d'ami.
 */
class FriendshipWithRoomMapper
{
    public function __construct(
        private FriendshipMapper $friendshipMapper,
        private GroupMapper $groupMapper,
        private RoomRepository $roomRepository
    ) {}

    /**
     * Convertit une entité Friendship et la Room liée en un DTO enrichi.
     *
     * @param Friendship $friendship  L'amitié concernée
     * @param User $currentUser       L'utilisateur courant (pour le mapping contextuel)
     * @return FriendshipWithRoomReadDTO
     */
    public function toReadDto(Friendship $friendship, User $currentUser): FriendshipWithRoomReadDTO
    {
        $dto = new FriendshipWithRoomReadDTO();

        // Infos de la friendship (statut, date, ami, etc.)
        $baseDto = $this->friendshipMapper->toReadDto($friendship, $currentUser);
        $dto->id = $baseDto->id;
        $dto->status = $baseDto->status;
        $dto->createdAt = $baseDto->createdAt;
        $dto->friend = $baseDto->friend;

        // Recherche la room privée associée à cette friendship
        $room = $this->roomRepository->findPrivateRoomBetweenUsers(
            $friendship->getApplicant(),
            $friendship->getRecipient()
        );

        // Sécurité : une room doit toujours exister après création d'une amitié
        if (!$room) {
            throw new \LogicException('Room privée non trouvée après création.');
        }

        // Mappe la Room en DTO (même méthode que pour les groupes)
        $roomDto = $this->groupMapper->toReadDto($room, $currentUser);

        // Si ce n'est pas un groupe, personnalise le nom (pseudo de l'autre user)
        if (!$roomDto->isGroup) {
            $other = $friendship->getApplicant() === $currentUser
                ? $friendship->getRecipient()
                : $friendship->getApplicant();

            $roomDto->name = $other->getPseudo() ?? 'Discussion privée';
        }

        $dto->room = $roomDto;

        return $dto;
    }
}
