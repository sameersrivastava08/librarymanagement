<?php
// return_book.php - select transaction to return and process
require_once 'config.php';
require_once 'functions.php';
require_login();

$errors = [];
$success = null;

// If returning via POST (select transaction)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['select_tx'])) {
    $tx_id = intval($_POST['tx_id'] ?? 0);
    if (!$tx_id) $errors[] = 'Select a transaction to return.';
    else {
        $stmt = $pdo->prepare("SELECT t.*, b.title, b.author FROM transactions t JOIN books b ON t.book_id=b.id WHERE t.id = ?");
        $stmt->execute([$tx_id]);
        $tx = $stmt->fetch();
        if (!$tx) $errors[] = 'Transaction not found.';
    }
}
// Confirm return (user pressed Confirm return)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_return'])) {
    $tx_id = intval($_POST['tx_id']);
    $serial = trim($_POST['serial_no'] ?? '');
    $returned_date = $_POST['returned_date'] ?? today();
    $remarks = trim($_POST['remarks'] ?? '');

    // validate serial provided
    if ($serial === '') $errors[] = 'Serial No of book is mandatory';
    // fetch tx
    $stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = ? AND status = 'issued'");
    $stmt->execute([$tx_id]);
    $tx = $stmt->fetch();
    if (!$tx) $errors[] = 'No such issued transaction';

    if ($errors) {
        foreach ($errors as $e) flash('error', $e);
        header('Location: return_book.php');
        exit;
    }

    // calculate fine
    $fine = calc_fine($tx['return_date'], $returned_date);

    // update transaction and increment book copies
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("UPDATE transactions SET returned_date=?, status='returned', fine=?, remarks=? WHERE id=?");
        $stmt->execute([$returned_date, $fine, $remarks, $tx_id]);
        $stmt2 = $pdo->prepare("UPDATE books SET copies = copies + 1 WHERE id = ?");
        $stmt2->execute([$tx['book_id']]);
        $pdo->commit();

        if ($fine > 0) {
            // take to pay fine page
            header("Location: pay_fine.php?tx_id=$tx_id");
            exit;
        } else {
            flash('success','Book returned successfully. No fine.');
            header('Location: '.(is_admin()? 'dashboard_admin.php':'dashboard_user.php'));
            exit;
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        flash('error','Return failed: '.$e->getMessage());
        header('Location: return_book.php');
        exit;
    }
}

// show list of currently issued transactions that are not returned
$stmt = $pdo->query("SELECT t.id, u.username, b.title, b.author, t.serial_no, t.issue_date, t.return_date FROM transactions t JOIN books b ON t.book_id=b.id JOIN users u ON t.user_id=u.id WHERE t.status='issued' ORDER BY t.issue_date DESC");
$issued = $stmt->fetchAll();
$err = flash('error');
$notice = flash('success');
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Return Book</title><link rel="stylesheet" href="styles.css"></head>
<body>
<div class="container">
  <div class="header"><h1>Return Book</h1><div><a href="<?=is_admin()? 'dashboard_admin.php':'dashboard_user.php'?>">Back</a></div></div>

  <?php if ($err): ?><div class="error"><?=htmlspecialchars($err)?></div><?php endif; ?>
  <?php if ($notice): ?><div class="notice"><?=htmlspecialchars($notice)?></div><?php endif; ?>

  <h3>Issued Books</h3>
  <form method="post">
    <table class="table">
      <thead><tr><th>Select</th><th>Serial</th><th>Title</th><th>Author</th><th>Issue Date</th><th>Due Date</th></tr></thead>
      <tbody>
      <?php foreach ($issued as $it): ?>
       <tr>
         <td><input type="radio" name="tx_id" value="<?=htmlspecialchars($it['id'])?>"></td>
         <td><?=htmlspecialchars($it['serial_no'])?></td>
         <td><?=htmlspecialchars($it['title'])?></td>
         <td><?=htmlspecialchars($it['author'])?></td>
         <td><?=htmlspecialchars($it['issue_date'])?></td>
         <td><?=htmlspecialchars($it['return_date'])?></td>
       </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <button name="select_tx" type="submit">Return Selected</button>
  </form>

  <?php if (!empty($tx)): ?>
    <hr>
    <h3>Return details for <?=htmlspecialchars($tx['title'])?></h3>
    <form method="post">
      <input type="hidden" name="tx_id" value="<?=htmlspecialchars($tx['id'])?>">
      <div class="form-row"><label>Name of Book (required)</label><input type="text" value="<?=htmlspecialchars($tx['title'])?>" disabled></div>
      <div class="form-row"><label>Author (auto populated)</label><input type="text" value="<?=htmlspecialchars($tx['author'])?>" disabled></div>
      <div class="form-row"><label>Serial No (mandatory)</label><input type="text" name="serial_no" value="<?=htmlspecialchars($tx['serial_no'])?>"></div>
      <div class="form-row"><label>Issue Date (non-editable)</label><input type="date" value="<?=htmlspecialchars($tx['issue_date'])?>" disabled></div>
      <div class="form-row"><label>Return Date (populated to due date - can edit)</label><input type="date" name="returned_date" value="<?=htmlspecialchars($tx['return_date'])?>"></div>
      <div class="form-row"><label>Remarks (optional)</label><textarea name="remarks"></textarea></div>
      <button name="confirm_return" type="submit">Confirm Return</button>
    </form>
  <?php endif; ?>

</div>
</body></html>