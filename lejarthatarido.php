<?php

require './language/src/Exception.php';
require './language/src/PHPMailer.php';
require './language/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kutv3";


$deadlines = [];
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Fetch deadlines from database
    $stmt = $conn->prepare("SELECT forduloID, hatarido FROM versenyfordulo WHERE forduloID IN (1, 2)");
    $stmt->execute();
    $deadlineResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Organize deadlines by forduloID
    foreach ($deadlineResults as $result) {
        $deadlines[$result['forduloID']] = new DateTime($result['hatarido']);
    }
    
    date_default_timezone_set('Europe/Bucharest');
    $deadline = $deadlines[1];
    $deadline2 = $deadlines[2];
    $currentTime = new DateTime();

    // Format dates for display
    $formattedCurrentTime = $currentTime->format('Y-m-d H:i:s');
    $formattedDeadline = $deadline->format('Y-m-d H:i:s');
    $formattedDeadline2 = $deadline2->format('Y-m-d H:i:s');

    // Check if upload is past the deadline
    $isPastDeadline = $currentTime > $deadline;
    $isPastDeadline2 = $currentTime > $deadline2;

    $mail = new PHPMailer(true);
    if ($isPastDeadline){
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'mailbence74@gmail.com';
            $mail->Password = 'bwom gljb zzlf zmjk';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->CharSet = "UTF -8";

            // Recipients
            $mail->setFrom('mailbence74@gmail.com', 'Bence');
            $mail->addAddress('gyongyver.kovacs@econ.ubbcluj.ro');

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Lejart a hatarido';
            $mail->Body = "Lejart az elso fordulo hatarideje!";

            $mail->send();
            } catch (Exception $e) {
            echo "<div class='error-message'>Az üzenet nem küldhető el. Mailer Error: {$mail->ErrorInfo}</div>";
        }
    }
}
    catch (PDOException $e) {
        $_SESSION['upload_message'] = "Adatbázis hiba: " . $e->getMessage();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
    ?>