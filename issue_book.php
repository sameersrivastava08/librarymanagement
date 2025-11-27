<?php
// process issuing a selected book
require_once 'config.php';
require_once 'functions.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: search.php'); exit;
}

$book_id = intval($_POST['book_id'] ?? 0);
$membership_no = trim($_POST['membership_no'] ?? '');
$issue_date = $_POST['issue_date'] ?? '';
$return_date = $_POST['return_date'] ?? '';
$remarks = trim($_POST['remarks'] ?? '');

$errors = [];
if (!$book_id) $errors[] = 'Select a book using the radio button.';
if ($issue_date === '' || $return_date === '') $errors[] = 'Issue and Return dates required.';
if ($issue_date < today()) $errors[] = 'Issue Date cannot be earlier than today.';
$max_return = add_days($issue_date, 15);
if (strtotime($return_date) > strtotime($max_return)) $errors[] = 'Return Date cannot be greater than 15 days from issue date.';

if ($membership_no !== '') {
    $stmt = $pdo->prepare("SELECT * FROM members WHERE membership_number = ?");
    $stmt->execute([$membership_no]);
    $member = $stmt->fetch();
    if (!$member) $errors[] = 'Membership number not found.';
    else $member_id = $member['id'];
} else {
    $member_id = null;
}

if ($errors) {
    foreach ($errors as $e) flash('error', $e);
    header('Location: search.php?q=' . urlencode($_POST['q'] ?? ''));
    exit;
}

// reduce copies if available
$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ? FOR UPDATE");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();
    if (!$book) throw new Exception('Book not found');
    if ($book['copies'] <= 0) throw new Exception('No copies available');

    $stmt2 = $pdo->prepare("INSERT INTO transactions (user_id, member_id, book_id, serial_no, issue_date, return_date, remarks) VALUES (?,?,?,?,?,?,?)");
    $stmt2->execute([$_SESSION['user']['id'], $member_id, $book['id'], $book['serial_no'], $issue_date, $return_date, $remarks]);

    $stmt3 = $pdo->prepare("UPDATE books SET copies = copies - 1 WHERE id = ?");
    $stmt3->execute([$book['id']]);

    $pdo->commit();
    flash('success','Book issued successfully.');
    header('Location: '.(is_admin() ? 'dashboard_admin.php' : 'dashboard_user.php'));
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    flash('error','Issue failed: '.$e->getMessage());
    header('Location: search.php');
    exit;
}
?>