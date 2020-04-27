<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\MovieInfo;
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

    public function getMovieInfo(int $movieId): ?MovieInfo
    {
        $movie = $this->apiClient->getMovie($movieId);
        if ($movie === null) {
            return null;
        }

        $movieInfo = new MovieInfo();
        $movieInfo->setId($movie['id']);
        $movieInfo->setTitle($movie['title']);
        $movieInfo->setTrailerLink($this->retrieveTrailerLink($movie['id']));

        return $movieInfo;
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
                $movieInfo = new MovieInfo();
                $movieInfo->setId($result['id']);
                $movieInfo->setTitle($result['title']);
                $movieInfo->setTrailerLink($this->retrieveTrailerLink($result['id']));

                $recommendations[] = $movieInfo;

                if (count($recommendations) === $limit) {
                    break(2);
                }
            }
        }

        return $recommendations;
    }

    private function retrieveTrailerLink(int $movieId): ?string
    {
        $videos = $this->apiClient->getMovieVideos($movieId);

        if (!isset($videos['results'])) {
            return null;
        }

        foreach ($videos['results'] as $video) {
            if (isset($video['type']) && $video['type'] !== 'Trailer') {
                continue;
            }
            if (isset($video['site']) && $video['site'] !== 'YouTube') {
                continue;
            }
            if (!isset($video['key'])) {
                continue;
            }

            return sprintf('https://www.youtube.com/watch?v=%s', $video['key']);
        }

        return null;
    }
}
