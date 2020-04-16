<?php declare(strict_types=1);

namespace App\Service;

use GuzzleHttp\Client;

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
        $response = $this->client->get($this->endpoint . '/search/movie', [
            'query' => [
                'api_key' => $this->key,
                'language' => 'ru-RU',
                'include_adult' => false,
                'query' => $title,
                'page' => $page,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}
