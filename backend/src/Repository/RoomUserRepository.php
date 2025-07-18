<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\RoomUser;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Uid\Uuid;

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

    /**
     * Trouve un RoomUser par l'utilisateur et l'ID de la room.
     *
     * @param User $user
     * @param string $roomId
     * @return RoomUser|null
     */
    public function findOneByUserAndRoomId(User $user, string $roomId): ?RoomUser
    {
        return $this->createQueryBuilder('ru')
            ->join('ru.room', 'r')
            ->where('r.id = :roomId')
            ->andWhere('ru.user = :user')
            ->setParameter('roomId', Uuid::fromString($roomId)->toBinary())
            ->setParameter('user', $user->getId()->toBinary())
            ->getQuery()
            ->getOneOrNullResult();
    }
}
