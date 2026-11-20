<?php
// Include this on every page that only logged-in students may use:
//     require "auth.php";

session_start();
define("KEY_COLUMN", "student_id");

if (!isset($_SESSION["student_id"])) {
    header("Location: login.php");
    exit;
}

$studentId = $_SESSION["student_id"]; 

function redirectWithMessage($section, $success, $text) {
    $_SESSION["flash"] = [
        "section" => $section,
        "success" => $success,
        "text"    => $text
    ];
    header("Location: settings.php");
    exit;
}

// Your login.php must set:  $_SESSION["student_id"] = (the id from the students table);
/*if (!isset($_SESSION["student_id"])) {
    header("Location: login.php");
    exit;
}

$studentId = (int) $_SESSION["student_id"];

// Saves a message, then sends the student back to the settings page.
// $section is "profile" or "password" (decides where the message appears).
function redirectWithMessage($section, $success, $text) {
    $_SESSION["flash"] = [
        "section" => $section,
        "success" => $success,
        "text"    => $text
    ];
    header("Location: settings.php");
    exit;
}
    */
