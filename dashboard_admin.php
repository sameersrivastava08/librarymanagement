<?php
require_once 'config.php';
require_once 'functions.php';
require_login();
if (!is_admin()) { echo "Access denied."; exit; }
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Admin Dashboard</title><link rel="stylesheet" href="styles.css"></head>
<body>
<div class="container">
  <div class="header">
    <h1>Admin Dashboard</h1>
    <div>Welcome, <?=htmlspecialchars($_SESSION['user']['full_name'])?> | <nav><a href="logout.php">Logout</a></nav></div>
  </div>

  <div class="notice">Use Maintenance to manage Books and Members. Reports and Transactions available below.</div>

  <ul>
    <li><a href="maintenance.php">Maintenance (Books & Members)</a></li>
    <li><a href="reports.php">Reports</a></li>
    <li><a href="search.php">Transactions: Search & Issue Book</a></li>
    <li><a href="return_book.php">Transactions: Return Book</a></li>
    <li><a href="users.php">User Management</a></li>
  </ul>
</div>
</body>
</html>