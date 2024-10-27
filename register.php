<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    try {
        $stmt->execute([$email, $password]);
        $_SESSION['message'] = "Registrierung erfolgreich! Sie können sich jetzt anmelden.";
        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        $error = "Fehler bei der Registrierung: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Registrierung</title>
</head>
<body>
    <h2>Registrierung</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="POST" action="">
        <input type="email" name="email" required placeholder="E-Mail">
        <input type="password" name="password" required placeholder="Passwort">
        <button type="submit">Registrieren</button>
    </form>
    <p>Bereits registriert? <a href="index.php">Anmelden</a></p>
</body>
</html>
