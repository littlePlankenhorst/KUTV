<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kutv1";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './language/src/Exception.php';
require './language/src/PHPMailer.php';
require './language/src/SMTP.php';

try {
    // Initialize the database connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch schools from the database
    $school_query = "SELECT iskolaID, iskolaNev, helyseg FROM Iskola";
    $school_result = $conn->query($school_query);

    // Rest of your logic...

} catch (PDOException $e) {
         "Connection failed: " . $e->getMessage();
}

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Capture form data
        $csapatnev = $_POST['csapatnev'];
        $vezeteknev = $_POST['vezeteknev'];
        $keresztnev = $_POST['keresztnev'];
        $email = $_POST['email'];
        $telefon = $_POST['telefon'];
        $iskolaNev = $_POST['iskolaNev'];
        $password = $_POST['password'];

        // Capture teammates' data
        $teammates = [];
        for ($i = 1; $i <= 3; $i++) {
            $teammates[] = [
                'keresztNev' => $_POST["teammate_keresztnev_$i"],
                'osztaly' => $_POST["teammate_osztaly_$i"], // Assuming you want to capture class as well
                'telefon' => $_POST["teammate_telefon_$i"],
                'email' => $_POST["teammate_email_$i"],
                'IBAN' => $_POST["teammate_iban_$i"],
                'telepules' => $_POST["teammate_telepules_$i"],
                'utca' => $_POST["teammate_utca_$i"],
            ];
        }

        // Input validation
        $errors = [];

        if (empty($csapatnev)) {
            $errors[] = "Csapatnév is required.";
        }
        if (empty($vezeteknev)) {
            $errors[] = "Vezető tanár vezetékneve is required.";
        }
        if (empty($keresztnev)) {
            $errors[] = "Vezető tanár keresztneve is required.";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }
        if (empty($telefon)) {
            $errors[] = "Telefonszám is required.";
        }
        if (empty($iskolaNev)) {
            $errors[] = "Iskola neve is required.";
        }
        if (empty($password)) {
            $errors[] = "Jelszó is required.";
        }

        // Validate teammates' data
        foreach ($teammates as $index => $teammate) {
            if (empty($teammate['keresztNev'])) {
                $errors[] = "Teammate " . ($index + 1) . " keresztneve is required.";
            }
            if (empty($teammate['osztaly'])) {
                $errors[] = "Teammate " . ($index + 1) . " osztálya is required.";
            }
            if (empty($teammate['telefon'])) {
                $errors[] = "Teammate " . ($index + 1) . " telefonszáma is required.";
            }
            if (!filter_var($teammate['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Teammate " . ($index + 1) . " has an invalid email format.";
            }
            if (empty($teammate['IBAN'])) {
                $errors[] = "Teammate " . ($index + 1) . " IBAN is required.";
            }
            if (empty($teammate['telepules'])) {
                $errors[] = "Teammate " . ($index + 1) . " települése is required.";
            }
            if (empty($teammate['utca'])) {
                $errors[] = "Teammate " . ($index + 1) . " utcája is required.";
            }
        }

        // If there are errors, display them
        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo "<div class='error-message'>" . htmlspecialchars($error) . "</div>";
            }
        } else {
            // Hash the password using SHA1
            $hashed_password = sha1($password);

            // Insert into Tanar table
            $stmt = $conn->prepare("INSERT INTO Tanar (vezetekNev, keresztNev, telefon, email) VALUES (:vezeteknev, :keresztnev, :telefon, :email)");
            $stmt->bindParam(':vezeteknev', $vezeteknev);
            $stmt->bindParam(':keresztnev', $keresztnev);
            $stmt->bindParam(':telefon', $telefon);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $tanar_id = $conn->lastInsertId(); // Get the last inserted ID

            // Insert into Csapat table
            $stmt = $conn->prepare("INSERT INTO Csapat (csapatNev, bejelentkezesIdeje, jelszo, iskolaID, tanarID, versenyID) VALUES (:csapatnev, NOW(), :hashed_password, :iskola_id, :tanar_id, :verseny_id)");
            $stmt->bindParam(':csapatnev', $csapatnev);
            $stmt->bindParam(':hashed_password', $hashed_password); // Use hashed password
            $stmt->bindParam(':iskola_id', $iskolaNev);
            $stmt->bindParam(':tanar_id', $tanar_id);
            $verseny_id=1;
            $stmt->bindParam(':verseny_id', $verseny_id);
            $stmt->execute();
            $csapat_id = $conn->lastInsertId(); // Get the last inserted ID for the team

            // Insert teammates into versenyzo table
foreach ($teammates as $teammate) {
    $stmt = $conn->prepare("INSERT INTO versenyzo (vezetekNev, keresztNev, telefon, email, osztaly, IBAN, telepules, utca, csapatID) 
                            VALUES (:vezetekNev, :keresztNev, :telefon, :email, :osztaly, :IBAN, :telepules, :utca, :csapatID)");
    $stmt->bindParam(':vezetekNev', $teammate['keresztNev']); // Student's last name
    $stmt->bindParam(':keresztNev', $teammate['keresztNev']); // Student's first name
    $stmt->bindParam(':telefon', $teammate['telefon']);
    $stmt->bindParam(':email', $teammate['email']);
    $stmt->bindParam(':osztaly', $teammate['osztaly']);
    $stmt->bindParam(':IBAN', $teammate['IBAN']);
    $stmt->bindParam(':telepules', $teammate['telepules']);
    $stmt->bindParam(':utca', $teammate['utca']);
    $stmt->bindParam(':csapatID', $csapat_id); // Team ID
    $stmt->execute();
}

            // Create a new PHPMailer instance
            $mail = new PHPMailer(true);

            try {
                //Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
                $mail->SMTPAuth = true; // Enable SMTP authentication
                $mail->Username = 'mailbence74@gmail.com'; // SMTP username
                $mail->Password = 'bwom gljb zzlf zmjk'; // SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
                $mail->Port = 587; // TCP port to connect to

                // Recipients
                // Send email to each teammate
                foreach ($teammates as $teammate) {
                    $mail->addAddress($teammate['email']); // Add a teammate's email
                    $mail->setFrom('your_email@gmail.com', 'Szabo David-Bence'); // Set sender's email and name
                    $mail->Subject = 'Sikeres Regisztracio';
                    $mail->Body = "Kedves " . htmlspecialchars($teammate['keresztNev']) . ",\n\n" .
                                  "Gratulálunk! Sikeresen regisztráltál a versenyre.\n\n" .
                                  "Üdvözlettel,\n" .
                                  "A Szervezők";

                    $mail->send(); // Send the email
                    $mail->clearAddresses(); // Clear the recipient for the next iteration
                }

                // Send email to the lead teacher
                $mail->addAddress($email); // Add the lead teacher's email
                $mail->setFrom('mailbence74@gmail.com', 'Szabo David-Bence'); // Set sender's email and name
                $mail->Subject = 'Sikeres Regisztracio';
                $mail->Body = "Kedves " . htmlspecialchars($vezeteknev) . " " . htmlspecialchars($keresztnev) . ",\n\n" .
                              "Gratulálunk! A csapat sikeresen regisztrált a versenyre.\n\n" .
                              "Üdvözlettel,\n" .
                              "A Szervezők";

                $mail->send(); // Send the email to the lead teacher
                $mail->clearAddresses(); // Clear the recipient for any future emails

                echo "Registration successful!";
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        }
    }
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regisztráció</title>
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
                <a href="./login.php">Bejelentkezés</a>
            </div>
        </nav>
    </header>
    
        <div class="main">
            <h1 style="text-align: center;">Regisztráció</h1>
            <form onsubmit="return validatePasswords();" method="POST" action="">
                <div class="formrow student-row">
                    <div class="field">
                        <label for="csapatnev">Csapatnév:</label>
                        <input type="text" id="csapatnev" name="csapatnev" required>
                    </div>
                    <div class="field">
                        <label for="school">Iskola:</label>
                        <select class="form-control" id="school-name" name="iskolaNev" required>
                            <option value="">Válasszon iskolát...</option>
                            <?php
                            if ($school_result->rowCount() > 0) {
                                while ($row = $school_result->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='" . htmlspecialchars($row['iskolaID']) . "'>" . htmlspecialchars($row['iskolaNev']) . ' - ' . htmlspecialchars($row['helyseg']) . "</option>";
                                }
                            } else {
                                echo "<option value=''>Nincs elérhető iskola</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="formrow">
                    <div class="field">
                        <label for="vezeteknev">Vezető tanár vezetékneve:</label>
                        <input type="text" id="vezeteknev" name="vezeteknev" required>
                    </div>
                    <div class="field">
                        <label for="keresztnev">Vezető tanár keresztneve:</label>
                        <input type="text" id="keresztnev" name="keresztnev" required>
                    </div>
                </div>
                <div class="formrow">
                    <div class="field">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="field">
                        <label for="telefon">Telefonszám:</label>
                        <input type="text" id="telefon" name="telefon" required>
                    </div>
                </div>
                <div class="formrow">
                    <div class="field">
                        <label for="password">Jelszó:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="field">
                        <label for="password_confirm">Jelszó megerősítése:</label>
                        <input type="password" id="password_confirm" name="password_confirm" required>
                    </div>
                </div>


                <!-- Teammates Information -->
        <h3 style="margin-top:50px;">Diákok</h3>
        <?php for ($i = 1; $i <= 3; $i++): ?>
            <div class="last-row">
            <div class="formrow">
                <div class="field">
                    <label for="teammate_keresztnev_<?php echo $i; ?>">Diák <?php echo $i; ?> Vezetékneve:</label>
                    <input type="text" id="teammate_keresztnev_<?php echo $i; ?>" name="teammate_keresztnev_<?php echo $i; ?>" required>
                </div>
                <div class="field">
                    <label for="teammate_keresztnev_<?php echo $i; ?>">Diák <?php echo $i; ?> Keresztneve:</label>
                    <input type="text" id="teammate_keresztnev_<?php echo $i; ?>" name="teammate_keresztnev_<?php echo $i; ?>" required>
                </div>
            </div>

            <div class="formrow">
                <div class="field">
                    <label for="teammate_osztaly_<?php echo $i; ?>">Osztály:</label>
                    <input type="text" id="teammate_osztaly_<?php echo $i; ?>" name="teammate_osztaly_<?php echo $i; ?>" required>
                </div>
                <div class="field">
                    <label for="teammate_telefon_<?php echo $i; ?>">Telefonszám:</label>
                    <input type="text" id="teammate_telefon_<?php echo $i; ?>" name="teammate_telefon_<?php echo $i; ?>" required>
                </div>
            </div>
            <div class="formrow">
                <div class="field">
                    <label for="teammate_email_<?php echo $i; ?>"> Email:</label>
                    <input type="email" id="teammate_email_<?php echo $i; ?>" name="teammate_email_<?php echo $i; ?>" required>
                </div>
                <div class="field">
                    <label for="teammate_iban_<?php echo $i; ?>"> IBAN:</label>
                    <input type="text" id="teammate_iban_<?php echo $i; ?>" name="teammate_iban_<?php echo $i; ?>" required>
                </div>
            </div>
            <div class="formrow">
                <div class="field">
                    <label for="teammate_telepules_<?php echo $i; ?>"> Település:</label>
                    <input type="text" id="teammate_telepules_<?php echo $i; ?>" name="teammate_telepules_<?php echo $i; ?>" required>
                </div>
                <div class="field">
                    <label for="teammate_utca_<?php echo $i; ?>"> Utca:</label>
                    <input type="text" id="teammate_utca_<?php echo $i; ?>" name="teammate_utca_<?php echo $i; ?>" required>
                </div>
            </div>
            </div>
        
        <?php endfor; ?>

        <button type="submit">Regisztrálás</button>
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
    <script>
function validatePasswords() {
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("password_confirm").value;

            if (password !== confirmPassword) {
                alert("A jelszavak nem egyeznek! Kérlek, próbáld újra.");
                return false; 
            }

            return true; }
    </script>
</body>
</html>

