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
        if (empty($likes)) {
            return $this->moviesInfoProvider->getRecommendations($userMoviePreference, $limit);
        }

        $recommendations = [];
        foreach ($likes as $like) {
            $movieInfo = $this->moviesInfoProvider->getMovieInfo($like['movie_id']);
            if ($movieInfo !== null) {
                $recommendations[] = $movieInfo;
            }
            if (count($recommendations) === $limit) {
                break;
            }
        }

        return $recommendations;
    }
}
