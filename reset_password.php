<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kutv3";

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
    <title>Jelszó visszaállítása</title>
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
            <h1>Jelszó visszaállítása</h1>
            <form method="post" action="update_password.php?token=<?php echo htmlspecialchars($token); ?>" class="login-form">
                <div class="form-group">
                    <label for="password">Új jelszó:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="button-wrapper">
                    <button type="submit" class="submit-btn">Jelszó visszaállítása</button>
                </div>
            </form>
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
</body>
</html> 