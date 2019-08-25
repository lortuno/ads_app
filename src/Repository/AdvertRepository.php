<?php

namespace App\Repository;

use App\Entity\Advert;
use App\Entity\Status;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Advert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Advert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Advert[]    findAll()
 * @method Advert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Advert::class);
    }

    public function findByIdSerialized($id)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.id = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    /**
     * @return ArrayCollection
     */
    public function findAllAdvertInfo()
    : array
    {
        $fields = array(
            'a.id AS advert_id',
            'a.name AS advert_name',
            'status_id',
            's.name AS status_name',
            'GROUP_CONCAT(c.id) AS components_id',
        );

        $fields = implode(',', $fields);

        $conn =
            $this->getEntityManager()
                ->getConnection();

        $sql  = '
        SELECT ' . $fields . ' FROM advert a
           INNER JOIN status s ON a.status_id = s.id
           LEFT JOIN component c ON c.advert_id = a.id
           LEFT JOIN component_type ct on c.type_id = ct.id
        GROUP BY a.id
        ';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll();
    }

    /*
    public function findOneBySomeField($value): ?Advert
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
