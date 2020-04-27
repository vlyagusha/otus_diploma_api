<?php declare(strict_types=1);

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Sentry\ClientInterface as SentryClient;
use Symfony\Component\HttpFoundation\Response;

class TmdbApiClient
{
    private Client $client;
    private SentryClient $sentry;
    private string $endpoint;
    private string $key;

    public function __construct(Client $client, SentryClient $sentry, string $endpoint, string $key)
    {
        $this->client = $client;
        $this->sentry = $sentry;
        $this->endpoint = $endpoint;
        $this->key = $key;
    }

    public function getMoviesList(string $title, int $page = 1): array
    {
        $uri = sprintf('%s/search/movie', $this->endpoint);
        try {
            $response = $this->client->get($uri, [
                'query' => [
                    'api_key' => $this->key,
                    'language' => 'ru-RU',
                    'include_adult' => false,
                    'query' => $title,
                    'page' => $page,
                    'region' => 'RU'
                ],
            ]);
        } catch (GuzzleException $guzzleException) {
            $this->sentry->captureException($guzzleException);

            return [];
        }
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            return [];
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getMovie(int $movieId): ?array
    {
        $uri = sprintf('%s/movie/%s', $this->endpoint, $movieId);
        try {
            $response = $this->client->get($uri, [
                'query' => [
                    'api_key' => $this->key,
                    'language' => 'ru-RU',
                ],
            ]);
        } catch (GuzzleException $guzzleException) {
            $this->sentry->captureException($guzzleException);

            return null;
        }
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            return null;
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getMovieRecommendations(int $movieId): ?array
    {
        $uri = sprintf('%s/movie/%s/recommendations', $this->endpoint, $movieId);
        try {
            $response = $this->client->get($uri, [
                'query' => [
                    'api_key' => $this->key,
                    'language' => 'ru-RU',
                    'page' => 1,
                ],
            ]);
        } catch (GuzzleException $guzzleException) {
            $this->sentry->captureException($guzzleException);

            return null;
        }
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            return null;
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getMovieVideos(int $movieId): ?array
    {
        $uri = sprintf('%s/movie/%s/videos', $this->endpoint, $movieId);
        try {
            $response = $this->client->get($uri, [
                'query' => [
                    'api_key' => $this->key,
                    'language' => 'ru-RU',
                ],
            ]);
        } catch (GuzzleException $guzzleException) {
            $this->sentry->captureException($guzzleException);

            return null;
        }
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            return null;
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}
