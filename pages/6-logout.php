<?php
# Sign Out Handler
# This script safely signs out users and redirects them home
# Written by: Your Name
# Last updated: Nov 1, 2025

session_start();

# Clear all session data
# This ensures no sensitive info sticks around
$_SESSION = array();

# Clean up the session cookie if it exists
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

# Destroy the session completely
session_destroy();

# Send them back to the homepage
# They'll need to sign in again to access protected pages
header("Location: 1-home.php");
exit();
?>