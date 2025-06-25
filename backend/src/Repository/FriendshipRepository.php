<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Friendship;
use App\Enum\FriendshipStatus;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Friendship>
 */
class FriendshipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Friendship::class);
    }

    public function save(Friendship $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Friendship $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Récupère tous les amis confirmés d'un utilisateur.
     *
     * @param User $user
     * @return Friendship[]
     */
    public function findConfirmedFriendshipsForUser(User $user): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.status = :status')
            ->andWhere('f.applicant = :user OR f.recipient = :user')
            ->setParameter('status', FriendshipStatus::Friend)
            ->setParameter('user', $user->getId()->toBinary())
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère toutes les demandes d’amis reçues par l’utilisateur connecté
     * qui sont encore en attente (statut "pending").
     *
     * @param User $user L’utilisateur connecté
     * @return Friendship[] Liste des demandes d’amis en attente
     */
    public function findPendingRequestsForUser(User $user): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.recipient = :user')
            ->andWhere('f.status = :status')
            ->setParameter('user', $user->getId()->toBinary())
            ->setParameter('status', FriendshipStatus::Pending)
            ->getQuery()
            ->getResult();
    }


    /**
     * Récupère toutes les demandes d’amis envoyées par l’utilisateur et en attente.
     *
     * @param User $user
     * @return Friendship[]
     */
    public function findPendingSentRequests(User $user): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.applicant = :user')
            ->andWhere('f.status = :status')
            ->setParameter('user', $user->getId()->toBinary())
            ->setParameter('status', FriendshipStatus::Pending)
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère une relation entre deux utilisateurs, peu importe l'ordre.
     */
    public function findFriendshipBetween(User $a, User $b): ?Friendship
    {
        return $this->createQueryBuilder('f')
            ->where('(f.applicant = :a AND f.recipient = :b) OR (f.applicant = :b AND f.recipient = :a)')
            ->setParameter('a', $a->getId()->toBinary())
            ->setParameter('b', $b->getId()->toBinary())
            ->getQuery()
            ->getOneOrNullResult();
    }
}
