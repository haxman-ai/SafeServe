<?php

namespace App\Repository;

use App\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Menu>
 */
class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    public function findWeekMenus(\DateTime $start, \DateTime $end): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.served_at BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->getResult();
    }

    /**
     * Bornes lundi-vendredi de la semaine décalée de $offset semaines par rapport à la semaine courante.
     *
     * @return array{0: \DateTime, 1: \DateTime}
     */
    public function getWeekBounds(int $offset): array
    {
        $monday = new \DateTime('monday this week');
        $monday->modify("{$offset} week");
        $friday = clone $monday;
        $friday->modify('+4 days')->setTime(23, 59, 59);

        return [$monday, $friday];
    }
}
