<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: staffLogin.html');
    exit;
}

$staffId = trim($_POST['staffId'] ?? '');
$password  = $_POST['password'] ?? '';

// Server-side check
if (!preg_match('/^\d{6}$/', $staffId) || $password === '') {
    header('Location: staffLogin.html?error=1');
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=smart-parking;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$stmt = $pdo->prepare('SELECT staff_id, full_name, password_hash FROM staffs WHERE staff_id = ?');
$stmt->execute([$staffId]);
$staff = $stmt->fetch(PDO::FETCH_ASSOC);

if ($staff && password_verify($password, $staff['password_hash'])) {
    session_regenerate_id(true);               // prevents session fixation
    $_SESSION['staff_id'] = $staff['staff_id'];
    $_SESSION['full_name']  = $staff['full_name'];

    header('Location: ../staff/staffDashboard.html');
    exit;
}

// Same message whether the ID or the password was wrong
header('Location: staffLogin.html?error=1');
exit;