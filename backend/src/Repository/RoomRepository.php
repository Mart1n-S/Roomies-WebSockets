<?php

namespace App\Repository;

use App\Entity\Room;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Room>
 */
class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    public function save(Room $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Room $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Trouve une salle privée entre deux utilisateurs.
     * 
     * @param User $a Le premier utilisateur
     * @param User $b Le deuxième utilisateur
     * @return Room|null La salle privée si elle existe, sinon null
     */
    public function findPrivateRoomBetweenUsers(User $a, User $b): ?Room
    {
        $qb = $this->createQueryBuilder('r')
            ->join('r.members', 'ru')
            ->andWhere('r.isGroup = false')
            ->groupBy('r.id')
            ->having('COUNT(DISTINCT ru.user) = 2')
            ->andHaving('SUM(CASE WHEN ru.user = :a OR ru.user = :b THEN 1 ELSE 0 END) = 2')
            ->setParameter('a',  $a->getId()->toBinary())
            ->setParameter('b', $b->getId()->toBinary());

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Récupère toutes les discussions privées (isGroup = false) auxquelles appartient l’utilisateur.
     *
     * @param User $user
     * @return Room[]
     */
    public function findPrivateRoomsByUser(User $user): array
    {
        return $this->createQueryBuilder('r')
            ->innerJoin('r.members', 'ru')
            ->where('ru.user = :user')
            ->andWhere('r.isGroup = false')
            ->setParameter('user', $user->getId()->toBinary())
            ->orderBy('r.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
