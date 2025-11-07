
<?php
session_start();
include '/xampp/htdocs/web-dev/health-project/includes/db_connect.php';

//creating a variable to store messages
$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name= $_POST['name'];
    $username= $_POST['username'];
    $email= $_POST['email'];
    $password= $_POST['password'];

    //checking if username or email already exists
    $sql_check= "SELECT * FROM `user` WHERE `username` = ? OR `email` = ?";
    $stmt_check= mysqli_prepare ($conn, $sql_check); //this is to prevent SQL injection
    mysqli_stmt_bind_param($stmt_check, "ss", $username, $email);
    mysqli_stmt_execute($stmt_check);
    $result_check= mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows ($result_check) >0) {
        //print out user already exists
        $message= "Error: Username or Email already exists. Please try another one";
    } else {
        //since user does not exist, proceed to registration

        //then hash the password
        $cpassword= password_hash ($password, PASSWORD_DEFAULT);

        //insertin user into database
        $sql_insert= "INSERT INTO `user` (`full_name`, `username`, `email`, `password`) VALUES (?, ?, ?, ?)";
        $stmt_insert= mysqli_prepare ($conn, $sql_insert);
        mysqli_stmt_bind_param($stmt_insert, "ssss", $name, $username, $email, $cpassword);

        if (mysqli_stmt_execute($stmt_insert)) {
            //print out registration success message
            $message = "Registration successful! You can now <a href='5-sign-in.php'>Sign in</a>.";
        } else {
            $message = 'Error: Could not register. Please try again bro';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>|Registration Page|</title>
    <link rel="stylesheet" href="../assets/css/a-style.css">
</head>
<body>
    <?php include '/xampp/htdocs/web-dev/health-project/includes/header.php'; ?>
    
    <div class="register-container">
        <h2>Register for Health Advice Group</h2>

        <?php if (!empty($message))
            {echo "<p id='message'>" . ($message) . "</p>";}
        ?>
        <form action="4-register.php" method="POST">
            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" placeholder="Enter your Full Name" required> <br>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Create a Username" required> <br>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" placeholder="Enter your email..." required> <br>

            <label for="password">Password: </label>
            <input type="password" id="password" name="password" placeholder="Create a strong Password..." required> <br>

            <button type="submit">Register</button>
        </form>
        <p class="signup-label">
            Already have an account? <a href="5-sign-in.php">Sign in</a>
        </p>
        
    </div>

    <?php include '/xampp/htdocs/web-dev/health-project/includes/footer.php'?>

</body>
</html>