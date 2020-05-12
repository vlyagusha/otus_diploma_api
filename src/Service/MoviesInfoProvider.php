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

    public function getMovies(string $title, int $page = 1): array
    {
        $movies = [];
        $moviesList = $this->apiClient->getMoviesList($title, $page);
        foreach ($moviesList['results'] as $movie) {
            $movieInfo = $this->getFromArray($movie);

            $movies[] = $movieInfo;
        }

        return $movies;
    }

    public function getMovieInfo(int $movieId): ?MovieInfo
    {
        $movie = $this->apiClient->getMovie($movieId);
        if ($movie === null) {
            return null;
        }

        return $this->getFromArray($movie);
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
                $movieInfo = $this->getFromArray($result);
                $recommendations[] = $movieInfo;

                if (count($recommendations) === $limit) {
                    break(2);
                }
            }
        }

        return $recommendations;
    }

    private function getFromArray(array $movie): MovieInfo
    {
        $releaseDate = $movie['release_date'] ?? null;
        if (!empty($releaseDate)) {
            $releaseYear = explode('-', $releaseDate)[0] ?? '';
            $title = sprintf('%s (%s)', $movie['title'], $releaseYear);
        } else {
            $title = $movie['title'];
        }

        $movieInfo = new MovieInfo();
        $movieInfo->setId($movie['id']);
        $movieInfo->setTitle($title);
        $movieInfo->setTrailerLink($this->retrieveTrailerLink($movie['id']));

        return $movieInfo;
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
