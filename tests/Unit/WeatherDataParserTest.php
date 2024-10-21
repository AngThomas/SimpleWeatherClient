<?php

namespace Unit;

use App\Service\WeatherDataParser;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class WeatherDataParserTest extends TestCase
{
    private WeatherDataParser $parser;

    protected function setUp(): void
    {
        $this->parser = new WeatherDataParser();
    }

    /**
     * @dataProvider datesProvider
     */
    public function testCorrectDatesAreParsedToNewFormat(array $input, array $expected)
    {
        $result = $this->invokeMethod($this->parser, 'parseDayDates', [$input]);

        $this->assertEquals($expected, $result);
    }

    public function datesProvider(): array
    {
        return [
            [
                [['2023-10-16T00:00:00', '2023-10-16T01:00:00', '2023-10-16T02:00:00'], ['2023-10-17T00:00:00', '2023-10-17T00:01:00', '2023-10-17T00:02:00']],
                ['2023-10-16', '2023-10-17'],
            ],
            [
                [['2023-01-01T12:00:00', '2023-01-01T13:00:00', '2023-01-01T14:00:00'], ['2023-01-02T12:00:00', '2023-01-02T13:00:00']],
                ['2023-01-01', '2023-01-02'],
            ],
            [
                [['2024-02-29 00:00:00']], // Leap year
                ['2024-02-29'],
            ],
        ];
    }

    /**
     * @dataProvider temperaturesProvider
     */
    public function testTemperaturesAreCorrectlyAveraged(array $temperatures, float $expected)
    {
        $result = $this->invokeMethod($this->parser, 'calculateAvgTemperature', [$temperatures]);

        $this->assertEqualsWithDelta($expected, $result, 0.01);
    }

    public function temperaturesProvider(): array
    {
        return [
            [[20.0, 22.55555, 19.49999], 20.69],
            [[20.123456, 22.654321, 19.987654], 20.92],
            [[15.987654321, 20.123456789, 25.456789], 20.52],
            [[10.100001, 10.200002, 10.300003], 10.20],
            [[0.123456789, 0.987654321, 0.555555555], 0.56],
            [[-0.123456789, -0.987654321, -0.555555555], -0.56],
            [[35.6789, 32.1234, 30.4567], 32.75],
            [[-15.12345, -20.6789, -10.99999], -15.60],
        ];
    }

    public function testAverageTemperatureCalculatingThrowsExceptionOnEmptyArray()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Temperature array is empty');
        $this->expectExceptionCode(422);

        $temperatures = [];

        $this->invokeMethod($this->parser, 'calculateAvgTemperature', [$temperatures]);
    }

    public function testParseAverageTemperaturesReturnValidDataValidResponse()
    {
        $mockStream = $this->createMock(StreamInterface::class);
        $mockStream->method('getContents')
            ->willReturn(file_get_contents(__DIR__ . '/../Resource/weatherData.json'));

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getBody')
            ->willReturn($mockStream);

        $result = $this->parser->parseAverageTemperatures($mockResponse);

        $this->assertCount(8, $result);

        $this->assertEquals('2024-10-09', $result[0]['date']);
        $this->assertEquals(14.82, $result[0]['average_temperature']);

        $this->assertEquals('2024-10-10', $result[1]['date']);
        $this->assertEquals(16.39, $result[1]['average_temperature']);

        $this->assertEquals('2024-10-11', $result[2]['date']);
        $this->assertEquals(12.12, $result[2]['average_temperature']);

        $this->assertEquals('2024-10-12', $result[3]['date']);
        $this->assertEquals(9.25, $result[3]['average_temperature']);

        $this->assertEquals('2024-10-13', $result[4]['date']);
        $this->assertEquals(10.96, $result[4]['average_temperature']);

        $this->assertEquals('2024-10-14', $result[5]['date']);
        $this->assertEquals(9.1, $result[5]['average_temperature']);

        $this->assertEquals('2024-10-15', $result[6]['date']);
        $this->assertEquals(5.5, $result[6]['average_temperature']);

        $this->assertEquals('2024-10-16', $result[7]['date']);
        $this->assertEquals(null, $result[7]['average_temperature']);
    }

    public function testParseAverageTemperaturesThrowsExceptionOnInvalidResponse()
    {
        $mockStream = $this->createMock(StreamInterface::class);
        $mockStream->method('getContents')
            ->willReturn(file_get_contents(__DIR__ . '/../Resource/invalidWeatherData.json'));

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getBody')
            ->willReturn($mockStream);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid or missing weather data!');
        $this->expectExceptionCode(422);

        $result = $this->parser->parseAverageTemperatures($mockResponse);
    }

    public function testParseAverageTemperaturesThrowsExceptionOnMismatchedDataResponse()
    {
        $mockStream = $this->createMock(StreamInterface::class);
        $mockStream->method('getContents')
            ->willReturn(file_get_contents(__DIR__ . '/../Resource/mismatchedWeatherData.json'));

        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getBody')
            ->willReturn($mockStream);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Mismatched number of dates and temperature data chunks');
        $this->expectExceptionCode(500);

        $result = $this->parser->parseAverageTemperatures($mockResponse);
    }

    private function invokeMethod(&$object, $methodName, $parameters = [])
    {
        $reflection = new \ReflectionClass($object);
        $method = $reflection->getMethod($methodName);

        return $method->invokeArgs($object, $parameters);
    }
}
