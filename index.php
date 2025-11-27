<?php
require_once 'config.php';
require_once 'functions.php';

if (is_logged_in()) {
    // redirect based on role
    if (is_admin()) header('Location: dashboard_admin.php');
    else header('Location: dashboard_user.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter username and password.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            // set session
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'full_name' => $user['full_name'],
                'role' => $user['role']
            ];
            if ($user['role'] === 'admin') header('Location: dashboard_admin.php');
            else header('Location: dashboard_user.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Library Login</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
  <div class="header"><h1>Library Management System - Login</h1></div>

  <?php if ($error): ?><div class="error"><?=htmlspecialchars($error)?></div><?php endif; ?>

  <form method="post" novalidate>
    <div class="form-row">
      <label>Username</label>
      <input type="text" name="username" required>
    </div>
    <div class="form-row">
      <label>Password</label>
      <input type="password" name="password" required> <!-- password hidden as required -->
    </div>
    <button type="submit">Login</button>
  </form>

  <div class="footer small">
    Default accounts: admin/admin123, user/user123
  </div>
</div>
</body>
</html>