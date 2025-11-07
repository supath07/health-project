<?php
/**
 * Site Footer Component
 * Provides consistent navigation and copyright info across all pages
 * Last updated: Nov 6, 2025
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Footer</title>
    <link rel="stylesheet" href="/web-dev/health-project/assets/css/a-style.css">
    <style>
        footer {
            padding: 1.5rem;
            text-align: center;
            background: #0f172a;
            color: #e2e8f0;
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .footer-links {
            display: flex;
            gap: 1.5rem;
        }

        .footer-links a {
            color: #e2e8f0;
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: #38bdf8;
        }

        @media (max-width: 768px) {
            .footer-content {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <footer>
        <div class="footer-content">
            <p>&copy; <?= date("Y")?> Health Advice Group. Making health advice accessible.</p>
            <div class="footer-links">
                <a href="/web-dev/health-project/pages/1-home.php">Home</a>
                <a href="/web-dev/health-project/pages/2-advice.php">Health Tips</a>
                <a href="/web-dev/health-project/pages/3-forecast.php">Weather</a>
            </div>
        </div>
    </footer>
</body>
</html>