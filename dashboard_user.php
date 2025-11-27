<?php
require_once 'config.php';
require_once 'functions.php';
require_login();
// non-admin user
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>User Dashboard</title><link rel="stylesheet" href="styles.css"></head>
<body>
<div class="container">
  <div class="header">
    <h1>User Dashboard</h1>
    <div>Welcome, <?=htmlspecialchars($_SESSION['user']['full_name'])?> | <nav><a href="logout.php">Logout</a></nav></div>
  </div>

  <div class="notice">Users cannot access Maintenance. Use the options below for Reports and Transactions.</div>

  <ul>
    <li><a href="reports.php">Reports</a></li>
    <li><a href="search.php">Search & Issue Book</a></li>
    <li><a href="return_book.php">Return Book</a></li>
  </ul>
</div>
</body>
</html>