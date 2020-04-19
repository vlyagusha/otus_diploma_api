<?php declare(strict_types=1);

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\HttpFoundation\Response;

class TmdbApiClient
{
    private Client $client;

    private string $endpoint;

    private string $key;

    public function __construct(Client $client, string $endpoint, string $key)
    {
        $this->client = $client;
        $this->endpoint = $endpoint;
        $this->key = $key;
    }

    public function getMoviesList(string $title, int $page = 1): array
    {
        try {
            $response = $this->client->get($this->endpoint . "/search/movie", [
                'query' => [
                    'api_key' => $this->key,
                    'language' => 'ru-RU',
                    'include_adult' => false,
                    'query' => $title,
                    'page' => $page,
                ],
            ]);
        } catch (GuzzleException $guzzleException) {
            return [];
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getMovie(int $movieId): ?array
    {
        try {
            $response = $this->client->get($this->endpoint . "/movie/$movieId", [
                'query' => [
                    'api_key' => $this->key,
                    'language' => 'ru-RU',
                ],
            ]);
        } catch (GuzzleException $guzzleException) {
            return null;
        }
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            return null;
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    public function getMovieRecommendations(int $movieId): ?array
    {
        try {
            $response = $this->client->get($this->endpoint . "/movie/$movieId/recommendations", [
                'query' => [
                    'api_key' => $this->key,
                    'language' => 'ru-RU',
                    'page' => 1,
                ],
            ]);
        } catch (GuzzleException $guzzleException) {
            return null;
        }
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            return null;
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}
