<?php
/**
 * Database Connection Manager
 * Establishes a secure connection to MySQL

 */

// Database credentials

$servername = "localhost";  //Local MySQL server
$username = "root";         //Default XAMPP username
$password = "";            //Blank for local development
$database = "health-1";    //Main application database

// Attempt database connection
// Uses try-catch for graceful error handling
try {
    $conn = mysqli_connect($servername, $username, $password, $database);
    
    if (!$conn) {
        throw new Exception(mysqli_connect_error());
    }
    
    // Set UTF-8 character encoding
    // This ensures proper handling of special characters
    mysqli_set_charset($conn, 'utf8mb4');
    
} catch (Exception $e) {
    // Log the error properly in production
    error_log("Database connection failed: " . $e->getMessage());
    
    // Show a user-friendly message
    die("Sorry, we can't reach our database right now. Please try again in a moment.");
}
?>