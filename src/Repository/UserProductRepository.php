<?php

namespace App\Repository;

use App\Entity\UserProduct;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserProduct|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserProduct|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserProduct[]    findAll()
 * @method UserProduct[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserProductRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserProduct::class);
    }

//    /**
//     * @return UserProduct[] Returns an array of UserProduct objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserProduct
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getProductIfInCartAndNotDownloaded($param)
    {
        $query = $this->createQueryBuilder('u')
            ->join('u.product', 'r')
            ->join('u.user', 'e')
            ->select('r.id', 'r.name', 'r.description','r.image')
            ->where('u.is_in_cart = 1')
            ->andWhere('u.is_downloaded = 0')
            ->andWhere('u.product = r.id')
            ->andWhere('e.phone = :param')
            ->setParameter('param', $param)
            ->getQuery();

        return $query->getResult();
    }

    public function getDownloadedGames($userId)
    {
        $query = $this->createQueryBuilder('u')
            ->select('u')
            ->where('u.is_downloaded = 1')
            ->andWhere('u.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery();

        return $query->getScalarResult();
    }

    public function getAmountOfDownloadedGames($userId)
    {
        $query = $this->createQueryBuilder('u')
            ->select('count(u.is_downloaded)')
            ->where('u.is_downloaded = 1')
            ->andWhere('u.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    public function getAllDownloadedGames($userId)
    {
        $query = $this->createQueryBuilder('u')
            ->select('p.id', 'p.name', 'p.description', 'p.image')
            ->join('u.product', 'p')
            ->where('u.is_downloaded = 1')
            ->andWhere('u.user = :param')
            ->setParameter('param', $userId)
            ->getQuery();

        return $query->getScalarResult();
    }
}
