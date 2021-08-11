<?php

namespace App\Repository;

use App\Entity\Hit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Hit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hit[]    findAll()
 * @method Hit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hit::class);
    }

    /**
     * @param string $shop
     * @return string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getHits(string $shop): string
    {
        return $this->createQueryBuilder('hits')
            ->select('count(hits.id)')
            ->where('hits.shop = :shop')
            ->setParameter('shop', $shop)
            ->getQuery()
            ->getSingleScalarResult();
    }

}
