<?php

namespace App\Repository;

use App\Entity\Download;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Download|null find($id, $lockMode = null, $lockVersion = null)
 * @method Download|null findOneBy(array $criteria, array $orderBy = null)
 * @method Download[]    findAll()
 * @method Download[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DownloadRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Download::class);
    }

    public function findAfter(int $days) {
        $entityManager = $this->getEntityManager();

        $rsm = new ResultSetMappingBuilder($entityManager);
        $rsm->addIndexByScalar('date');
        $rsm->addScalarResult('date', 'date', 'date');
        $rsm->addScalarResult('count', 'count', 'integer');

        $query = $entityManager->createNativeQuery('SELECT DATE(time) as date, COUNT(time) as count FROM download WHERE time >= DATE_SUB(CURRENT_DATE(), INTERVAL :days DAY) GROUP BY DATE(time);', $rsm);
        $query->setParameter('days', $days);

        return $query->getArrayResult();
    }

//    /**
//     * @return Download[] Returns an array of Download objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Download
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
