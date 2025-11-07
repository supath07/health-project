<?php
# Home Page
# Our welcoming landing page with key features and information
# Written by: Your Name
# Last updated: Nov 1, 2025

session_start();
include '/xampp/htdocs/web-dev/health-project/includes/db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Welcome to Health Advice Group - Your trusted source for personalized health advice and local weather information">
    <title>Health Advice Group - Your Wellness Partner</title>
    <link rel="stylesheet" href="../assets/css/a-style.css">
</head>
<body>
    <?php include '/xampp/htdocs/web-dev/health-project/includes/header.php'; ?>

    <div class="sub-head">
        <h1>Welcome to Health Advice Group</h1>
        <p>Your trusted partner for personalized health advice and local weather insights</p>
    </div>

    <main>
        <section class="intro">
            <h2>Why Choose Us?</h2>
            <p>
                We understand that your health is influenced by many factors - including the weather 
                and environment around you. That's why we combine expert health advice with real-time 
                weather data to give you personalized recommendations that make sense for your situation.
            </p>
            <p>
                Whether you're managing seasonal allergies, planning outdoor activities, or just want 
                to stay healthy, we're here to help you make informed decisions about your wellbeing.
            </p>
        </section>

        <section class="features">
            <h2>What We Offer</h2>
            <ul class="features-list">
                <li>
                    <a href="/web-dev/health-project/pages/2-advice.php">
                        Get Personal Health Tips
                    </a>
                    - Tailored advice based on your health profile and local conditions
                </li>
                <li>
                    <a href="/web-dev/health-project/pages/3-forecast.php">
                        Check Weather Forecast
                    </a>
                    - Stay informed with our detailed 5-day weather predictions
                </li>
                <li>
                    <a href="/web-dev/health-project/pages/7-dashboard.php">
                        Monitor Air Quality
                    </a>
                    - Real-time air quality data to help you breathe easier
                </li>
            </ul>
        </section>
    </main>

    <?php include '/xampp/htdocs/web-dev/health-project/includes/footer.php';?>
</body>
</html>