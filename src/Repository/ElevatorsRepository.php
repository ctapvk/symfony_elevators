<?php

namespace App\Repository;

use App\Entity\Elevators;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Elevators|null find($id, $lockMode = null, $lockVersion = null)
 * @method Elevators|null findOneBy(array $criteria, array $orderBy = null)
 * @method Elevators[]    findAll()
 * @method Elevators[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ElevatorsRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Elevators::class);
    }

    // /**
    //  * @return Elevators[] Returns an array of Elevators objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Elevators
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
