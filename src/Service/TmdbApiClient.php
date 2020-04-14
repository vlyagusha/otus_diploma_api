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

    public function getMoviesList(string $title): array
    {
        $response = $this->client->get($this->endpoint . '/search/movie', [
            'query' => $this->getCommonQuery() + [
                'query' => $title
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }

    private function getCommonQuery(): array
    {
        return [
            'api_key' => $this->key,
            'language' => 'ru-RU',
            'page' => 1,
            'include_adult' => false,
        ];
    }
}
