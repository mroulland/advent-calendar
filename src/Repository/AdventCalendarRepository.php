<?php

namespace App\Repository;

use App\Entity\AdventCalendar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AdventCalendar>
 */
class AdventCalendarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdventCalendar::class);
    }

    public function findForDisplay(): AdventCalendar
    {
        // On récupère l’année actuelle
        $year = (int) (new \DateTime())->format('Y');

        // 1) On essaie de trouver celui de l’année en cours
        $current = $this->findOneBy(['year' => $year]);
        if ($current) {
            return $current;
        }

        // 2) Sinon on prend le dernier existant
        return $this->createQueryBuilder('a')
            ->orderBy('a.year', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
    

    //    /**
    //     * @return AdventCalendar[] Returns an array of AdventCalendar objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?AdventCalendar
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
