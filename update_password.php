<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kutv3";

$message = ""; // Initialize message variable

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['token'])) {
        $token = $_GET['token'];
    } else {
        die("No token provided.");
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $newPassword = $_POST['password'];
        $hashedPassword = sha1($newPassword); // Use the same hashing method

        // Check if the token is valid and not expired
        $stmt = $conn->prepare("
            SELECT c.csapatID, t.email 
            FROM tanar t 
            JOIN csapat c ON t.tanarID = c.tanarID 
            WHERE t.reset_token = :token AND t.reset_expires > :now
        ");
        $now = date("U");
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':now', $now);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Fetch the csapatID to update the password using the teacher's email
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $email = $row['email']; // Get the teacher's email

            // Update the password in the csapat table and reset the token in the tanar table
            $stmt = $conn->prepare("
                UPDATE csapat 
                SET jelszo = :password 
                WHERE csapatID = (SELECT csapatID FROM csapat WHERE tanarID = (SELECT tanarID FROM tanar WHERE email = :email));
            ");
            // Update the tanar table to reset the token and expiration
            $stmt2 = $conn->prepare("
                UPDATE tanar 
                SET reset_token = NULL, reset_expires = NULL 
                WHERE email = :email
            ");
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':email', $email); // Bind the email parameter
            $stmt->execute();
            $stmt2->bindParam(':email', $email); // Bind the email parameter for the second statement
            $stmt2->execute();

            $message = "Your password has been reset successfully.";
        } else {
            $message = "This token is invalid or has expired.";
        }
    }
} catch(PDOException $e) {
    $message = "Connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="hu">
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
            <?php if ($message): ?>
                <div class="<?php echo strpos($message, 'successfully') !== false ? 'success-message' : 'error-message'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if (strpos($message, 'successfully') === false): // Show form only if password update was not successful ?>
                <form method="post" action="" class="login-form">
                    <div class="form-group">
                        <label for="password">Új jelszó:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="button-wrapper">
                        <button type="submit" class="submit-btn">Jelszó visszaállítása</button>
                    </div>
                </form>
            <?php endif; ?>

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
</body>
</html> 