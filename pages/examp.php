<?php
session_start();
include __DIR__ . '/../includes/db_connect.php'; // Relative path

// *** CRITICAL FIX ***
// Was: $_SESSION['loggein'] !== true
// Is:  $_SESSION['loggedin'] !== true 
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: 5-sign-in.php");
    exit;
}

// if user is logged in, 
// load their information
$full_name = $_SESSION['full_name'];

// --- API Placeholder for Detailed 5-Day Forecast ---
$api_key = "YOUR_OPENWEATHERMAP_API_KEY"; // Replace this
$city = "London"; // Example city
$forecast_data = null;
$forecast_url = "https://api.openweathermap.org/data/2.5/forecast?q={$city}&appid={$api_key}&units=metric";

$forecast_json = @file_get_contents($forecast_url);
if ($forecast_json) {
    $forecast_data = json_decode($forecast_json, true);
    // Filter the list to get one forecast per day (e.g., at noon)
    $daily_forecast = [];
    if (isset($forecast_data['list'])) {
        foreach ($forecast_data['list'] as $item) {
            // Check if the time is 12:00:00
            if (strpos($item['dt_txt'], '12:00:00') !== false) {
                $daily_forecast[] = $item;
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>5-Day Forecast</title>
    <link rel="stylesheet" href="/web-dev/health-project/assets/css/a-style.css">
    <style>
        .forecast-container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 2rem;
            background: #f1f5f9;
            border-radius: 8px;
        }
        .forecast-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1.5rem;
        }
        .forecast-card {
            background: #fff;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .forecast-card h3 {
            margin: 0 0 0.5rem 0;
            font-size: 1.1rem;
            color: #334155;
        }
        .forecast-card .temp {
            font-size: 1.75rem;
            font-weight: bold;
            color: #1e293b;
            margin: 0.5rem 0;
        }
        .forecast-card .icon {
            width: 50px;
            height: 50px;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../includes/header.php'; // Relative path ?>

    <main class="forecast-container">
        <h2>5-Day Weather Forecast for <?php echo htmlspecialchars($city); ?></h2>

        <?php if ($daily_forecast): ?>
            <div class="forecast-grid">
                <?php foreach ($daily_forecast as $day): ?>
                    <div class="forecast-card">
                        <h3><?php echo date('l, j M', $day['dt']); ?></h3>
                        <img src="http://openweathermap.org/img/wn/<?php echo $day['weather'][0]['icon']; ?>.png" 
                             alt="<?php echo $day['weather'][0]['description']; ?>" class="icon">
                        <div class="temp"><?php echo round($day['main']['temp']); ?>°C</div>
                        <p><?php echo ucfirst($day['weather'][0]['description']); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Could not load 5-day forecast. Please ensure your API key is correct.</p>
        <?php endif; ?>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; // Relative path ?>
</body>
</html>