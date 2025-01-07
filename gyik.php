<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gyakran Ismételt Kérdések</title>
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
                <a href="./regisztracio.php">Regisztráció</a>
                <a href="./login.php">Bejelentkezés</a>
            </div>
        </nav>
    </header>

    <main>
        <div class="hero-container">
            <div class="hero-image" style="background-image: url('https://tinyurl.com/p12345asd');"></div>
            <div class="hero-overlay"></div>
            <div class="hero-text">
                <h1>Gyakran Ismételt Kérdések</h1>
                <p>Válaszok a leggyakrabban felmerülő kérdésekre</p>
            </div>
        </div>

        <div class="content faq-content">
            <div class="head1">
                <strong>Gyakran Ismételt Kérdések</strong>
            </div>

            <div class="faq">
                <h3>Hány fős kell legyen a csapat?</h3>
                <p>Minimum három diák kell részt vegyen, maximum négy. Amennyiben nem jön el a csapat harmadik tagja sem, a maradék két diák dönthet úgy, hogy visszalép a versenytől.</p>
            </div>

            <div class="faq">
                <h3>Jelentkezhetünk vezető tanár nélkül?</h3>
                <p>Nem, létfontosságú hogy legyen melletetek egy vezető tanár.</p>
            </div>

            <div class="faq">
                <h3>A verseny helyszínére kapunk szállítást, szállást?</h3>
                <p>Szállást biztosítunk az egyetem bentlakásában, ám a szállítást sajátkezűleg kell intéznie mindenkinek.</p>
            </div>
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

        document.querySelectorAll('.faq h3').forEach(question => {
            question.addEventListener('click', () => {
                const faq = question.parentElement;
                faq.classList.toggle('active');
                
                document.querySelectorAll('.faq').forEach(item => {
                    if (item !== faq) {
                        item.classList.remove('active');
                    }
                });
            });
        });
    </script>
</body>
</html>
