
<?php
//echo '<pre>'; print_r($_POST); echo '</pre>'; exit;
// register.php: handles the student registration form
 
// ---------- Database settings (default XAMPP) ----------
const DB_HOST = 'localhost';
const DB_NAME = 'smart-parking';   // <-- change to your database name
const DB_USER = 'root';      // default XAMPP user
const DB_PASS = '';          // default XAMPP password is empty
 
// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: registration.html');
    exit;
}
 
// Small helper to show errors and stop
function fail(array $errors): void
{
    http_response_code(400);
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Registration error</title></head><body>';
    echo '<h3>Please fix the following:</h3><ul>';
    foreach ($errors as $err) {
        echo '<li>' . htmlspecialchars($err, ENT_QUOTES, 'UTF-8') . '</li>';
    }
    echo '</ul><p><a href="javascript:history.back()">Go back</a></p></body></html>';
    exit;
}
 
// ---------- 1. Collect input ----------
$fullName       = trim($_POST['fullName'] ?? '');
$email          = strtolower(trim($_POST['email'] ?? ''));
$identification = trim($_POST['identification'] ?? '');
$telephone      = preg_replace('/\s+/', '', trim($_POST['telephone'] ?? ''));
$regNumber      = strtoupper(trim($_POST['regNumber'] ?? ''));
$carType        = $_POST['carType'] ?? '';
$pass1          = $_POST['pass1'] ?? '';
$pass2          = $_POST['pass2'] ?? '';
 
// ---------- 2. Validate (never trust JS alone) ----------
$errors = [];
 
if ($fullName === '' || mb_strlen($fullName) > 100) {
    $errors[] = 'Full name is required (max 100 characters).';
}
 
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) {
    $errors[] = 'Provide a valid email address.';
}
 
if (!preg_match('/^\d{6}$/', $identification)) {
    $errors[] = 'Student ID must be exactly 6 digits.';
}
 
if (!preg_match('/^(\+254|0)\d{9}$/', $telephone)) {
    $errors[] = 'Enter a valid phone number.';
}
 
if (!preg_match('/^[A-Z0-9 ]{5,15}$/', $regNumber)) {
    $errors[] = 'Enter a valid vehicle registration number.';
}
 
$allowedTypes = ['saloon', 'suv', 'van', 'pickup', 'other'];
if (!in_array($carType, $allowedTypes, true)) {
    $errors[] = 'Select a valid vehicle type.';
}
 
if (strlen($pass1) < 8
    || !preg_match('/[A-Z]/', $pass1)
    || !preg_match('/[a-z]/', $pass1)
    || !preg_match('/\d/', $pass1)
    || !preg_match('/[^A-Za-z0-9]/', $pass1)) {
    $errors[] = 'Password must be at least 8 characters with an uppercase letter, a lowercase letter, a number and a special character.';
}
 
if ($pass1 !== $pass2) {
    $errors[] = 'Passwords do not match.';
}
 
if ($errors) {
    fail($errors);
}
 
// ---------- 3. Connect to the database ----------
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit('Could not connect to the database. Check the settings at the top of register.php.');
}
 
// ---------- 4. Check for duplicates ----------
$check = $pdo->prepare('SELECT student_id, email FROM student_reg WHERE student_id = ? OR email = ?');
$check->execute([$identification, $email]);
 
foreach ($check->fetchAll() as $row) {
    if ($row['student_id'] === $identification) {
        $errors[] = 'An account with that student ID already exists.';
    }
    if ($row['email'] === $email) {
        $errors[] = 'An account with that email already exists.';
    }
}
 
if ($errors) {
    fail(array_values(array_unique($errors)));
}
 
// ---------- 5. Hash the password and insert ----------
$hash = password_hash($pass1, PASSWORD_DEFAULT);
 
try {
    $stmt = $pdo->prepare(
        'INSERT INTO students (student_id, full_name, email, phone, reg_number, car_type, password_hash)
         VALUES (?, ?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$identification, $fullName, $email, $telephone, $regNumber, $carType, $hash]);
} catch (PDOException $e) {
    // 23000 = duplicate key (e.g. two people registering at the same moment)
    if ($e->getCode() === '23000') {
        fail(['An account with that student ID or email already exists.']);
    }
    http_response_code(500);
    exit('Something went wrong while saving your account. Please try again.');
}
 
// ---------- 6. Success: go to the login page ----------
header('Location: login.html');
exit;
 