<?php
// search.php - find books and allow selecting one via radio button for issuing
require_once 'config.php';
require_once 'functions.php';
require_login();

$keyword = trim($_GET['q'] ?? '');
$results = [];
if ($keyword !== '') {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR serial_no LIKE ?");
    $like = "%$keyword%";
    $stmt->execute([$like,$like,$like]);
    $results = $stmt->fetchAll();
}
$err = flash('error');
$success = flash('success');
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Search Books</title><link rel="stylesheet" href="styles.css"></head>
<body>
<div class="container">
  <div class="header"><h1>Search & Issue</h1><div><a href="<?=is_admin()? 'dashboard_admin.php':'dashboard_user.php'?>">Back</a></div></div>

  <?php if ($err): ?><div class="error"><?=htmlspecialchars($err)?></div><?php endif; ?>
  <?php if ($success): ?><div class="notice"><?=htmlspecialchars($success)?></div><?php endif; ?>

  <form method="get">
    <div class="form-row"><label>Search books (title, author, serial)</label>
      <input name="q" type="text" value="<?=htmlspecialchars($keyword)?>">
    </div>
    <button type="submit">Search</button>
  </form>

  <?php if ($results): ?>
    <form method="post" action="issue_book.php">
    <table class="table">
      <thead><tr><th>Serial</th><th>Title</th><th>Author</th><th>Type</th><th>Copies</th><th>Select</th></tr></thead>
      <tbody>
      <?php foreach($results as $r): ?>
        <tr>
          <td><?=htmlspecialchars($r['serial_no'])?></td>
          <td><?=htmlspecialchars($r['title'])?></td>
          <td><?=htmlspecialchars($r['author'])?></td>
          <td><?=htmlspecialchars($r['type'])?></td>
          <td><?=htmlspecialchars($r['copies'])?></td>
          <td><input type="radio" name="book_id" value="<?=htmlspecialchars($r['id'])?>"></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <div class="form-row"><label>Member Membership No (optional) - one of the text boxes or drop down to be filled in before submit</label>
      <input type="text" name="membership_no" placeholder="membership number">
    </div>

    <div class="form-row"><label>Issue Date</label><input type="date" name="issue_date" value="<?=today()?>"></div>
    <div class="form-row"><label>Return Date</label><input type="date" name="return_date" value="<?=add_days(today(),15)?>"></div>
    <div class="form-row"><label>Remarks (optional)</label><textarea name="remarks"></textarea></div>
    <button type="submit">Issue Selected Book</button>
    </form>
  <?php endif; ?>

</div>
</body>
</html>