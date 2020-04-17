<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user_movie_preferences")
 * @ORM\Entity(repositoryClass="App\Repository\UserMoviePreferenceRepository")
 */
class UserMoviePreference
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private string $userId;

    /**
     * @ORM\Column(type="array_int", nullable=false)
     */
    private array $movies = [];

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    public function getMovies(): array
    {
        return $this->movies;
    }

    public function setMovies(array $movies): void
    {
        $this->movies = $movies;
    }

    public function addMovie(int $movieId): void
    {
        $this->setMovies(array_unique(array_merge($this->getMovies(), [$movieId])));
    }
}
