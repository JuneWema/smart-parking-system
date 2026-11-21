<?php
session_start();

define("KEY_COLUMN", "staff_id");          // the unique column in the staffs table

if (!isset($_SESSION["staff_id"])) {
    header("Location: staffLogin.php");    // your STAFF login page
    exit;
}

$staffId = $_SESSION["staff_id"];

function redirectWithMessage($section, $success, $text) {
    $_SESSION["flash"] = [
        "section" => $section,
        "success" => $success,
        "text"    => $text
    ];
    header("Location: staffSettings.php"); // not settings.php
    exit;
}