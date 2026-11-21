<?php
// Database connection. Include this file on any page that needs the database:
//     require "db.php";

// Show real errors while developing (remove these 3 lines later)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set("display_errors", 1);
error_reporting(E_ALL);

$host     = "localhost";
$user     = "root";
$password = "";
$database = "smart-parking";

$conn = new mysqli($host, $user, $password, $database);