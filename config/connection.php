<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "smart-parking"

$conn = new mysqli(
    $host,
    $username,
    $password,
    $database
);

if($conn -> connect_error) {
    die("Database Connection Failed.");
}

$conn -> set_charset("utf8mb4");
?>