<?php
// test_password.php - quick debug tool. Remove after use.
require_once 'config.php';

$username = $_GET['user'] ?? 'admin';
$password = $_GET['pass'] ?? 'admin123';

$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user) {
    echo "<p style='color:red'>User not found: " . htmlspecialchars($username) . "</p>";
    exit;
}

echo "<p>User found: " . htmlspecialchars($user['username']) . "</p>";
echo "<p>Stored hash: <pre>" . htmlspecialchars($user['password']) . "</pre></p>";
$ok = password_verify($password, $user['password']);
echo "<p>password_verify('" . htmlspecialchars($password) . "'): " . ($ok ? "<b style='color:green'>TRUE</b>" : "<b style='color:red'>FALSE</b>") . "</p>";
?>