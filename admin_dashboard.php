<?php
session_start();
require 'config.php';

// Überprüfen, ob der Benutzer ein Admin ist
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Fehlermeldungen initialisieren
$error_message = '';
$success_message = '';

// Dateien abrufen
$files_stmt = $pdo->prepare("SELECT * FROM files");
$files_stmt->execute();
$files = $files_stmt->fetchAll(PDO::FETCH_ASSOC);

// Benutzer abrufen
$users_stmt = $pdo->prepare("SELECT * FROM users");
$users_stmt->execute();
$users = $users_stmt->fetchAll(PDO::FETCH_ASSOC);

// Freigabe der Datei
if (isset($_POST['share_file'])) {
    $file_id = $_POST['file_id'];
    $user_id = $_POST['user_id'];

    // Überprüfen, ob die Datei existiert
    $check_stmt = $pdo->prepare("SELECT * FROM files WHERE id = ?");
    $check_stmt->execute([$file_id]);
    $file = $check_stmt->fetch();

    // Überprüfen, ob der Benutzer existiert
    $user_check_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $user_check_stmt->execute([$user_id]);
    $user = $user_check_stmt->fetch();

    if ($file && $user) {
        // Freigabe der Datei
        $stmt = $pdo->prepare("INSERT INTO file_permissions (file_id, user_id, expiry_time) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 2 HOUR))");
        if ($stmt->execute([$file_id, $user_id])) {
            $success_message = 'Datei erfolgreich für den Benutzer freigegeben!';
        } else {
            $error_message = 'Fehler bei der Freigabe der Datei: ' . implode(', ', $stmt->errorInfo());
        }
    } else {
        $error_message = 'Ungültige Datei oder Benutzer.';
    }
}

?>


<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Admin Dashboard</title>
</head>
<body>

<div class="container">
    <h1>Admin Dashboard</h1>

    <?php if ($error_message): ?>
        <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>
    <?php if ($success_message): ?>
        <div class="success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <h2>Datei hochladen</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="file" required>
        <button type="submit" name="upload">Hochladen</button>
    </form>

    <h2>Dateien verwalten</h2>
    <form method="POST" action="">
        <input type="text" name="search_query" placeholder="Nach Dateien suchen..." value="<?php echo htmlspecialchars($search_query); ?>">
        <button type="submit" name="search">Suchen</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Dateiname</th>
                <th>Aktionen</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($files)): ?>
                <tr>
                    <td colspan="2">Keine Dateien gefunden.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($files as $file): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($file['filename']); ?></td>
                        <td>
                            <button class="share-button" onclick="toggleShareModal(<?php echo $file['id']; ?>)">Freigeben</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>Dateifreigabe</h2>
    <div id="shareModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="toggleShareModal()">&times;</span>
            <h3>Dateifreigabe</h3>
            <form method="POST" id="shareForm" action="">
                <input type="hidden" name="file_id" id="file_id">
                <select name="user_id" required>
                    <option value="">Benutzer auswählen</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['email']); ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="share_file">Freigeben</button>
            </form>
        </div>
    </div>

</div>

<script>
function toggleShareModal(fileId = null) {
    const modal = document.getElementById("shareModal");
    const fileIdInput = document.getElementById("file_id");

    if (fileId) {
        fileIdInput.value = fileId;
    }

    modal.style.display = modal.style.display === "block" ? "none" : "block";
}

// Schließe das Modal, wenn der Benutzer außerhalb des Modals klickt
window.onclick = function(event) {
    const modal = document.getElementById("shareModal");
    if (event.target === modal) {
        modal.style.display = "none";
    }
}
</script>

</body>
</html>
