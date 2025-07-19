<?php

namespace App\Repository;

use App\Entity\User;
use Symfony\Component\Uid\Uuid;
use App\Entity\Message;
use App\Entity\RoomUser;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function save(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * TODO: A tester pour voir si on récupère bien le nombre de messages non lus pour une room
     * 
     * Compte le nombre de messages non lus pour un utilisateur donné dans une room.
     * Un message est considéré comme lu s’il a été envoyé avant ou au moment du dernier accès à la room,
     * et uniquement s’il n’a pas été envoyé par l’utilisateur lui-même.
     *
     * @param RoomUser $roomUser L’association utilisateur/room avec la date de dernière consultation
     * @return int Nombre de messages non lus
     */
    public function countUnreadForRoomUser(RoomUser $roomUser): int
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.room = :room')
            ->andWhere('m.sender != :user') // pour ne pas compter ses propres messages
            ->andWhere('m.createdAt > :lastSeenAt')
            ->setParameter('room', $roomUser->getRoom()->getId()->toBinary())
            ->setParameter('user', $roomUser->getUser()->getId()->toBinary())
            ->setParameter('lastSeenAt', $roomUser->getLastSeenAt() ?? new \DateTimeImmutable('1970-01-01'))
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Récupère les messages d'une room avec pagination.
     *
     * @param string $roomId L'identifiant de la room
     * @param User $user L'utilisateur pour lequel on récupère les messages
     * @param int $page Le numéro de page (1 par défaut)
     * @param int $limit Le nombre de messages par page (40 par défaut)
     * @return Message[] Les messages paginés
     */
    public function findPaginatedByRoomId(string $roomId, User $user, int $page = 1, int $limit = 100): array
    {
        $qb = $this->createQueryBuilder('m')
            ->innerJoin('m.room', 'r')
            ->innerJoin('r.members', 'ru')
            ->where('r.id = :roomId')
            ->andWhere('ru.user = :user')
            ->orderBy('m.createdAt', 'DESC')
            ->setParameter('roomId', Uuid::fromString($roomId)->toBinary())
            ->setParameter('user', $user->getId()->toBinary())
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }

    /**
     * Compte le nombre total de messages dans une room pour un utilisateur donné.
     *
     * @param string $roomId L'identifiant de la room
     * @param User $user L'utilisateur pour lequel on compte les messages
     * @return int Le nombre total de messages
     */
    public function countMessagesByRoomId(string $roomId, User $user): int
    {
        $qb = $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->innerJoin('m.room', 'r')
            ->innerJoin('r.members', 'ru')
            ->where('r.id = :roomId')
            ->andWhere('ru.user = :user')
            ->setParameter('roomId', Uuid::fromString($roomId)->toBinary())
            ->setParameter('user', $user->getId()->toBinary());

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
