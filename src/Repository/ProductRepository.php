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

    /**
     * @param int $id
     * @param array $param
     * @return array
     */
    public function findProductsByCategoryId(int $id, array $param): array
    {
        $query = $this->createQueryBuilder('p')
            ->join('p.category', 'c')
            ->where('c.id = :id')
            ->andWhere('p.id NOT IN (:param)')
            ->setParameter('id', $id)
            ->setParameter('param', $param)
            ->setMaxResults(3)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param int $id
     * @return array
     */
    public function findAllProductsByCategoryId(int $id): array
    {
        $query = $this->createQueryBuilder('p')
            ->join('p.category', 'c')
            ->where('c.id = :id')
            ->setParameter('id', $id)
            ->setMaxResults(3)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param array $param
     * @return array|null
     */
    public function showProductsFromCart(array $param): ?array
    {
        $query = $this->createQueryBuilder('p')
            ->where('p.id IN (:param)')
            ->setParameter('param', $param)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param int $id
     * @param array $param
     * @param array $param2
     * @return array
     */
    public function showProductsNotInCart(int $id, array $param, array $param2): array
    {
        $query = $this->createQueryBuilder('p')
            ->join('p.category', 'c')
            ->where('c.id = :id')
            ->andWhere('p.id NOT IN (:param)')
            ->andWhere('p.id NOT IN (:param2)')
            ->setParameter('id', $id)
            ->setParameter('param', $param)
            ->setParameter('param2', $param2)
            ->setMaxResults(3)
            ->getQuery();

        return $query->getResult();
    }

    /**
     * @param string $image
     * @return array
     */
    public function getIdAccordingToImageName(string $image): array
    {
        $query = $this->createQueryBuilder('p')
            ->select('p.id')
            ->where('p.image = :name')
            ->setParameter('name',$image)
            ->getQuery();

        return $query->getResult();
    }
}
