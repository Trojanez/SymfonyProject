<?php

namespace App\Repository;

use App\Entity\Screen;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Screen|null find($id, $lockMode = null, $lockVersion = null)
 * @method Screen|null findOneBy(array $criteria, array $orderBy = null)
 * @method Screen[]    findAll()
 * @method Screen[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScreenRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Screen::class);
    }

//    /**
//     * @return Screen[] Returns an array of Screen objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Screen
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @param int $id
     * @return array
     */
    public function getScreenAccordingProduct(int $id): array
    {
        $query = $this->createQueryBuilder('e')
            ->join('e.product', 'r')
            ->where('e.product = :id')
            ->setParameter('id', $id)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param int $id
     * @return array
     */
    public function countScreenNumber(int $id): array
    {
        $query = $this->createQueryBuilder('e')
            ->join('e.product', 'r')
            ->where('e.product = :id')
            ->setParameter('id', $id)
            ->select('COUNT(e.id) as sum')
            ->getQuery();

        return $query->getResult();
    }
}
