<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Általános szabályok</title>
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
                <h1>Általános szabályok</h1>
                <p>Fontos információk a résztvevők számára</p>
            </div>
        </div>

        <section class="rules-content">
            <h2>Szabályzat</h2>
            <p>Itt található a részletes szabályzat szövege. Ez a rész tartalmazhat több bekezdést is, amelyek részletezik a különböző szabályokat és irányelveket.</p>
            
            <p>A versenyre magyar anyanyelvű középiskolás diákok jelentkezhetnek, 3 fős csapatokat alkotva.<br>
                o    Iskolánként a jelentkező csapatok száma nincs korlátozva. <br>
                o    A versenyre való jelentkezés egy jelentkezési űrlap kitöltésével történik, ami elérhető a következő linken  <a href="./regisztracio.html" style="text-decoration: none;">REGISZTRÁCIÓ</a>. <br>
                o    A jelentkezési határidő minden csapat számára kötelező, utólagos jelentkezéseket nem áll módunkban elfogadni. <br>
                o    Az online forduló és a döntő is tartalmaz saját szabályzatot, a speciálisan arra a fordulóra jellemző problémákkal. <br>
                o    Az egyes fordulók beküldési határidejének betartása kötelező minden csapat számára. <br>
                o    Az egyes fordulókra beküldött munkák kiértékelése és az eredmények közlése a mellékelt ütemterv szerint történik. <br>  
                o    A zsűri tagjai kizárólag gyakorlati szakemberek. <br>
                o    Az egyes fordulókon elért eredményről és a továbbjutásról a csapat kapcsolattartó személyét e-mailben értesítjük a jelentkezéskor megadott e-mail címen. <br>
                o    A második fordulóban a csapatok először iskolán belül, majd az iskolák egymás között versenyeznek. Egy iskolából alapesetben csak egy csapat kerül a döntőbe, de kiemelkedő teljesítmény alapján a selejtező forduló zsűrije dönthet maximum két csapat továbbjutásáról is egy adott iskolából. Nem kötelező minden iskolából bekerülnie csapatnak a döntőbe. <br>
                o    Egy „zöld kártyás”, szabad helyet biztosítunk a döntőben annak a csapatnak, amely iskolájából egy csapat sem érte el ugyan a továbbjutást, de az adott csapat a legjobban teljesített az online fordulóban azon nem továbbjutó iskolák csapatai közül, amelyek a tavaly nem vettek részt a kolozsvári döntőben.<br
                o    Ha egy iskolából közeli pontszámmal versengenek csapatok a döntőbe jutásért, a kiválasztásnál kikérjük a csapatokat irányító tanár(ok) véleményét.
                o    A szervezők a szabályzat változtatásának jogát fenntartják, a szabályzat esetleges változásairól a csapatokat értesítik (a változások csak a verseny zökkenőmentes lebonyolításának célját szolgálhatják és csak olyan változások történhetnek, amelyek nem érintik hátrányosan egyik csapatot sem).</p>
        </section>
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
