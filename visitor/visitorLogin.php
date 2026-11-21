<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: visitorLogin.html');
    exit;
}

$visitorId = trim($_POST['visitorId'] ?? '');
$password  = $_POST['password'] ?? '';

// Server-side check
if (!preg_match('/^\d{9}$/', $visitorId) || $password === '') {
    header('Location: visitorLogin.html?error=1');
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=smart-parking;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$stmt = $pdo->prepare('SELECT visitor_id, full_name, password_hash FROM visitors WHERE visitor_id = ?');
$stmt->execute([$visitorId]);
$visitor = $stmt->fetch(PDO::FETCH_ASSOC);

if ($visitor && password_verify($password, $visitor['password_hash'])) {
    session_regenerate_id(true);               // prevents session fixation
    $_SESSION['visitor_id'] = $visitor['visitor_id'];
    $_SESSION['full_name']  = $visitor['full_name'];

    header('Location: ../visitor/visitorDashboard.html');
    exit;
}

// Same message whether the ID or the password was wrong
header('Location: visitorLogin.html?error=1');
exit;