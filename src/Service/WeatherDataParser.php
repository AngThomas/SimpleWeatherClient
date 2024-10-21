<?php

namespace App\Service;

use DateTime;
use Psr\Http\Message\ResponseInterface;

class WeatherDataParser
{
    const DATE_FORMAT = 'Y-m-d';
    const CHUNK_SIZE = 24;
    const KEY_HOURLY = 'hourly';
    const KEY_TIME = 'time';
    const KEY_TEMPERATURE_2M = 'temperature_2m';

    /**
     * @param array<array<string>> $dayChunks
     * @return string[]
     * @throws \Exception
     */
    private function parseDayDates(array $dayChunks): array
    {
        return array_map(function($dayChunk) {
            $date = new DateTime($dayChunk[0]);
            return $date->format(self::DATE_FORMAT);
        }, $dayChunks);

    }

    private function calculateAvgTemperature(array $temperatures): float
    {
        if (empty($temperatures)) {
            throw new \InvalidArgumentException('Temperature array is empty', 422);
        }

        return round(array_sum($temperatures) / count($temperatures), 2);
    }

    /**
     * @param ResponseInterface $response
     * @return array<array<string, mixed>>
     * @throws \InvalidArgumentException
     * @throws \LogicException
     * @throws \Exception
     */
    public function parseAverageTemperatures(ResponseInterface $response): array
    {
        $averageTemperatures = [];
        $weatherData = json_decode($response->getBody()->getContents(), true);
        if (!isset($weatherData[self::KEY_HOURLY][self::KEY_TIME]) || !isset($weatherData[self::KEY_HOURLY][self::KEY_TEMPERATURE_2M])) {
            throw new \InvalidArgumentException('Invalid or missing weather data!', 422);
        }

        $chunkedDates = array_chunk($weatherData[self::KEY_HOURLY][self::KEY_TIME], self::CHUNK_SIZE);
        $chunkedTemperatures = array_chunk($weatherData[self::KEY_HOURLY][self::KEY_TEMPERATURE_2M], self::CHUNK_SIZE);
        $dates = $this->parseDayDates($chunkedDates);
        if (count($dates) !== count($chunkedTemperatures)) {
            throw new \LogicException('Mismatched number of dates and temperature data chunks', 500);
        }

        $dateTemperatureMap = array_combine($dates, $chunkedTemperatures);
        foreach ($dateTemperatureMap as $date => $dayTemperatures) {
            $filteredTemperatures = array_filter($dayTemperatures, function($temp) {
                return $temp !== null;
            });

            $averageTemperatures[] = [
                'date' => $date,
                'average_temperature' => !empty($filteredTemperatures) ? $this->calculateAvgTemperature($filteredTemperatures) : null,
            ];
        }

        return $averageTemperatures;
    }
}