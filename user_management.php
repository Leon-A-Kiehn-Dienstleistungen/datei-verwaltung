<?php
session_start();
include 'config.php';

// Überprüfen, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Holen Sie sich die freigegebenen Dateien für den Benutzer
$stmt = $pdo->prepare("SELECT f.* FROM files f
    JOIN file_permissions fp ON f.id = fp.file_id
    WHERE fp.user_id = ? AND fp.expires_at > NOW()");
$stmt->execute([$_SESSION['user_id']]);
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Benutzerdashboard</h1>
        <h2>Freigegebene Dateien</h2>
        
        <table>
            <thead>
                <tr>
                    <th>Dateiname</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($files as $file): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($file['filename']); ?></td>
                        <td>
                            <form method="POST" action="actions.php" style="display:inline;">
                                <input type="hidden" name="file_id" value="<?php echo $file['id']; ?>">
                                <button type="submit" name="action" value="preview" class="button">Vorschau</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
