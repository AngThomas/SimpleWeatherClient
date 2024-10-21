# recruitment-task

## Task:
Your task is to create a PHP service that fetches data from a public weather repository,
filters this data for a specific time range, make some processing and returns it in a specified JSON format.  
Additionally, you should write a unit test for the service.

### Evaluation Criteria
- Code structure
- Code performance
- Code quality
- Unit tests
- Error handling

### Useful data
- Latitude: 50.049683  
- Longitude: 19.944544

### Initiating the Service
- Execute `source docker/.profile` to establish command shortcuts
- Launch the service by running `dstart`
- Visit https://localhost:443 to interact with the application
- Refer to `docker/.profile` for additional commands (for instance, the command to execute phpunit tests)

### Steps to Complete:
#### 1. Fetching Data
Utilize the free OpenWeatherMap API endpoint to retrieve weather data for the specified location, focusing on the date range that spans from 7 days ago to 3 days ago.

Endpoint:
https://archive-api.open-meteo.com/v1/archive?latitude={latitude}&longitude={longitude}&start_date={start_date}&end_date={end_date}&hourly=temperature_2m

Parameters:
- `latitude`: latitude of the location
- `longitude`: longitude of the location
- `start_date`: start date in the format YYYY-MM-DD
- `end_date`: end date in the format YYYY-MM-DD

#### 2. Processing Data
Calculate average temperature for each day based on hourly data.

#### 3. Formatting Data
The data should be accessible from a localhost endpoint.
Average temperature should be rounded to 2 decimal places.
Display the filtered data in JSON format with specified keys:
```json
{
  "latitude": "value",
  "longitude": "value",
  "history_weather": [
    {
      "date": "YYYY-MM-DD",
      "average_temperature": "value"
    },
    ...
  ]
}
```

#### 4. Unit Testing
Write a unit test to verify the correctness of the services.
