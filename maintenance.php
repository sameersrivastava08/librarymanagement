<?php
require_once 'config.php';
require_once 'functions.php';
require_login();
if (!is_admin()) { echo "Access denied."; exit; }
$action = $_GET['a'] ?? '';
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Maintenance</title><link rel="stylesheet" href="styles.css"></head>
<body>
<div class="container">
  <div class="header"><h1>Maintenance</h1><div><a href="dashboard_admin.php">Back</a></div></div>

  <h3>Books</h3>
  <p><a href="add_book.php">Add Book</a> | <a href="update_book.php">Update Book</a></p>

  <h3>Members</h3>
  <p><a href="add_member.php">Add Membership</a> | <a href="update_member.php">Update Membership</a></p>

</div>
</body>
</html>