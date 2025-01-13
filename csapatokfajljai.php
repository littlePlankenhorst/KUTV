<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kutv3";

$existingFiles1 = [];
$teams=[];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt2 = $conn->prepare("
        SELECT csapatNev
        FROM csapat
    ");
    $stmt2 -> execute();
    $teams = $stmt2 ->fetchAll(PDO::FETCH_COLUMN);

    // Fetch existing files for this team
    $stmt = $conn->prepare("
        SELECT f.megoldas
        FROM fordulomegoldasa f
        JOIN csapat c
        WHERE csapatID = :csapatID AND forduloID = 1
    ");
    $stmt->bindParam(':csapatID', $_SESSION['csapatID']);
    $stmt->execute();
    $existingFiles1 = $stmt->fetchAll(PDO::FETCH_COLUMN);

}
catch(PDOException $e){

}

?>

<form method="POST" action="">
        <label for="teams">Select a team:</label>
        <select name="team" id="teams">
            <?php foreach ($teams as $team): ?>
                <option value="<?= htmlspecialchars($team) ?>"><?= htmlspecialchars($team) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Submit</button>
    </form>

<?php echo $existingFiles1[1];?>