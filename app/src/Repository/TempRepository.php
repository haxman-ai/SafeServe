<?php

namespace App\Repository;

use App\Entity\Temp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Temp>
 */
class TempRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Temp::class);
    }

//    /**
//     * @return Temp[] Returns an array of Temp objects
//     */
        public function findTemp(\DateTime $debut, \DateTime $fin): array
    {
        return $this->createQueryBuilder('m')
            ->where('t.revele_at BETWEEN :debut AND :fin')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->getQuery()
            ->getResult();
    }

//    public function findOneBySomeField($value): ?Temp
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
