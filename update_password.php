<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kutv1";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $token = $_GET['token'];
        $newPassword = $_POST['password'];
        $hashedPassword = sha1($newPassword); // Use the same hashing method

        // Check if the token is valid and not expired
        $stmt = $conn->prepare("
            SELECT c.csapatID, t.email 
            FROM tanar t 
            JOIN csapat c ON t.tanarID = c.tanarID 
            WHERE t.reset_token = :token AND t.reset_expires > :now
        ");
        $now = date("U");
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':now', $now);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Fetch the csapatID to update the password using the teacher's email
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $email = $row['email']; // Get the teacher's email

            // Update the password in the csapat table and reset the token in the tanar table
            $stmt = $conn->prepare("
                UPDATE csapat 
                SET jelszo = :password 
                WHERE csapatID = (SELECT csapatID FROM csapat WHERE tanarID = (SELECT tanarID FROM tanar WHERE email = :email));
            ");
            // Update the tanar table to reset the token and expiration
            $stmt2 = $conn->prepare("
                UPDATE tanar 
                SET reset_token = NULL, reset_expires = NULL 
                WHERE email = :email
            ");
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':email', $email); // Bind the email parameter
            $stmt->execute();
            $stmt2->bindParam(':email', $email); // Bind the email parameter for the second statement
            $stmt2->execute();

            echo "Your password has been reset successfully.";
            echo '<button onclick="window.location.href=\'login.php\'">Go to Login</button>';
        } else {
            echo "This token is invalid or has expired.";
        }
    }
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?> 