<?php
# Sign In Handler
# This script securely handles user authentication
# Written by: Your Name
# Last updated: Nov 1, 2025

session_start();
include '/xampp/htdocs/web-dev/health-project/includes/db_connect.php';

# Store any messages for the user here
$message = '';

# Process sign in attempts
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    # Users can sign in with either username or email
    $login_identifier = trim($_POST['login']);
    $password = $_POST['password'];

    # Look up the user's account securely
    $sql = "SELECT * FROM `user` WHERE `username` = ? OR `email` = ?";
    try {
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            throw new Exception("Couldn't prepare database query");
        }

        mysqli_stmt_bind_param($stmt, "ss", $login_identifier, $login_identifier);
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Couldn't execute query");
        }

        $result = mysqli_stmt_get_result($stmt);

        # Did we find exactly one matching user?
        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            # Verify their password
            if (password_verify($password, $user['password'])) {
                # Success! Set up their session
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];

                # Send them to their dashboard
                header("Location: 7-dashboard.php");
                exit();
            }
        }

        # If we got here, login failed
        $message = "Sorry, those credentials don't match our records. Please try again.";

    } catch (Exception $e) {
        # Log the error (in a real app)
        error_log("Sign in error: " . $e->getMessage());
        $message = "We couldn't sign you in right now. Please try again later.";
    } finally {
        if (isset($stmt)) {
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign-In Form</title>
    <link rel="stylesheet" href="../assets/css/a-style.css">
</head>
<body>
    <?php include '/xampp/htdocs/web-dev/health-project/includes/header.php'; ?>

    <div class="login-container register-container">
        <h2>Welcome back</h2>

        <?php
        if(!empty($message)) {
            echo "<p id='message'>" . htmlspecialchars($message) . "</p>";
        }
        ?>
        <form action="5-sign-in.php" method="POST">
            <label for="login">Username or Email</label>
            <input type="text" id="login" name="login" placeholder="Enter your username or email..." required><br>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password..." required><br>
            <button type="submit">Log In</button>
        </form>
        <p class="login-label" >
            Don't have an account? <a href="4-register.php">Register</a>
        </p>
    </div>

    <?php include '/xampp/htdocs/web-dev/health-project/includes/footer.php'?>

</body>
</html>