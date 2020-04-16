<?php declare(strict_types=1);

namespace App\Service;

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
}
