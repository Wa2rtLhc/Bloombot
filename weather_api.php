<?php
function getWeather($city = "Nairobi") {
    $apiKey = "0cbe6dbb97ae2c61c6c962a4eae9afde"; // Replace with your valid OpenWeatherMap API key
    $url = "https://api.openweathermap.org/data/2.5/weather?q=" . urlencode($city) . "&appid=$apiKey&units=metric";

    // Turn on error reporting (for debugging)
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $response = file_get_contents($url);

    if ($response === FALSE) {
        return null;
    }

    $data = json_decode($response, true);

    if (!isset($data['weather'][0])) {
        return null;
    }

    // Check if rain is mentioned in weather description or present in data
    $description = strtolower($data['weather'][0]['description']);
    $willRain = strpos($description, 'rain') !== false || isset($data['rain']);

    return [
        'temperature' => $data['main']['temp'],
        'humidity' => $data['main']['humidity'],
        'description' => $data['weather'][0]['description'],
        'wind_speed' => $data['wind']['speed'],
        'will_rain' => $willRain,
        'city' => $data['name']
];
}
?>
