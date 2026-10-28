<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.html');
    exit;
}

$studentId = trim($_POST['studentId'] ?? '');
$password  = $_POST['password'] ?? '';

// Server-side check
if (!preg_match('/^\d{6}$/', $studentId) || $password === '') {
    header('Location: login.html?error=1');
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=smart-parking;charset=utf8mb4', 'db_user', 'db_pass', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

$stmt = $pdo->prepare('SELECT student_id, full_name, password_hash FROM students WHERE student_id = ?');
$stmt->execute([$studentId]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if ($student && password_verify($password, $student['password_hash'])) {
    session_regenerate_id(true);               // prevents session fixation
    $_SESSION['student_id'] = $student['student_id'];
    $_SESSION['full_name']  = $student['full_name'];

    header('Location: studentDash.php');
    exit;
}

// Same message whether the ID or the password was wrong
header('Location: login.html?error=1');
exit;