<?php

namespace App\Repository;

// use App\Entity\User;
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
            ->setParameter('room', $roomUser->getRoom())
            ->setParameter('user', $roomUser->getUser())
            ->setParameter('lastSeenAt', $roomUser->getLastSeenAt() ?? new \DateTimeImmutable('1970-01-01'))
            ->getQuery()
            ->getSingleScalarResult();
    }
    // TODO: Garder car si onsupprime la room directement, on supprime les messages mais dans le doute on garde cette méthode
    // /**
    //  * TODO: A tester pour voir si cela supprime bien tous les messages entre deux utilisateurs
    //  * Supprime tous les messages entre deux utilisateurs.
    //  *
    //  * @param User $userA Le premier utilisateur
    //  * @param User $userB Le deuxième utilisateur
    //  */
    // public function deleteMessagesBetweenUsers(User $a, User $b): void
    // {
    //     $this->createQueryBuilder('m')
    //         ->delete()
    //         ->where('(m.sender = :a AND m.recipient = :b) OR (m.sender = :b AND m.recipient = :a)')
    //         ->setParameter('a',  $a->getId()->toBinary())
    //         ->setParameter('b', $b->getId()->toBinary())
    //         ->getQuery()
    //         ->execute();
    // }
}
