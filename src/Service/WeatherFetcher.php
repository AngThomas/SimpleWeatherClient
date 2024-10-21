<?php

namespace App\Service;

use App\DTO\WeatherSearchDTO;
use DateTime;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class WeatherFetcher
{
    public function __construct(
        private Client $client,
        private string $endpointUrl
    )
    {
    }


    public function fetchData(WeatherSearchDTO $weatherSearchDTO): ResponseInterface
    {
        return $this->client->request('GET', $this->endpointUrl, [
            'query' => $weatherSearchDTO->toQuery()

        ]);
    }
}