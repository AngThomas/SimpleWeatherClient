<?php

namespace App\Service;

use App\DTO\AverageTemperaturesDTO;
use App\DTO\WeatherDataDTO;
use App\DTO\WeatherSearchDTO;

class WeatherArchiveService
{
    public function __construct(
        private WeatherFetcher $weatherFetcher,
        private WeatherDataParser $weatherDataParser,
    ){}

    /**
     * @param WeatherSearchDTO $weatherSearchDTO
     * @return WeatherDataDTO
     * @throws \RuntimeException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function getAverageTemperatures(WeatherSearchDTO $weatherSearchDTO): WeatherDataDTO
    {
        $response = $this->weatherFetcher->fetchData($weatherSearchDTO);
        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('Error fetching data from weather API', $response->getStatusCode());
        }

        $averageTemperaturesDTO = new AverageTemperaturesDTO(
            $this->weatherDataParser->parseAverageTemperatures($response)
        );

        return new WeatherDataDTO(
            $weatherSearchDTO->getLatitude(),
            $weatherSearchDTO->getLongitude(),
            $averageTemperaturesDTO
        );
    }
}