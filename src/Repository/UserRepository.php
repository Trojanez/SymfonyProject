<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

//    /**
//     * @return User[] Returns an array of User objects
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
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getUserSubscribeInfo($param)
    {
        $query = $this->createQueryBuilder('e')
            ->select('e.is_subscribe')
            ->where('e.phone = :phone')
            ->setParameter('phone', $param)
            ->getQuery();

        return $query->getResult();
    }
    public function getUserSubscribeInfoAdditional($param)
    {
        $query = $this->createQueryBuilder('e')
            ->select('e.is_subscribe')
            ->where('e.phone = :phone')
            ->setParameter('phone', $param)
            ->getQuery();

        return $query->getSingleScalarResult();
    }

    public function getSubscribedDateForUser($param)
    {
        $query = $this->createQueryBuilder('e')
            ->select('e.date')
            ->where('e.id = :param')
            ->setParameter('param', $param)
            ->getQuery();

        return $query->getResult();
    }

    public function getDownloadsForUser($param)
    {
        $query = $this->createQueryBuilder('e')
            ->select('e.downloads')
            ->where('e.phone = :phone')
            ->setParameter('phone', $param)
            ->getQuery();

        return $query->getResult();
    }

    public function getUserId($param)
    {
        $query = $this->createQueryBuilder('e')
            ->select('e.id')
            ->where('e.phone = :phone')
            ->setParameter('phone', $param)
            ->getQuery();

        return $query->getSingleScalarResult();
    }

}
