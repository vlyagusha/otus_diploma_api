<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\UserMoviePreference;
use App\Repository\UserMoviePreferenceRepository;

class UserMovieRecommendationsProvider
{
    private UserMoviePreferenceRepository $userMoviePreferenceRepository;
    private MoviesInfoProvider $moviesInfoProvider;

    public function __construct(UserMoviePreferenceRepository $userMoviePreferenceRepository, MoviesInfoProvider $moviesInfoProvider)
    {
        $this->userMoviePreferenceRepository = $userMoviePreferenceRepository;
        $this->moviesInfoProvider = $moviesInfoProvider;
    }

    public function getRecommendations(UserMoviePreference $userMoviePreference, int $limit = 5): array
    {
        $likes = $this->userMoviePreferenceRepository->getLikes($userMoviePreference);
        $likes = array_slice($likes, 0, $limit);
        $recommendations = [];
        foreach ($likes as &$like) {
            $movie = $this->moviesInfoProvider->getMovie($like['movie_id']);
            if ($movie !== null) {
                $recommendations[] = [
                    'id' => $like['movie_id'],
                    'title' => $movie['title'],
                ];
            }
        }

        return $recommendations;
    }
}
