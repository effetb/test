<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @return Event[] Returns events created by the given user, starting within the current month
     */
    public function findCurrentMonthByCreator(User $creator): array
    {
        $firstDayOfMonth = new \DateTime('first day of this month midnight');
        $firstDayOfNextMonth = new \DateTime('first day of next month midnight');

        return $this->createQueryBuilder('e')
            ->andWhere('e.creator = :creator')
            ->andWhere('e.startDate >= :from')
            ->andWhere('e.startDate < :to')
            ->setParameter('creator', $creator)
            ->setParameter('from', $firstDayOfMonth)
            ->setParameter('to', $firstDayOfNextMonth)
            ->orderBy('e.startDate', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    //    /**
    //     * @return Event[] Returns an array of Event objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Event
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
