<?php
require_once 'config.php';
require_once 'functions.php';
require_login();

$tx_id = intval($_GET['tx_id'] ?? $_POST['tx_id'] ?? 0);
if (!$tx_id) { header('Location: return_book.php'); exit; }

$stmt = $pdo->prepare("SELECT t.*, b.title FROM transactions t JOIN books b ON t.book_id=b.id WHERE t.id = ?");
$stmt->execute([$tx_id]);
$tx = $stmt->fetch();
if (!$tx) { flash('error','Transaction not found'); header('Location: return_book.php'); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paid = isset($_POST['fine_paid']) ? 1 : 0;
    if ($tx['fine'] > 0 && !$paid) $errors[] = 'For a pending fine, the Paid checkbox must be selected before completing return.';
    if (!$errors) {
        $stmt = $pdo->prepare("UPDATE transactions SET fine_paid = ?, remarks = CONCAT(IFNULL(remarks,''), ?) WHERE id = ?");
        $remark = "\nFine paid on " . date('Y-m-d');
        $stmt->execute([$paid, $remark, $tx_id]);
        flash('success','Fine status updated. Return completed.');
        header('Location: '.(is_admin()? 'dashboard_admin.php':'dashboard_user.php'));
        exit;
    }
}
$err = flash('error');
$notice = flash('success');
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Pay Fine</title><link rel="stylesheet" href="styles.css"></head>
<body>
<div class="container">
  <div class="header"><h1>Fine Payment</h1><div><a href="return_book.php">Back</a></div></div>

  <?php if ($err): ?><div class="error"><?=htmlspecialchars($err)?></div><?php endif; ?>
  <?php if ($notice): ?><div class="notice"><?=htmlspecialchars($notice)?></div><?php endif; ?>

  <p><strong>Book:</strong> <?=htmlspecialchars($tx['title'])?></p>
  <p><strong>Fine Amount:</strong> <?=number_format($tx['fine'],2)?></p>

  <form method="post">
    <input type="hidden" name="tx_id" value="<?=$tx_id?>">
    <div class="form-row">
      <label><input type="checkbox" name="fine_paid"> Fine Paid</label>
    </div>
    <button type="submit">Confirm</button>
  </form>
</div>
</body></html>