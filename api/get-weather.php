<?php
/**
 * Weather API Handler
 * Securely fetches weather data from OpenWeatherMap
 * Last updated: Nov 6, 2025
 */

# API Configuration
# Note: In production, store this in environment variables
$api_key = "fa212de7fe0f9a7d4a830063568758a1";

# Get user location from request parameters
$lat = $_GET['lat'] ?? null; # Latitude
$lon = $_GET['lon'] ?? null; # Longitude

# Determine which data to fetch
# Available types:
# - weather: current conditions
# - forecast: 5-day prediction
# - aqi: air quality index
$type = $_GET['type'] ?? 'weather';

# Safety check - we need coordinates
if(!$lat || !$lon) {
    echo json_encode(['error' => 'Please enable location services to get weather data']);
    exit;
}

# Configure API request settings
$opts = [
    'http' => [
        'method' => 'GET',
        'header' => 'Content-Type: application/json',
        'timeout' => 10 # Prevent long waits
    ]
];
$context = stream_context_create($opts);


# Build the appropriate OpenWeatherMap API endpoint
switch ($type) {
    case 'weather':
        # Current weather conditions
        $api_url = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$api_key}&units=metric";
        break;
    case 'aqi':
        # Air quality data
        $api_url = "https://api.openweathermap.org/data/2.5/air_pollution?lat={$lat}&lon={$lon}&appid={$api_key}";
        break;
    case 'forecast':
        # 5-day weather forecast
        $api_url = "https://api.openweathermap.org/data/2.5/forecast?lat={$lat}&lon={$lon}&appid={$api_key}&units=metric";
        break;
    default:
        echo json_encode(['error' => 'Unknown weather data type requested']);
        exit;
}

# Fetch the weather data
$response_json = @file_get_contents($api_url);

# Handle connection issues gracefully
if ($response_json === false) {
    echo json_encode(['error' => 'Unable to reach weather service. Please try again in a moment.']);
    exit;
}

# Send the data back to the browser
header('Content-Type: application/json');
echo $response_json;
exit;
?>