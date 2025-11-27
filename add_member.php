<?php
require_once 'config.php';
require_once 'functions.php';
require_login();
if (!is_admin()) { echo "Access denied."; exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mem_no = trim($_POST['membership_number'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $duration = $_POST['duration'] ?? '6';

    if ($mem_no === '' || $name === '') $errors[] = 'Membership number and name are mandatory';
    if (!$errors) {
        $start = today();
        $end = date('Y-m-d', strtotime("+$duration months", strtotime($start)));
        $stmt = $pdo->prepare("INSERT INTO members (membership_number,name,email,membership_start,membership_end) VALUES (?,?,?,?,?)");
        $stmt->execute([$mem_no,$name,$email,$start,$end]);
        flash('success','Membership added');
        header('Location: maintenance.php'); exit;
    }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Add Membership</title><link rel="stylesheet" href="styles.css"></head>
<body>
<div class="container">
  <div class="header"><h1>Add Membership</h1><div><a href="maintenance.php">Back</a></div></div>

  <?php if (!empty($errors)): foreach($errors as $er): ?><div class="error"><?=htmlspecialchars($er)?></div><?php endforeach; endif; ?>

  <form method="post">
    <div class="form-row"><label>Membership Number (required)</label><input name="membership_number" type="text"></div>
    <div class="form-row"><label>Name (required)</label><input name="name" type="text"></div>
    <div class="form-row"><label>Email</label><input name="email" type="text"></div>
    <div class="form-row"><label>Duration</label>
      <select name="duration">
        <option value="6" selected>6 months</option>
        <option value="12">1 year</option>
        <option value="24">2 years</option>
      </select>
    </div>
    <button type="submit">Add Membership</button>
  </form>
</div>
</body></html>