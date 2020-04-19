<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\UserMoviePreference;
use Doctrine\ORM\EntityManagerInterface;

class UserMoviePreferencesManager
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function saveUserMoviePreferences(string $userId, array $movies): UserMoviePreference
    {
        $userMoviePreference = $this->entityManager->getRepository(UserMoviePreference::class)->find($userId);
        if ($userMoviePreference === null) {
            $userMoviePreference = new UserMoviePreference();
            $userMoviePreference->setUserId($userId);
            $userMoviePreference->setMovies($movies);

            $this->entityManager->persist($userMoviePreference);
        } else {
            foreach ($movies as $movie) {
                $userMoviePreference->addMovie($movie);
            }
        }

        $this->entityManager->flush();

        return $userMoviePreference;
    }
}
