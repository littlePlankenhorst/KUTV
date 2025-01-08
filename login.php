<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kutv3";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $csapatnev = $_POST['csapatnev'];
        $password = $_POST['password'];
        
        // Hash the password using the same method as during registration
        $hashed_password = sha1($password);

        // Prepare and execute the SQL statement
        $stmt = $conn->prepare("SELECT * FROM csapat WHERE csapatNev = :csapatnev AND jelszo = :password");
        $stmt->bindParam(':csapatnev', $csapatnev);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->execute();

        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($stmt->rowCount() == 0) {
            echo "Rossz jelszo"; // Wrong password
        } else {
            $_SESSION["csapatnev"] = $csapatnev; // Set session variable
            $_SESSION["csapatID"] = $result['csapatID']; // Store csapatID in session
            $_SESSION["is_admin"] = ($csapatnev === 'admin'); // Check if the user is admin
            header("Location: index.php"); // Redirect to index.php
            exit();
        }
    }
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KÜTV</title>
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
                <a href="./regisztracio.php">Regisztráció</a>
            </div>
        </nav>
    </header>
    <main>
        <div class="hero-container">
            <div class="hero-image" style="background-image: url('https://tinyurl.com/p12345asd');"></div>
            <div class="hero-overlay"></div>
            <div class="hero-text">
                <h1>Bejelentkezés</h1>
                <p>Add meg a csapatod adatait!</p>
            </div>
        </div>
        <div class="login-container">
            <form class="login-form" method="post">
                <div class="form-group">
                    <label for="csapatnev">Csapat neve</label>
                    <input type="text" id="csapatnev" name="csapatnev" required>
                </div>
                <div class="form-group">
                    <label for="password">Jelszó</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="button-wrapper">
                    <button type="submit" class="submit-btn">Bejelentkezés</button>
                </div>
                <div class="form-footer">
                    <p>Még nincs fiókod? <a href="./regisztracio.php">Regisztrálj itt!</a></p>
                    <p><a href="./forgot_password.php">Elfelejtetted a jelszavad?</a></p>
                </div>
                <?php if (isset($error_message)): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>
            </form>
        </div>
    </main>
    <script>
        function toggleMenu() {
            const navButtons = document.querySelector('.nav-buttons');
            navButtons.classList.toggle('active');
        }
    </script>
</body>
</html>
