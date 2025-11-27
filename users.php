<?php
require_once 'config.php';
require_once 'functions.php';
require_login();
if (!is_admin()) { echo "Access denied."; exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $full = trim($_POST['full_name']);
    $role = $_POST['role'] ?? 'user';
    if ($username === '' || $password === '' || $full === '') $errors[] = 'All fields required';
    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username,password,full_name,role) VALUES (?,?,?,?)");
        try { $stmt->execute([$username,$hash,$full,$role]); flash('success','User added'); header('Location: users.php'); exit; }
        catch (Exception $e) { $errors[] = 'Could not add user: '.$e->getMessage(); }
    }
}

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
$notice = flash('success');
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>User Management</title><link rel="stylesheet" href="styles.css"></head>
<body>
<div class="container">
  <div class="header"><h1>User Management</h1><div><a href="dashboard_admin.php">Back</a></div></div>

  <?php if ($errors): foreach ($errors as $er): ?><div class="error"><?=htmlspecialchars($er)?></div><?php endforeach; endif; ?>
  <?php if ($notice): ?><div class="notice"><?=htmlspecialchars($notice)?></div><?php endif; ?>

  <h3>Create User</h3>
  <form method="post">
    <div class="form-row"><label>Username</label><input name="username" type="text"></div>
    <div class="form-row"><label>Password</label><input name="password" type="password"></div>
    <div class="form-row"><label>Full Name</label><input name="full_name" type="text"></div>
    <div class="form-row"><label>Role</label>
      <select name="role"><option value="user">user</option><option value="admin">admin</option></select>
    </div>
    <button name="create_user" type="submit">Create</button>
  </form>

  <h3>Existing Users</h3>
  <table class="table"><thead><tr><th>Username</th><th>Full Name</th><th>Role</th></tr></thead>
  <tbody>
    <?php foreach($users as $u): ?><tr><td><?=htmlspecialchars($u['username'])?></td><td><?=htmlspecialchars($u['full_name'])?></td><td><?=htmlspecialchars($u['role'])?></td></tr><?php endforeach; ?>
  </tbody></table>
</div>
</body></html>