<?php
require_once 'config.php';
require_once 'functions.php';
require_login();
if (!is_admin()) { echo "Access denied."; exit; }

$errors = [];
// simple search/update flow
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['find'])) {
    $serial = trim($_POST['serial_search'] ?? '');
    if ($serial === '') $errors[] = 'Enter serial to search';
    else {
        $stmt = $pdo->prepare("SELECT * FROM books WHERE serial_no = ?");
        $stmt->execute([$serial]);
        $book = $stmt->fetch();
        if (!$book) $errors[] = 'Book not found';
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $copies = intval($_POST['copies']);
    if ($title === '' || $author === '') $errors[] = 'Title and Author required';
    if (!$errors) {
        $stmt = $pdo->prepare("UPDATE books SET title=?, author=?, copies=? WHERE id=?");
        $stmt->execute([$title,$author,$copies,$id]);
        flash('success','Book updated');
        header('Location: maintenance.php'); exit;
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Update Book</title><link rel="stylesheet" href="styles.css"></head>
<body>
<div class="container">
  <div class="header"><h1>Update Book</h1><div><a href="maintenance.php">Back</a></div></div>

  <?php if ($errors): foreach ($errors as $er): ?><div class="error"><?=htmlspecialchars($er)?></div><?php endforeach; endif; ?>

  <form method="post">
    <div class="form-row"><label>Search by Serial No</label><input name="serial_search" type="text"></div>
    <button name="find" type="submit">Find</button>
  </form>

  <?php if (!empty($book)): ?>
  <hr>
  <form method="post">
    <input type="hidden" name="id" value="<?=htmlspecialchars($book['id'])?>">
    <div class="form-row"><label>Serial No</label><input type="text" value="<?=htmlspecialchars($book['serial_no'])?>" disabled></div>
    <div class="form-row"><label>Title</label><input name="title" type="text" value="<?=htmlspecialchars($book['title'])?>"></div>
    <div class="form-row"><label>Author</label><input name="author" type="text" value="<?=htmlspecialchars($book['author'])?>"></div>
    <div class="form-row"><label>Copies</label><input name="copies" type="text" value="<?=htmlspecialchars($book['copies'])?>"></div>
    <button name="update" type="submit">Update</button>
  </form>
  <?php endif; ?>

</div>
</body></html>