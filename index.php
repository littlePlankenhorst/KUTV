<?php
session_start();
?>
<!DOCTYPE html>
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
                <a href="./hirek.php">Hírek</a>
                <a href="./infok.php">Általános információk</a>
                <a href="./szabaly.php">Általános szabályok</a>
                <a href="./gyik.php">Gyakori kérdések</a>
                <?php if (isset($_SESSION['csapatnev'])): ?>
                    <?php if ($_SESSION['csapatnev'] == 'admin'): ?>
                        <a href="./admin.php">Admin Panel</a>
                    <?php endif; ?>
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

    <div class="full-screen-container" id="home">
        <div class="intro-content">
            <h1>XVII. KÖZÉPISKOLÁSOK ÜZLETI TANÁCSADÓ VERSENYE</h1>
            <p>A jövő a tied!</p>
        </div>
        <div class="image-container">
            <img src="./images/logo.jpg" alt="Students" class="placeholder-image">
        </div>
    </div>

    <main>
        <section id="about">
            <h2>A verseny célja</h2>
            <p>A Középiskolások üzleti Tanácsadó Versenye magyar anyanyelvű középiskolás diákoknak szervezett gazdasági verseny, amit a Babeș–Bolyai Tudományegyetem Közgazdaság- és Gazdálkodástudományi Karának magyar tagozata szervez. Célunk a közgazdász szakma rejtélyeibe betekintést nyújtani a pályaválasztás előtt álló fiatalok számára. Ennek megfelelően, egyszerű, gyakorlatias és szórakoztató tanulási módszerek segítségével építettük fel a Középiskolások üzleti Tanácsadó Versenyét. A verseny alatt a diákok megismerkedhetnek a piackutatás területével és egy esettanulmány megoldásának kihívásaival.</p>
        </section>
    </main>

    <section class="sponsors">
        <h2>Szponzoraink</h2>
        <div class="sponsor-container">
            <div class="sponsor"><img src="https://hu.econ.ubbcluj.ro/kutv/assets/images/babes.png" alt="Babes-Bolyai Egyetem"></div>
            <div class="sponsor"><img src="https://hu.econ.ubbcluj.ro/kutv/assets/images/kutv.jpg" alt="Középiskolások üzleti Tanácsadó Versenye"></div>
            <div class="sponsor"><img src="https://hu.econ.ubbcluj.ro/kutv/assets/images/fsega.png" alt="Fővárosi Gazdasági és Kereskedelmi Szövetkezet"></div>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 My Website. All rights reserved.</p>
    </footer>

    <script>
        function toggleMenu() {
            const navButtons = document.querySelector('.nav-buttons');
            navButtons.classList.toggle('active');
        }
    </script>
</body>
</html>
