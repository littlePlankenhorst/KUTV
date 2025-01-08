<?php
session_start();
require './language/src/Exception.php';
require './language/src/PHPMailer.php';
require './language/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jelszó visszaállítás</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo"><img src="https://hu.econ.ubbcluj.ro/kutv/assets/images/logo.png" alt="LOGO"></div>
            <div class="menu-toggle" onclick="toggleMenu()">☰</div>
            <div class="nav-buttons">
                <a href="./index.php">Főoldal</a>
                <a href="./hirek.php">Hírek</a>
                <a href="./infok.php">Általános információk</a>
                <a href="./szabaly.php">Általános szabályok</a>
                <a href="./gyik.php">Gyakori kérdések</a>
                <?php if (isset($_SESSION['csapatnev'])): ?>
                    <a href="./upload.php">Feltöltés</a>
                    <a href="./logout.php">Kijelentkezés</a>
                    <span class="welcome-message"><?php echo htmlspecialchars($_SESSION['csapatnev']); ?></span>
                <?php else: ?>
                    <a href="./regisztracio.php">Regisztráció</a>
                    <a href="./login.php">Bejelentkezés</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main>
        <div class="login-container">
            <h1>Jelszó visszaállítás</h1>
            <div class="form-group">
                <?php
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "kutv3";

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
                                $mail->Host = 'smtp.gmail.com';
                                $mail->SMTPAuth = true;
                                $mail->Username = 'mailbence74@gmail.com';
                                $mail->Password = 'bwom gljb zzlf zmjk';
                                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                                $mail->Port = 587;

                                // Recipients
                                $mail->setFrom('mailbence74@gmail.com', 'Bence');
                                $mail->addAddress($email);

                                // Content
                                $mail->isHTML(true);
                                $mail->Subject = 'Jelszó visszaállítási kérelem';
                                $resetLink = "http://localhost/KUTV1/reset_password.php?token=" . $token;
                                $mail->Body = "Kattints a linkre a jelszavad visszaállításához: <a href='" . $resetLink . "'>" . $resetLink . "</a>";

                                $mail->send();
                                echo "<div class='success-message'>A visszaállítási link elküldve az email címedre.</div>";
                            } catch (Exception $e) {
                                echo "<div class='error-message'>Az üzenet nem küldhető el. Mailer Error: {$mail->ErrorInfo}</div>";
                            }
                        } else {
                            echo "<div class='error-message'>Nem található fiók ezzel az email címmel.</div>";
                        }
                    }
                } catch(PDOException $e) {
                    echo "<div class='error-message'>Kapcsolódási hiba: " . $e->getMessage() . "</div>";
                }
                ?>
            </div>
            <div class="form-footer">
                <p>Vissza a <a href="login.php">bejelentkezéshez</a></p>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Szervezők. Minden jog fenntartva.</p>
    </footer>

    <script>
        function toggleMenu() {
            const navButtons = document.querySelector('.nav-buttons');
            navButtons.classList.toggle('active');
        }
    </script>

    <style>
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            margin: 10px 0;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            text-align: center;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            margin: 10px 0;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            text-align: center;
        }
    </style>
</body>
</html> 