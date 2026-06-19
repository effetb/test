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
     * @return Event[]
     */
    public function findForCurrentMonthByCreator(User $creator): array
    {
        $startOfMonth = (new \DateTimeImmutable('first day of this month'))->setTime(0, 0, 0);
        $endOfMonth = (new \DateTimeImmutable('last day of this month'))->setTime(23, 59, 59);

        return $this->createQueryBuilder('e')
            ->andWhere('e.creator = :creator')
            ->andWhere('e.startDate BETWEEN :start AND :end')
            ->setParameter('creator', $creator)
            ->setParameter('start', $startOfMonth)
            ->setParameter('end', $endOfMonth)
            ->orderBy('e.startDate', 'ASC')
            ->getQuery()
            ->getResult();
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
