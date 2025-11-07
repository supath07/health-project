<?php
# Health Logs Clear Handler
# This script safely removes recent health logs for the current user
# Written by: Your Name
# Last updated: Nov 1, 2025

session_start();
include '/xampp/htdocs/web-dev/health-project/includes/db_connect.php';

# We'll be sending back JSON responses
header('Content-Type: application/json');

# Safety first - make sure the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode([
        'success' => false, 
        'message' => 'Please sign in to manage your health logs'
    ]);
    exit;
}

# Get the current user's ID
$user_id = $_SESSION['user_id'];

# We'll only clear recent logs (last 7 days)
# This keeps older records for long-term health tracking
$sql = "DELETE FROM health_logs 
        WHERE user_id = ? 
        AND log_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)";

# Prepare and execute the query safely
try {
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        throw new Exception('Failed to prepare database query');
    }
    
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to clear logs');
    }
    
    # All good! Let the user know it worked
    echo json_encode([
        'success' => true,
        'message' => 'Your recent health logs have been cleared'
    ]);
    
} catch (Exception $e) {
    # Something went wrong - let's tell the user
    echo json_encode([
        'success' => false,
        'message' => 'Sorry, we couldn\'t clear your logs. Please try again.'
    ]);
    
    # In a production app, we'd log this error properly
    error_log("Clear logs error: " . $e->getMessage());
} finally {
    # Always clean up
    if (isset($stmt)) {
        mysqli_stmt_close($stmt);
    }
}