<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\RoomUser;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<RoomUser>
 */
class RoomUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoomUser::class);
    }

    public function save(RoomUser $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RoomUser $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Trouve les groupes (rooms de type groupe) auxquels un utilisateur appartient.
     *
     * @param User $user
     * @return RoomUser[]
     */
    public function findGroupsForUser(User $user): array
    {
        return $this->createQueryBuilder('ru')
            ->join('ru.room', 'r')
            ->andWhere('ru.user = :userId')
            ->andWhere('r.isGroup = true')
            ->orderBy('ru.lastSeenAt', 'DESC')
            ->setParameter('userId', $user->getId()->toBinary())
            ->getQuery()
            ->getResult();
    }
}
