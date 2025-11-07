<?php
/**
 * Main Navigation Header
 * Displays the site logo and main menu
 * Adjusts menu items based on login status
 * Last updated: Nov 6, 2025
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Health Advice Group - Get personalized health tips and weather forecasts">
    <title>Health Advice Group</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/web-dev/health-project/assets/css/a-style.css">
</head>
<body>
    <header>
        <a href="/web-dev/health-project/pages/1-home.php" class="logo-link" aria-hidden="true">
            <span>Health Advice Group</span>
        </a>
        <nav>
            <ul>
                <li><a href="/web-dev/health-project/pages/1-home.php">Home</a></li>
                <li><a href="/web-dev/health-project/pages/2-advice.php">Health Tips</a></li>
                <li><a href="/web-dev/health-project/pages/3-forecast.php">Weather</a></li>
                
                <?php if(!empty($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                    <li><a href="/web-dev/health-project/pages/7-dashboard.php">My Dashboard</a></li>
                    <li><a href="/web-dev/health-project/pages/6-logout.php" class="nav-logout">Sign Out</a></li>
                <?php else: ?>
                    <li><a href="/web-dev/health-project/pages/4-register.php" class="nav-highlight">Join Us</a></li>
                    <li><a href="/web-dev/health-project/pages/5-sign-in.php">Sign In</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
</body>
</html>