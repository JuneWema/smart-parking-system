 <?php
// Changes the student's password. The password form in settings.php sends data here.
// Uses the column "password_hash" (same as login.php) and KEY_COLUMN from auth.php.

require "staffSettingsDb.php";
require "staffSettingsAuth.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: staffSettings.php");
    exit;
}

// 1. Read the form values (do NOT trim passwords: spaces can be part of them)
$currentPassword = $_POST["currentPassword"] ?? "";
$newPassword     = $_POST["newPassword"] ?? "";
$confirmPassword = $_POST["confirmPassword"] ?? "";

// 2. Get the stored (hashed) password for this student
$stmt = $conn->prepare("SELECT password_hash FROM staffs WHERE " . KEY_COLUMN . " = ?");
$stmt->bind_param("s", $staffId);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

// 3. Validate
if (!$row || !password_verify($currentPassword, $row["password_hash"])) {
    redirectWithMessage("password", false, "Your current password is incorrect.");
}
if (strlen($newPassword) < 8) {
    redirectWithMessage("password", false, "The new password must be at least 8 characters.");
}
if ($newPassword !== $confirmPassword) {
    redirectWithMessage("password", false, "The new passwords do not match.");
}
if ($newPassword === $currentPassword) {
    redirectWithMessage("password", false, "The new password must be different from the current one.");
}

// 4. Save the HASH, never the plain password
$newHash = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE staffs SET password_hash = ? WHERE " . KEY_COLUMN . " = ?");
$stmt->bind_param("ss", $newHash, $staffId);
$stmt->execute();
$stmt->close();

redirectWithMessage("password", true, "Your password has been changed.");