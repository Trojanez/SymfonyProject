<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);
    }

//    /**
//     * @return Product[] Returns an array of Product objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findAllProductsByCategoryId($id)
    {
        $query = $this->createQueryBuilder('e')
            ->join('e.category', 'r')
            ->where('r.id = :id')
            ->andWhere('e.is_in_cart != 1')
            ->andWhere('e.is_downloaded != 1')
            ->setParameter('id', $id)
            ->setMaxResults(3)
            ->getQuery();

        return $query->getResult();
    }

    public function getProductIfInCartAndNotDownloaded()
    {
        $query = $this->createQueryBuilder('e')
            ->where('e.is_in_cart = :param')
            ->andWhere('e.is_downloaded != 1')
            ->setParameter('param', 1)
            ->getQuery();

        return $query->getResult();
    }

    public function getDownloadedGames()
    {
        $query = $this->createQueryBuilder('e')
            ->where('e.is_in_cart = :param')
            ->andWhere('e.is_downloaded != 1')
            ->setParameter('param', 1)
            ->getQuery();

        return $query->getResult();
    }
    
}
