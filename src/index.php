<?php

use App\Service\WeatherArchiveService;
use App\Service\WeatherDataParser;
use App\Service\WeatherFetcher;
use App\DTO\WeatherSearchDTO;
use GuzzleHttp\Client;

require_once __DIR__ . '/../vendor/autoload.php';

header('Content-Type: application/json');

try {
    $weatherSearchDTO = WeatherSearchDTO::fromRequest();
    $weatherArchiveService = buildWeatherArchiveService();

    echo $weatherArchiveService->getAverageTemperatures($weatherSearchDTO)->toJson();
} catch (Exception $e) {
    http_response_code($e->getCode() !== 0 ? $e->getCode() : 500);
    echo json_encode(['error' => $e->getMessage()]);
}

function buildWeatherArchiveService(): WeatherArchiveService
{
    return new WeatherArchiveService(
        new WeatherFetcher(
            new Client(),
            'https://archive-api.open-meteo.com/v1/archive'
        ),
        new WeatherDataParser()
    );
}
