<?php

namespace App\DTO;

class AverageTemperaturesDTO
{
    /**
     * @param array<array<string, string>> $averageTemperatures
     */
    public function __construct(
        private array $averageTemperatures
    )
    {
    }

    /**
     * @return array<array<string, mixed>>
     */
    public function getAverageTemperatures(): array
    {
        return $this->averageTemperatures;
    }

    /**
     * @param array<array<string, mixed>> $averageTemperatures
     */
    public function setAverageTemperatures(array $averageTemperatures): self
    {
        $this->averageTemperatures = $averageTemperatures;
        return $this;
    }


}