<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kutv1";

if (isset($_GET['token'])) {
    $token = $_GET['token'];
} else {
    die("No token provided.");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <h1>Reset Password</h1>
    <form method="post" action="update_password.php?token=<?php echo htmlspecialchars($token); ?>">
        <label for="password">New Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html> 