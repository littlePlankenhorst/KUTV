<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elfelejtett jelszó</title>
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
                <a href="./login.php">Bejelentkezés</a>
            </div>
        </nav>
    </header>

    <main>
        <div class="login-container">
            <h1>Elfelejtett jelszó</h1>
            <form method="post" action="send_reset_link.php" class="login-form">
                <div class="form-group">
                    <label for="email">Add meg az email címed:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="button-wrapper">
                    <button type="submit" class="submit-btn-forgot">Visszaállítási link küldése</button>
                </div>
                <div class="form-footer">
                    <p>Vissza a <a href="login.php">bejelentkezéshez</a></p>
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
<style>
    button[type="submit"] {
    display: block;
    width: 100%;
    margin-left:1px;
    margin-top:30px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 18px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}
</style>
</html> 