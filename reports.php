<?php
require_once 'config.php';
require_once 'functions.php';
require_login();
// reports simple: list all transactions and current issued
$stmt = $pdo->query("SELECT t.*, u.username, b.title FROM transactions t JOIN users u ON t.user_id=u.id JOIN books b ON t.book_id=b.id ORDER BY t.created_at DESC");
$all = $stmt->fetchAll();
$stmt2 = $pdo->query("SELECT t.*, b.title FROM transactions t JOIN books b ON t.book_id=b.id WHERE t.status='issued' ORDER BY t.issue_date DESC");
$issued = $stmt2->fetchAll();
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Reports</title><link rel="stylesheet" href="styles.css"></head>
<body>
<div class="container">
  <div class="header"><h1>Reports</h1><div><a href="<?=is_admin()?'dashboard_admin.php':'dashboard_user.php'?>">Back</a></div></div>

  <h3>Currently Issued</h3>
  <table class="table">
    <thead><tr><th>Book</th><th>Serial</th><th>Issued To (user)</th><th>Issue Date</th><th>Due Date</th></tr></thead>
    <tbody>
      <?php foreach($issued as $i): ?>
        <tr><td><?=htmlspecialchars($i['title'])?></td><td><?=htmlspecialchars($i['serial_no'])?></td><td><?=htmlspecialchars($i['user_id'])?></td><td><?=htmlspecialchars($i['issue_date'])?></td><td><?=htmlspecialchars($i['return_date'])?></td></tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <h3>All Transactions</h3>
  <table class="table">
    <thead><tr><th>ID</th><th>Book</th><th>User</th><th>Status</th><th>Fine</th></tr></thead>
    <tbody>
      <?php foreach($all as $a): ?>
        <tr><td><?=htmlspecialchars($a['id'])?></td><td><?=htmlspecialchars($a['title'])?></td><td><?=htmlspecialchars($a['username'])?></td><td><?=htmlspecialchars($a['status'])?></td><td><?=number_format($a['fine'],2)?></td></tr>
      <?php endforeach; ?>
    </tbody>
  </table>

</div>
</body></html>