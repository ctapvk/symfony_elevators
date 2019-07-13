<?php

namespace App\Repository;

use App\Entity\Houses;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Houses|null find($id, $lockMode = null, $lockVersion = null)
 * @method Houses|null findOneBy(array $criteria, array $orderBy = null)
 * @method Houses[]    findAll()
 * @method Houses[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HousesRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Houses::class);
    }

    // /**
    //  * @return Houses[] Returns an array of Houses objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('h.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Houses
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
