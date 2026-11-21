<?php
session_start();

define("KEY_COLUMN", "visitor_id");          // the unique column in the staffs table

if (!isset($_SESSION["visitor_id"])) {
    header("Location: visitorLogin.php");    // your STAFF login page
    exit;
}

$visitorId = $_SESSION["visitor_id"];

function redirectWithMessage($section, $success, $text) {
    $_SESSION["flash"] = [
        "section" => $section,
        "success" => $success,
        "text"    => $text
    ];
    header("Location: visitorSettings.php"); 
    exit;
}