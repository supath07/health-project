<?php
# 5-Day Weather Forecast
# Shows detailed weather predictions to help plan your week
# Written by: Your Name
# Last updated: Nov 1, 2025

session_start();
include '/xampp/htdocs/web-dev/health-project/includes/db_connect.php';

# Members only - redirect guests to sign in
// if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
//     header("Location: 5-sign-in.php");
//     exit;
// }

// # Get the user's name for personalisation
$full_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'User';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="5-day weather forecast to help you plan your week and stay healthy">
    <title>Your Weekly Weather Forecast - Health Advice Group</title>
    <link rel="stylesheet" href="../assets/css/a-style.css">
</head>
<body>
    <?php include '/xampp/htdocs/web-dev/health-project/includes/header.php' ?>

    <main class="forecast-container" id="forecast-container">
        <div class="welcome-banner">
            <h2>Your 5-Day Weather Forecast</h2>
            <p>Hello <?php if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true){
                echo htmlspecialchars($full_name);
            } else {
                echo "User";
            } ?>, here's what to expect in the days ahead.</p>
        </div> 
        
        <div id="location-name"></div>
        
        <div class="forecast-grid" id="forecast-grid">
            <p class="loading-text">
                Just a moment while we get your local forecast...
            </p>
        </div>
        
        <div id="forecast-error" style="display: none;" class="error-banner">
            <h3>Oops! We're Having Trouble</h3>
            <p class="error-message">
                We need access to your location to show accurate weather forecasts.
                Please enable location services in your browser and refresh the page.
            </p>
            <button onclick="location.reload()" class="retry-btn">
                Try Again
            </button>
        </div>
    </main>

    <script src="../assets/js/forecast.js"></script>
    <?php include '/xampp/htdocs/web-dev/health-project/includes/footer.php'; ?>
</body>
</html>