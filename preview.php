<?php
session_start();
require 'config.php';

if (!isset($_GET['file_id'])) {
    die('Ungültige Datei-ID.');
}

$fileId = $_GET['file_id'];
$stmt = $pdo->prepare("SELECT * FROM files WHERE id = ?");
$stmt->execute([$fileId]);
$file = $stmt->fetch();

if (!$file) {
    die('Datei nicht gefunden.');
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Vorschau der Datei</title>
</head>
<body>
    <h2>Vorschau der Datei: <?php echo htmlspecialchars($file['filename']); ?></h2>
    <iframe src="uploads/<?php echo htmlspecialchars($file['filename']); ?>" width="600" height="400"></iframe>
    <br>
    <a href="user_dashboard.php">Zurück zum Dashboard</a>
</body>
</html>
