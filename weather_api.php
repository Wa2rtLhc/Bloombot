<?php
function getWeather($city = "Nairobi") {
    $apiKey = "0cbe6dbb97ae2c61c6c962a4eae9afde"; 
    $url = "https://api.openweathermap.org/data/2.5/weather?q=" . urlencode($city) . "&appid=$apiKey&units=metric";

    $response = file_get_contents($url);

    if ($response === FALSE) {
        return null;
    }

    $data = json_decode($response, true);

    return [
        'temperature' => $data['main']['temp'],
        'humidity' => $data['main']['humidity'],
        'description' => $data['weather'][0]['description'],
        'wind_speed' => $data['wind']['speed']
];
}
?>