<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require 'config.php';

// Stelle sicher, dass der Benutzer eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // SQL-Abfrage zur Auswahl der freigegebenen Dateien
    $stmt = $pdo->prepare("
        SELECT f.id, f.filename, f.file_path 
        FROM files f 
        JOIN file_permissions fp ON f.id = fp.file_id 
        WHERE fp.user_id = :user_id AND fp.expiry_time > NOW()
    ");
    $stmt->execute(['user_id' => $user_id]);
    $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Datenbankfehler: " . $e->getMessage();
    exit; // Stoppe das Skript bei einem Fehler
}
?>


<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Verlinkung zu deinem CSS -->
</head>
<body>
    <header>
        <h1>Benutzerdashboard</h1>
        <p>Willkommen, <?php echo htmlspecialchars($_SESSION['email']); ?></p>
        <a href="logout.php">Ausloggen</a>
    </header>

    <main>
        <h2>Freigegebene Dateien</h2>
        
        <?php if (empty($files)): ?>
            <p>Keine Dateien für Sie freigegeben.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Dateiname</th>
                        <th>Download</th>
                        <th>Vorschau</th>
                        <th>Einsatz Ende</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($files as $file): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($file['filename']); ?></td>
                            <td>
                                <a href="download.php?file_id=<?php echo $file['id']; ?>">Herunterladen</a>
                            </td>
                            <td>
                                <a href="preview.php?file_id=<?php echo $file['id']; ?>" target="_blank">Vorschau</a>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($file['expiry_time']); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</body>
</html>
