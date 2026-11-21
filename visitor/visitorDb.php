<?php
date_default_timezone_set('Africa/Nairobi');

$pdo = new PDO(
    'mysql:host=localhost;dbname=smart-parking;charset=utf8mb4',
    'root',      // XAMPP default user
    '',          // XAMPP default password is empty
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);
$pdo->exec("SET time_zone = '+03:00'");