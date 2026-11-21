<?php
// Saves the personal + vehicle details. The form in settings.php sends data here.

require "staffSettingsDb.php";
require "staffSettingsAuth.php";

// Only accept a submitted form (not someone typing the address in the browser)
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: staffSettings.php");
    exit;
}

// 1. Read the form values
$fullName      = trim($_POST["fullName"] ?? "");
$email         = trim($_POST["email"] ?? "");
$phone         = trim($_POST["telephone"] ?? "");
$vehicleNumber = strtoupper(trim($_POST["regNumber"] ?? ""));
$vehicleColor  = trim($_POST["color"] ?? "");
$vehicleType   = $_POST["vehicleType"] ?? "";

$allowedTypes = ["saloon", "suv", "van", "pick-up", "other"];

// 2. Validate
if ($fullName === "") {
    redirectWithMessage("profile", false, "Please enter your full name.");
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    redirectWithMessage("profile", false, "Please enter a valid email address.");
}
if ($phone !== "" && !preg_match('/^\+?[0-9 ]{9,15}$/', $phone)) {
    redirectWithMessage("profile", false, "Please enter a valid phone number.");
}
if (!in_array($vehicleType, $allowedTypes)) {
    redirectWithMessage("profile", false, "Please choose a valid vehicle type.");
}

// 3. Make sure no OTHER student already uses this email
$check = $conn->prepare(
    "SELECT " . KEY_COLUMN . " FROM staffs WHERE email = ? AND " . KEY_COLUMN . " <> ?"
);
$check->bind_param("ss", $email, $staffId);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    redirectWithMessage("profile", false, "That email is already used by another account.");
}
$check->close();

// 4. Save
$stmt = $conn->prepare(
    "UPDATE staffs
     SET full_name = ?, email = ?, phone = ?,
        reg_number = ?, color = ?, car_type = ?
     WHERE " . KEY_COLUMN . " = ?"
);
$stmt->bind_param("sssssss",
    $fullName, $email, $phone,
    $vehicleNumber, $vehicleColor, $vehicleType,
    $staffId
);
$stmt->execute();
$stmt->close();

redirectWithMessage("profile", true, "Your details have been saved.");