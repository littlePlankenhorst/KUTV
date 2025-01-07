<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hírek</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo"><img src="https://hu.econ.ubbcluj.ro/kutv/assets/images/logo.png" alt="LOGO"></div>
            <div class="menu-toggle" onclick="toggleMenu()">☰</div>
            <div class="nav-buttons">
                <a href="./index.php">Főoldal</a>
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
                <h1>Hírek</h1>
                <p>Itt jelennek meg a legfrissebb hírek és közlemények.</p>
            </div>
        </div>

        <div class="news-container">
            <div class="news-item">
                <img src="https://tinyurl.com/p23456asd" alt="Hír 1">
                <h2>Négy alapképzéses diák kijutott Rotterdamba!</h2>
            </div>
            <div class="news-item">
                <img src="https://tinyurl.com/p34567asd" alt="Hír 2">
                <h2>Az Üzleti és pénzügyi adattan mesteri programunk épp rád vár!</h2>
            </div>
            <div class="news-item">
                <img src="https://tinyurl.com/as666d123" alt="Hír 3">
                <h2>Konferencia: A középiskolások üzleti tanácsadó versenye</h2>
            </div>
            <!-- Add more news items as needed -->
        </div>
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
