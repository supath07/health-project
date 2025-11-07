<?php
session_start();
include '/xampp/htdocs/web-dev/health-project/includes/db_connect.php';

// redirect to sign-in if user not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: 5-sign-in.php');
    exit;
}

$user_id   = $_SESSION['user_id'];
$full_name = $_SESSION['full_name'];

// handle new log submission
$log_message = '';
$log_message_type = 'info';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_log'])) {
    $symptom  = trim($_POST['symptom']  ?? '');
    $details  = trim($_POST['details']  ?? '');
    $log_date = trim($_POST['log_date'] ?? '');

    if ($symptom !== '' && $log_date !== '') {
        $sql_insert = 'INSERT INTO health_logs (user_id, symptom, details, log_date) VALUES (?, ?, ?, ?)';
        $stmt = mysqli_prepare($conn, $sql_insert);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'isss', $user_id, $symptom, $details, $log_date);

            if (mysqli_stmt_execute($stmt)) {
                $log_message = 'Log added successfully!';
                $log_message_type = 'success';
            } else {
                $log_message = 'Error: Could not add log. ' . mysqli_stmt_error($stmt);
                $log_message_type = 'error';
            }

            mysqli_stmt_close($stmt);
        } else {
            $log_message = 'Error: Failed to prepare statement. ' . mysqli_error($conn);
            $log_message_type = 'error';
        }
    } else {
        $log_message = 'Error: Symptom and Date are required.';
        $log_message_type = 'error';
    }
}

// fetch existing logs
$logs = [];
$sql_fetch = 'SELECT * FROM health_logs WHERE user_id = ? ORDER BY log_date DESC, created_at DESC LIMIT 10';
$stmt_fetch = mysqli_prepare($conn, $sql_fetch);

if ($stmt_fetch) {
    mysqli_stmt_bind_param($stmt_fetch, 'i', $user_id);
    mysqli_stmt_execute($stmt_fetch);
    $result_logs = mysqli_stmt_get_result($stmt_fetch);

    if ($result_logs) {
        while ($row = mysqli_fetch_assoc($result_logs)) {
            $logs[] = $row;
        }
        mysqli_free_result($result_logs);
    }

    mysqli_stmt_close($stmt_fetch);
}

// --- API calls moved to a-script.js ---
// This makes the page load faster and use the user's geolocation
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../assets/css/a-style.css">
    <style>
        /* Styles for the log message */
        .log-message { margin-bottom: 1rem; padding: .5rem; border-radius: 4px; }
        .log-message.success { background: #d1fae5; color: #064e3b; }
        .log-message.error   { background: #fee2e2; color: #7f1d1d; }
    </style>
</head>
<body>
    <?php include '/xampp/htdocs/web-dev/health-project/includes/header.php' ?>

    <main>
        <div class="welcome-banner dash-card">
            <h2>Hello, <?php echo htmlspecialchars($full_name, ENT_QUOTES, 'UTF-8'); ?></h2>
            <p>Here's your health snapshot for today.</p>
        </div>

        <div class="dashboard-container">
            <div class="dash-card">
                <h3>Log New Symptom</h3>

                <?php if (!empty($log_message)): ?>
                    <div class="log-message <?php echo htmlspecialchars($log_message_type); ?>">
                        <?php echo htmlspecialchars($log_message); ?>
                    </div>
                <?php endif; ?>

                <form action="7-dashboard.php" method="POST" class="log-form">
                    <input type="hidden" name="add_log" value="1">

                    <div>
                        <label for="symptom">Symptom:</label>
                        <input type="text" id="symptom" name="symptom" placeholder="e.g. Headache, Cough" required>
                    </div>

                    <div>
                        <label for="log_date">Date:</label>
                        <input type="date" id="log_date" name="log_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>

                    <div>
                        <label for="details">Details (optional):</label>
                        <textarea name="details" id="details" placeholder="e.g. Pain behind eyes..."></textarea>
                    </div>

                    <button type="submit">Add Log</button>
                </form>
            </div>

            <div class="dash-card log-list">
                <h3>My Recent Health Logs</h3>
                <form id="clear-form">
                    <?php if (empty($logs)): ?>
                        <p>No logs yet. Add your first symptom above.</p>
                    <?php else: ?>
                        <ul>
                            <?php foreach ($logs as $log): ?>
                                <li>
                                    <small><?php echo htmlspecialchars(date('D, j M Y', strtotime($log['log_date']))); ?></small>
                                    <strong><?php echo htmlspecialchars($log['symptom']); ?></strong>
                                    <?php if (!empty($log['details'])): ?>
                                        <p><?php echo htmlspecialchars($log['details']); ?></p>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </form>


                <!-- <script>
                    document.addEventListener('DOMContentLoaded', function(){
                        var form= document.getElementById('clear-form');
                        if(form) {

                            var clearButton= document.createElement('button');
                            clearButton.type= 'button';
                            clearButton.innerText= 'Clear';
                             clearButton.class= 'custom-btn';
                            clearButton.style.marginTop= '10px';

                                clearButton.classList.add('custom-clear-button');
                            
                            form.appendChild(clearButton);

                            clearButton.addEventListener('click', function() {
                                var fields= form.querySelectorAll('input', 'textarea', 'select');
                                field.ForEach(function(field) {
                                    if(field.type === 'checkbox' || field.type === 'radio'){
                                        field.checked= false;
                                    } else {
                                        field.value= '';
                                    }
                                });
                            });
                        }
                    });
                </script> -->

                
            </div>

            <div class="dash-card" id="aqi-card">
                <h3>Air Quality (AQI)</h3>
                <p class="loading-text">Loading local air quality...</p>
            </div>

            <div class="dash-card" id="weather-card">
                <h3>Weather</h3>
                <p class="loading-text">Loading local weather...</p>
            </div>
        </div>

    </main>

    <?php include '/xampp/htdocs/web-dev/health-project/includes/footer.php'; ?>
    <script src="../assets/js/weather.js"></script>
</body>
</html>