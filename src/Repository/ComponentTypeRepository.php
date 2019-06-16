<?php

namespace App\Repository;

use App\Entity\ComponentType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ComponentType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ComponentType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ComponentType[]    findAll()
 * @method ComponentType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComponentTypeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ComponentType::class);
    }

//    /**
//     * @return ComponentType[] Returns an array of ComponentType objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ComponentType
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
