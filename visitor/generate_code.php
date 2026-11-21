<?php
session_start();
require __DIR__ . '/visitorDp.php';
header('Content-Type: application/json');

if (!isset($_SESSION['visitor_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Not allowed']);
    exit;
}

$allowed = ['admission','meeting','academicVisit','interview','delivery',
            'event','business','staffVisit','studentVisit','maintanance','other'];
$purpose = $_POST['purpose'] ?? '';

if (!in_array($purpose, $allowed, true)) {
    echo json_encode(['success' => false, 'message' => 'Please select a valid purpose.']);
    exit;
}

$check = $pdo->prepare("SELECT COUNT(*) FROM access_codes WHERE code = ? AND expires_at > NOW()");
do {
    $code = (string) random_int(100000, 999999);
    $check->execute([$code]);
} while ($check->fetchColumn() > 0);

$created = date('Y-m-d H:i:s');
$expires = date('Y-m-d H:i:s', strtotime('+24 hours'));

$insert = $pdo->prepare(
    "INSERT INTO access_codes (visitor_id, purpose, code, created_at, expires_at)
     VALUES (?, ?, ?, ?, ?)"
);
$insert->execute([$_SESSION['visitor_id'], $purpose, $code, $created, $expires]);

echo json_encode([
    'success' => true,
    'code'    => $code,
    'created' => date('d-m-Y H:i', strtotime($created)),
    'expires' => date('d-m-Y H:i', strtotime($expires)),
    'purpose' => ucfirst($purpose),
    'name'    => $_SESSION['full_name']
]);