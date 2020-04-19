<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\UserMoviePreference;

class MoviesInfoProvider
{
    private TmdbApiClient $apiClient;

    public function __construct(TmdbApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function getList(string $title, int $page = 1): array
    {
        $result = [];
        $moviesList = $this->apiClient->getMoviesList($title, $page);
        foreach ($moviesList['results'] as $movie) {
            $result[] = [
                'id' => $movie['id'],
                'title' => $movie['title'],
                'popularity' => $movie['popularity'],
            ];
        }
        usort($result, function ($a, $b) {
            return $b['popularity'] <=> $a['popularity'];
        });

        return $result;
    }

    public function getMovie(int $movieId): ?array
    {
        $movie = $this->apiClient->getMovie($movieId);
        if ($movie === null) {
            return null;
        }

        return [
            'id' => $movie['id'],
            'title' => $movie['title'],
        ];
    }

    public function getRecommendations(UserMoviePreference $userMoviePreference, int $limit = 5): array
    {
        $recommendations = [];
        foreach ($userMoviePreference->getMovies() as $movieId) {
            $response = $this->apiClient->getMovieRecommendations($movieId);
            if ($response === null) {
                continue;
            }
            foreach ($response['results'] as $result) {
                $recommendations[$result['id']] = [
                    'id' => $result['id'],
                    'title' => $result['title'],
                ];
            }
        }

        return array_slice(array_values($recommendations), 0, $limit);
    }
}
