<?php
session_start();
require './language/src/Exception.php';
require './language/src/PHPMailer.php';
require './language/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kutv1";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = $_POST['email'];

        // Check if the email exists in the database by joining csapat and tanar
        $stmt = $conn->prepare("
            SELECT c.jelszo, t.email 
            FROM csapat c 
            JOIN tanar t ON c.tanarID = t.tanarID 
            WHERE t.email = :email
        ");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Generate a unique token
            $token = bin2hex(random_bytes(10));
            $expires = date("U") + 3600; // Token valid for 1 hour

            // Update the tanar's record with the token and expiration
            $stmt = $conn->prepare("UPDATE tanar SET reset_token = :token, reset_expires = :expires WHERE email = :email");
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':expires', $expires);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            // Send the reset email using PHPMailer
            $mail = new PHPMailer(true);
            try {
                //Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
                $mail->SMTPAuth = true; // Enable SMTP authentication
                $mail->Username = 'mailbence74@gmail.com'; // Your Gmail address
                $mail->Password = 'bwom gljb zzlf zmjk'; // Your Gmail password or App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
                $mail->Port = 587; // TCP port to connect to

                // Recipients
                $mail->setFrom('mailbence74@gmail.com', 'Bence'); // Sender's email and name
                $mail->addAddress($email); // Add a recipient

                // Content
                $mail->isHTML(true); // Set email format to HTML
                $mail->Subject = 'Password Reset Request';
                $resetLink = "http://localhost/KUTV1/reset_password.php?token=" . $token; // Change to your domain
                $mail->Body = "Click the link to reset your password: <a href='" . $resetLink . "'>" . $resetLink . "</a>";

                $mail->send(); // Send the email
                echo "Reset link has been sent to your email.";
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "No account found with that email address.";
        }
    }
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?> 