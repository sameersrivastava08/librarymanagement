<?php
// config.php - DB connection using PDO. Update credentials as needed.
session_start();

$DB_HOST = '127.0.0.1';
$DB_NAME = 'library_db';
$DB_USER = 'root';
$DB_PASS = ''; // set DB password

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (Exception $e) {
    die("Database connection error: " . htmlspecialchars($e->getMessage()));
}

function is_logged_in() {
    return isset($_SESSION['user']);
}
function require_login() {
    if (!is_logged_in()) { header('Location: index.php'); exit; }
}
function is_admin() {
    return is_logged_in() && ($_SESSION['user']['role'] === 'admin');
}
?>