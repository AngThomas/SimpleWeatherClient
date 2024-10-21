<?php

namespace App\DTO;

class WeatherDataDTO
{
    /**
     * @param float $latitude
     * @param float $longitude
     * @param AverageTemperaturesDTO $averageTemperaturesDTO
     */
    public function __construct(
        private float $latitude,
        private float $longitude,
        private AverageTemperaturesDTO $averageTemperaturesDTO
    )
    {
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function getAverageTemperaturesDTO(): AverageTemperaturesDTO
    {
        return $this->averageTemperaturesDTO;
    }

    public function setAverageTemperatures(AverageTemperaturesDTO $averageTemperaturesDTO): self
    {
        $this->averageTemperaturesDTO = $averageTemperaturesDTO;
        return $this;
    }

    public function toJson(): string
    {
        return json_encode([
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'history_weather' => $this->averageTemperaturesDTO->getAverageTemperatures(),
        ]);
    }
}