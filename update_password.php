<?php
// update_password.php - temporarily reset a user's password. DELETE this file after use!
require_once 'config.php';

$username = $_GET['user'] ?? 'admin';
$newpass = $_GET['pass'] ?? 'admin123';

$hash = password_hash($newpass, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
$ok = $stmt->execute([$hash, $username]);

if ($ok) {
    echo "Password for user '" . htmlspecialchars($username) . "' updated to '" . htmlspecialchars($newpass) . "'.<br>";
    echo "Now test with test_password.php or try logging in at the app.";
} else {
    echo "Failed to update password for '" . htmlspecialchars($username) . "'.";
}
?>