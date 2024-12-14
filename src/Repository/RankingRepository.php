<?php

namespace App\Repository;

use App\Entity\Ranking;
use App\Entity\PhotoChallenge;
use Doctrine\DBAL\Portability\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Ranking>
 */
class RankingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ranking::class);
    }


    /**
     * Ranking global for all users with total points, ordered by points
     *
     * @return Ranking[]
     */
    public function findGlobalRanking()
    {
        return $this->createQueryBuilder('r')
        ->select('r, SUM(r.points) AS totalPoints')
        ->join('r.user', 'u')
        ->groupBy('u')
        ->orderBy('totalPoints', 'DESC')
        ->getQuery()
        ->getResult()
    ;
    }

    /**
    * @return Ranking[] Returns an array of Ranking objects
    */
    public function findByChallenge($value): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.challenge = :val')
            ->setParameter('val', $value)
            ->addOrderBy('r.points', 'DESC')
            ->addOrderBy('r.date', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
    * @return Ranking[] Returns an array of Ranking objects
    */
    public function isUserAlreadyDone($user, $challenge): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.challenge = :chal')
            ->setParameter('chal', $challenge)
            ->andWhere('r.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
        ;
    }
}
