<?php

namespace App\DTO;

use DateTimeImmutable;

class WeatherSearchDTO extends AbstractDTO
{
    public function __construct(
        private string $latitude,
        private string $longitude,
        private DateTimeImmutable $startDate,
        private DateTimeImmutable $endDate,
        private string $hourly = 'temperature_2m',
    )
    {
    }

    public function getLatitude(): string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): self
    {
        $this->latitude = $latitude;
        return $this;
    }

    public function getLongitude(): string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): self
    {
        $this->longitude = $longitude;
        return $this;
    }

    public function getStartDate(): DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(DateTimeImmutable $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(DateTimeImmutable $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function getHourly(): string
    {
        return $this->hourly;
    }

    public function setHourly(string $hourly): self
    {
        $this->hourly = $hourly;
        return $this;
    }

    /**
     * @return array<string, string>
     */
    public function toQuery(): array
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'start_date' => $this->startDate->format('Y-m-d'),
            'end_date' => $this->endDate->format('Y-m-d'),
            'hourly' => $this->hourly,
        ];
    }

    /**
     * @throws \Exception
     */
    public static function fromRequest(): self
    {
        $dataArray = self::fromQueryString();
        return new self(
            $dataArray['latitude'] ?? '',
            $dataArray['longitude'] ?? '',
            new DateTimeImmutable($dataArray['startDate'] ?? 'now'),
            new DateTimeImmutable($dataArray['endDate'] ?? 'now')
        );
    }
}