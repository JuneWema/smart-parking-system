<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: adminLogin.html');
    exit;
}

$workId = trim($_POST['workId'] ?? '');
$password  = $_POST['password'] ?? '';

// Server-side check
if (!preg_match('/^\d{6}$/', $workId) || $password === '') {
    header('Location: adminLogin.html?error=1');
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=smart-parking;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$stmt = $pdo->prepare('SELECT work_id, full_name, password_hash FROM admins WHERE work_id = ?');
$stmt->execute([$workId]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if ($admin && password_verify($password, $admin['password_hash'])) {
    session_regenerate_id(true);               // prevents session fixation
    $_SESSION['work_id'] = $admin['work_id'];
    $_SESSION['full_name']  = $admin['full_name'];

    header('Location: ../parking-admin/adminDashboard.html');
    exit;
}

// Same message whether the ID or the password was wrong
header('Location: adminLogin.html?error=1');
exit;