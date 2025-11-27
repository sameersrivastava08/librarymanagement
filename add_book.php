<?php
require_once 'config.php';
require_once 'functions.php';
require_login();
if (!is_admin()) { echo "Access denied."; exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serial = trim($_POST['serial_no'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $type = ($_POST['type'] ?? 'book');
    $copies = intval($_POST['copies'] ?? 1);

    if ($serial === '') $errors[] = "Serial No is required";
    if ($title === '') $errors[] = "Title is required";
    if ($author === '') $errors[] = "Author is required";

    if (!$errors) {
        $stmt = $pdo->prepare("INSERT INTO books (serial_no,title,author,type,copies) VALUES (?,?,?,?,?)");
        try {
            $stmt->execute([$serial, $title, $author, $type, $copies]);
            flash('success', 'Book added successfully.');
            header('Location: maintenance.php'); exit;
        } catch (Exception $e) {
            $errors[] = "Error adding book: " . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Add Book</title><link rel="stylesheet" href="styles.css"></head>
<body>
<div class="container">
  <div class="header"><h1>Add Book</h1><div><a href="maintenance.php">Back</a></div></div>

  <?php if ($errors): foreach ($errors as $er): ?><div class="error"><?=htmlspecialchars($er)?></div><?php endforeach; endif; ?>
  <?php if ($msg = flash('success')): ?><div class="notice"><?=htmlspecialchars($msg)?></div><?php endif; ?>

  <form method="post">
    <div class="form-row"><label>Serial No (required)</label><input name="serial_no" type="text" required></div>
    <div class="form-row"><label>Title (required)</label><input name="title" type="text" required></div>
    <div class="form-row"><label>Author (required)</label><input name="author" type="text" required></div>
    <div class="form-row"><label>Type</label>
      <select name="type">
        <option value="book" selected>Book</option>
        <option value="movie">Movie</option>
      </select>
    </div>
    <div class="form-row"><label>Copies</label><input name="copies" type="text" value="1"></div>
    <button type="submit">Add Book</button>
  </form>
</div>
</body></html>