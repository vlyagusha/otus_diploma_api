<?php

namespace App\Repository;

use App\Entity\UserMoviePreference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserMoviePreference|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserMoviePreference|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserMoviePreference[]    findAll()
 * @method UserMoviePreference[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserMoviePreferenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserMoviePreference::class);
    }

    // /**
    //  * @return UserMoviePreference[] Returns an array of UserMoviePreference objects
    //  */
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
    public function findOneBySomeField($value): ?UserMoviePreference
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
