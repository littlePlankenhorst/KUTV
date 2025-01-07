<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['csapatnev']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php"); // Redirect to login page if not admin
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kutv1";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all relevant data from the database
    $stmt = $conn->prepare("
        SELECT 
            c.csapatNev, 
            i.iskolaNev, 
            CONCAT(t.vezetekNev, ' ', t.keresztNev) AS tanarNev,
            GROUP_CONCAT(CONCAT(v.vezetekNev, ' ', v.keresztNev) SEPARATOR ', ') AS Versenyzok,
            CASE 
                WHEN f.csapatID IS NOT NULL THEN '✔' 
                ELSE '✖' 
            END AS Status
        FROM 
            csapat c
        JOIN 
            iskola i ON c.iskolaID = i.iskolaID
        JOIN 
            tanar t ON c.tanarID = t.tanarID
        LEFT JOIN 
            versenyzo v ON c.csapatID = v.csapatID
        LEFT JOIN 
            fordulomegoldasa f ON c.csapatID = f.csapatID
        GROUP BY 
            c.csapatID
    ");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KÜTV Admin</title>
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
                <a href="./upload.php">Feltöltés</a>
                <a href="./logout.php">Kijelentkezés</a>
                <?php if (isset($_SESSION['csapatnev'])): ?>
                    <span>Üdvözöljük: <?php echo htmlspecialchars($_SESSION['csapatnev']); ?></span>
                <?php endif; ?>
            </div> 
        </nav>
    </header>

    <main>
        <h1>Admin Panel</h1>
        <div class="info-content">
            <table class="info-table">
                <thead>
                    <tr>
                        <th>Csapatnév</th>
                        <th>Iskola Név</th>
                        <th>Tanár Név</th>
                        <th>Versenyzők</th>
                        <th>Státusz</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['csapatNev']); ?></td>
                        <td><?php echo htmlspecialchars($row['iskolaNev']); ?></td>
                        <td><?php echo htmlspecialchars($row['tanarNev']); ?></td>
                        <td><?php echo htmlspecialchars($row['Versenyzok']); ?></td>
                        <td><?php echo htmlspecialchars($row['Status']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

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
