<?php

namespace App\State\Group;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Repository\RoomRepository;
use App\Repository\RoomUserRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Processor pour mettre à jour la date de dernière visite d'un membre dans un groupe.
 *
 * Permet de sauvegarder la dernière date de consultation d'un groupe par l'utilisateur,
 * afin de gérer les notifications ou l'affichage des nouveaux messages.
 */
final class GroupLastSeenPatchProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly RoomRepository $roomRepository,
        private readonly RoomUserRepository $roomUserRepository,
        private readonly TokenStorageInterface $tokenStorage
    ) {}

    /**
     * Met à jour la propriété "lastSeenAt" de l'utilisateur dans le groupe (room).
     *
     * @param mixed $data             Objet contenant la nouvelle date (lastSeenAt)
     * @param Operation $operation
     * @param array $uriVariables     Doit contenir l'id du groupe ('id')
     * @param array $context
     * @return array                  Message de confirmation
     * @throws \RuntimeException|\InvalidArgumentException si erreurs
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        // Récupère l'utilisateur courant
        $token = $this->tokenStorage->getToken();
        $user = $token?->getUser();

        if (!$user instanceof UserInterface) {
            throw new \RuntimeException('Utilisateur non authentifié');
        }

        // Vérifie que l'ID de groupe est bien fourni
        $roomId = $uriVariables['id'] ?? null;
        if (!$roomId) {
            throw new \InvalidArgumentException('ID de groupe requis');
        }

        // Recherche la room (groupe) correspondante
        $room = $this->roomRepository->find($roomId);
        if (!$room) {
            throw new \RuntimeException('Room introuvable');
        }

        // Recherche l'entrée RoomUser (lien utilisateur <-> groupe)
        $roomUser = $this->roomUserRepository->findOneBy([
            'room' => $room,
            'user' => $user
        ]);

        if (!$roomUser) {
            throw new \RuntimeException('Vous ne faites pas partie de ce groupe');
        }

        // Met à jour la date de dernière visite (ou maintenant si non fournie)
        $roomUser->setLastSeenAt($data->lastSeenAt ?? new \DateTimeImmutable());
        $this->roomUserRepository->save($roomUser, true);

        // Retourne un message de succès
        return ['message' => 'Dernière visite mise à jour'];
    }
}
