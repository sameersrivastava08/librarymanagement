<?php
// functions.php - shared helpers
require_once 'config.php';

function flash($key, $msg = null) {
    if ($msg === null) {
        if (!empty($_SESSION['flash'][$key])) {
            $m = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $m;
        }
        return null;
    }
    $_SESSION['flash'][$key] = $msg;
}

function today() {
    return date('Y-m-d');
}

function add_days($date, $days) {
    return date('Y-m-d', strtotime("$date +$days days"));
}

function calc_fine($due_date, $returned_date) {
    $due_ts = strtotime($due_date);
    $ret_ts = strtotime($returned_date);
    if ($ret_ts <= $due_ts) return 0.0;
    $days = floor(($ret_ts - $due_ts) / 86400);
    $rate = 1.00; // 1 currency unit per day
    return $days * $rate;
}
?>