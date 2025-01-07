<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['csapatnev'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kutv1";

$existingFiles1 = [];
$existingFiles2 = [];
$uploadError = null;

// Replace the hardcoded deadline definitions with database query
$deadlines = [];
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Fetch deadlines from database
    $stmt = $conn->prepare("SELECT forduloID, hatarido FROM versenyfordulo WHERE forduloID IN (1, 2)");
    $stmt->execute();
    $deadlineResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Organize deadlines by forduloID
    foreach ($deadlineResults as $result) {
        $deadlines[$result['forduloID']] = new DateTime($result['hatarido']);
    }
    
    // Set deadlines (with fallback dates just in case)
    $deadline = isset($deadlines[1]) ? $deadlines[1] : new DateTime('2025-02-01 10:00:00');
    $deadline2 = isset($deadlines[2]) ? $deadlines[2] : new DateTime('2025-12-25 10:00:00');
    $currentTime = new DateTime();

    // Format dates for display
    $formattedCurrentTime = $currentTime->format('Y-m-d H:i:s');
    $formattedDeadline = $deadline->format('Y-m-d H:i:s');
    $formattedDeadline2 = $deadline2->format('Y-m-d H:i:s');

    // Check if upload is past the deadline
    $isPastDeadline = $currentTime > $deadline;
    $isPastDeadline2 = $currentTime > $deadline2;

    // Maximum file size (20 MB in bytes)
    $maxFileSize = 20 * 1024 * 1024; // 20 * 1024 * 1024 bytes = 20 MB

    // Clear any previous upload messages
    if (isset($_SESSION['upload_message'])) {
        unset($_SESSION['upload_message']);
    }

    // Fetch existing files for this team
    $stmt = $conn->prepare("SELECT megoldas FROM fordulomegoldasa WHERE csapatID = :csapatID AND forduloID = 1");
    $stmt->bindParam(':csapatID', $_SESSION['csapatID']);
    $stmt->execute();
    $existingFiles1 = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $stmt = $conn->prepare("SELECT megoldas FROM fordulomegoldasa WHERE csapatID = :csapatID AND forduloID = 2");
    $stmt->bindParam(':csapatID', $_SESSION['csapatID']);
    $stmt->execute();
    $existingFiles2 = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Handle file upload
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Determine which form was submitted
        $forduloID = isset($_POST['fordulo']) ? $_POST['fordulo'] : 1;
        $isPastDeadlineCurrent = ($forduloID == 1) ? $isPastDeadline : $isPastDeadline2;

        // Check if upload is past the deadline
        if ($isPastDeadlineCurrent) {
            $_SESSION['upload_message'] = "A feltöltési határidő lejárt. Már nem lehet fájlt feltölteni.";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            // Check if a file was uploaded
            if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['pdf_file']['tmp_name'];
                $FileName = $_FILES['pdf_file']['name'];
                $fileNameCmps = explode(".", $FileName);
                $fileExtension = strtolower(end($fileNameCmps));

                // Check file size
                $fileSize = $_FILES['pdf_file']['size'];
                if ($fileSize > $maxFileSize) {
                    $_SESSION['upload_message'] = "A fájl mérete nem haladhatja meg a 20 MB-ot. A jelenlegi fájl mérete: " . 
                                   round($fileSize / (1024 * 1024), 2) . " MB";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                } 
                // Check if the file is a PDF
                elseif ($fileExtension === 'pdf') {
                    $uploadFileDir = './uploads/';
                    
                    // Ensure uploads directory exists
                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0777, true);
                    }

                    $dest_path = $uploadFileDir . $FileName;

                    // Attempt to move uploaded file with error checking
                    if (is_uploaded_file($fileTmpPath)) {
                        if (move_uploaded_file($fileTmpPath, $dest_path)) {
                            // Update database
                            $stmt = $conn->prepare("
                                INSERT INTO fordulomegoldasa (megoldas, csapatID, forduloID) 
                                VALUES (:megoldas, :csapatID, :forduloID) 
                                ON DUPLICATE KEY UPDATE megoldas = :megoldas
                            ");
                            $stmt->bindParam(':megoldas', $FileName);
                            $stmt->bindParam(':csapatID', $_SESSION['csapatID']);
                            $stmt->bindParam(':forduloID', $forduloID);
                            $stmt->execute();

                            $_SESSION['upload_message'] = "Fájl sikeresen feltöltve: " . $FileName;
                            header("Location: " . $_SERVER['PHP_SELF']);
                            exit();
                        } else {
                            $_SESSION['upload_message'] = "Nem sikerült a fájlt áthelyezni: " . $dest_path;
                            header("Location: " . $_SERVER['PHP_SELF']);
                            exit();
                        }
                    } else {
                        $_SESSION['upload_message'] = "Érvénytelen feltöltött fájl.";
                        header("Location: " . $_SERVER['PHP_SELF']);
                        exit();
                    }
                } else {
                    $_SESSION['upload_message'] = "Csak PDF fájlok tölthetők fel.";
                    header("Location: " . $_SERVER['PHP_SELF']);
                    exit();
                }
            } else {
                // Handle upload errors
                $uploadError = match($_FILES['pdf_file']['error']) {
                    UPLOAD_ERR_INI_SIZE => "A fájl mérete meghaladja a php.ini upload_max_filesize értékét.",
                    UPLOAD_ERR_FORM_SIZE => "A fájl mérete meghaladja a HTML form MAX_FILE_SIZE értékét.",
                    UPLOAD_ERR_PARTIAL => "A fájl csak részben lett feltöltve.",
                    UPLOAD_ERR_NO_FILE => "Nem lett fájl feltöltve.",
                    UPLOAD_ERR_NO_TMP_DIR => "Hiányzik az ideiglenes mappa.",
                    UPLOAD_ERR_CANT_WRITE => "Nem sikerült a fájlt lemezreírni.",
                    UPLOAD_ERR_EXTENSION => "Egy PHP kiterjesztés leállította a fájlfeltöltést.",
                    default => "Ismeretlen feltöltési hiba."
                };
                $_SESSION['upload_message'] = $uploadError;
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        }
    }
} catch (PDOException $e) {
    $_SESSION['upload_message'] = "Adatbázis hiba: " . $e->getMessage();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Megoldás Feltöltése</title>
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
                <a href="./logout.php">Kijelentkezés</a>
                <?php if (isset($_SESSION['csapatnev'])): ?>
                   <span class="welcome-message"><?php echo htmlspecialchars($_SESSION['csapatnev']); ?></span>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main>
    <div class="upload-layout">
        <!-- Left Section -->
        <div class="upload-section left-section">
            <div class="inner-container">
        <h1>1. Forduló megoldása</h1>
        <div class="time-info">
            <p>Rendszeridő: <strong><?php echo htmlspecialchars($formattedCurrentTime); ?></strong></p>
            <p>Feltöltési határidő: <strong><?php echo htmlspecialchars($formattedDeadline); ?></strong></p>
            <p>Státusz: 
                <strong>
                    <?php 
                    if ($isPastDeadline) {
                        echo '<span style="color: red;">Lejárt</span>';
                    } else {
                        echo '<span style="color: green;">Aktív</span>';
                    }
                    ?>
                </strong>
            </p>
        </div>

        <?php 
        // Display session message if exists
        if (isset($_SESSION['upload_message'])) {
            echo '<div class="' . ($isPastDeadline ? 'deadline-message' : 'error-message') . '">' . 
                 htmlspecialchars($_SESSION['upload_message']) . '</div>';
            // Clear the message after displaying
            unset($_SESSION['upload_message']);
        }
        ?>

        <?php if (!$isPastDeadline): ?>
            <?php if (!empty($existingFiles1)): ?>
                <div class="existing-files-info">
                    <h2>Korábban feltöltött fájlok:</h2>
                    <?php echo htmlspecialchars($existingFiles1[0]); ?>
                </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data" id="uploadForm1">
                <input type="hidden" name="fordulo" value="1">
                <label for="pdf_file1">Válassza ki a feltöltendő PDF fájlt:</label>
                <input type="file" name="pdf_file" id="pdf_file1" accept=".pdf" required>
                <button type="submit">Feltöltés</button>
            </form>
        <?php endif; ?>
            </div>
        </div>

        <!-- Right Section -->
        <div class="upload-section right-section">
            <div class="inner-container">
            <h1>2. Forduló megoldása</h1>
        <div class="time-info">
            <p>Rendszeridő: <strong><?php echo htmlspecialchars($formattedCurrentTime); ?></strong></p>
            <p>Feltöltési határidő: <strong><?php echo htmlspecialchars($formattedDeadline2); ?></strong></p>
            <p>Státusz: 
                <strong>
                    <?php 
                    if ($isPastDeadline2) {
                        echo '<span style="color: red;">Lejárt</span>';
                    } else {
                        echo '<span style="color: green;">Aktív</span>';
                    }
                    ?>
                </strong>
            </p>
        </div>

        <?php 
        // Display session message if exists
        if (isset($_SESSION['upload_message'])) {
            echo '<div class="' . ($isPastDeadline2 ? 'deadline-message' : 'error-message') . '">' . 
                 htmlspecialchars($_SESSION['upload_message']) . '</div>';
            // Clear the message after displaying
            unset($_SESSION['upload_message']);
        }
        ?>

        <?php if (!$isPastDeadline2): ?>
            <?php if (!empty($existingFiles2)): ?>
                <div class="existing-files-info">
                    <h2>Korábban feltöltött fájlok:</h2>
                    <?php echo htmlspecialchars($existingFiles2[0]); ?>
                </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data" id="uploadForm2">
                <input type="hidden" name="fordulo" value="2">
                <label for="pdf_file2">Válassza ki a feltöltendő PDF fájlt:</label>
                <input type="file" name="pdf_file" id="pdf_file2" accept=".pdf" required>
                <button type="submit">Feltöltés</button>
            </form>
        <?php endif; ?>
            </div>
        </div>
            </div>
        </div>
    </div>
    </main>

    <footer>
        <p>&copy; 2024 Szervezők. Minden jog fenntartva.</p>
    </footer>

    <style>
        .existing-files-info {
            background-color: #f4f4f4;
            border: 1px solid #ddd;
            padding: 29px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        button[type="submit"] {
                display: block;
                width: 80%;
                padding: 12px;
                margin-left:21px;
                margin-top:32px;
                background-color: #007bff;
                color: white;
                border: none;
                border-radius: 4px;
                font-size: 18px;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }
    </style>
</body>
    <script>
        function toggleMenu() {
            const navButtons = document.querySelector('.nav-buttons');
            navButtons.classList.toggle('active');
        }
    </script>
</html> 