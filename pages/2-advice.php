<?php
session_start();
include '/xampp/htdocs/web-dev/health-project/includes/db_connect.php';

//if logged in is not set in the session will just redirect them to sign in page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header ("Location: 5-sign-in.php");
    exit;
}

//after the user is logged in
//load their info
$user_id = $_SESSION["user_id"];
$full_name= $_SESSION['full_name'];


//fetch recent health logs
//getting all logs from the last 7 days to generate advice
$recent_logs= [];
$sql_logs= "SELECT symptom, log_date FROM health_logs
            WHERE user_id = ? AND log_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            ORDER BY log_date DESC";

//statements
$stmt_logs= mysqli_prepare($conn, $sql_logs);
mysqli_stmt_bind_param($stmt_logs, 'i', $user_id);
mysqli_stmt_execute($stmt_logs);
$result= mysqli_stmt_get_result($stmt_logs);

if($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $recent_logs[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Health Advice | Health Advice Group</title>
    <link rel="stylesheet" href="../assets/css/a-style.css">
</head>
<body>
    <?php include '/xampp/htdocs/web-dev/health-project/includes/header.php' ?>

    <main class="card-container">
        <div class="welcome-banner">
            <h2>Hello, <?php echo htmlspecialchars($full_name); ?></h2>
            <p>Here's your personalized health advice based on your recent logs and local environment</p>
        </div>

        <div class="card" id="advice-container">
            <h3>Your Health Tips</h3>
            <div class="content-section">
                <p class="loading-text">Generating advice based on your location and logs...</p>
            </div>
        </div>

        <div class="grid-container">
            <div class="card" id="my-logs-card">
                <h4>
                    Your Recent Logs (7 days)
                    <button class="action-btn danger-btn" onclick="clearHealthLogs()" title="Clear all logs">
                        Clear
                    </button>
                </h4>
                <div class="content-section">
                    <p class="loading-text">Loading your health logs...</p>
                </div>
            </div>

            <div class="card" id="advice-weather-card">
                <h4>Your Local Weather</h4>
                <p class="loading-text">Loading weather...</p>
            </div>

            <div class="card" id="advice-aqi-card">
                <h4>Your Local Air Quality</h4>
                <p class="loading-text">Loading AQI...</p>
            </div>
        </div>
    </main>

    <?php include '/xampp/htdocs/web-dev/health-project/includes/footer.php'; ?>

    <script>
        window.recentHealthLogs= <?php echo json_encode($recent_logs); ?>;
    </script>
    <script src="../assets/js/weather.js"></script>
</body>
</html>