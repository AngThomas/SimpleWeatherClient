<?php

namespace App\DTO;

class AbstractDTO
{
    protected static function fromStream(): array
    {
        $jsonData = file_get_contents("php://input");
        return json_decode($jsonData, true);
    }

    protected static function fromQueryString(): array
    {
        return $_GET;
    }
}