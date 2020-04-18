<?php

namespace App\Repository;

use App\Doctrine\DBAL\Types\ArrayIntType;
use App\Entity\UserMoviePreference;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
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

    public function getLikes(UserMoviePreference $userMoviePreference): array
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('movie_id', 'movie_id');
        $rsm->addScalarResult('likes', 'likes');

        return $this->getEntityManager()
            ->createNativeQuery('
                select movie_id, round(sum(similarity)/count(similarity), 4) as likes from (
                    select
                        unnest(movies - :userMoviesArray) as movie_id,
                        round(cardinality(:userMoviesArray & movies)::numeric / cardinality(:userMoviesArray | movies)::numeric, 4) as similarity
                    from user_movie_preferences
                    where :userMoviesArray && movies and user_id != :userId and movies - :userMoviesArray != :emptyArray
                    order by similarity desc) as similarity_table
                group by movie_id
                order by likes desc;', $rsm)
            ->setParameter('userId', $userMoviePreference->getUserId())
            ->setParameter('userMoviesArray', $userMoviePreference->getMovies(), ArrayIntType::ARRAY_INT)
            ->setParameter('emptyArray', [], ArrayIntType::ARRAY_INT)
            ->getResult();
    }
}
