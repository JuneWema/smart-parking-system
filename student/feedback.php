<?php

$host = "localhost";
$user = "root";
$password = "";         
$database = "smart-parking";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name       = trim($_POST["name"] ?? "");
    $email      = trim($_POST["email"] ?? "");
    $type       = $_POST["type"] ?? "";
    $experience = $_POST["experience"] ?? "";
    $rating     = (int) ($_POST["rating"] ?? 0);
    $message    = trim($_POST["message"] ?? "");
             
    $allowed_types = ["suggestion", "bug-report", "compliment", "complaint", "other"];

    if ($name === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Please enter a valid name and email.");
    }
    if (!in_array($type, $allowed_types)) {
        die("Please choose a feedback type.");
    }
    if ($experience === "" || $rating < 1 || $rating > 5) {
        die("Please select your experience and a star rating.");
    }

    $stmt = $conn->prepare(
        "INSERT INTO student_feedback (name, email, type, experience, rating, message)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("ssssis", $name, $email, $type, $experience, $rating, $message);

    if ($stmt->execute()) {
        header("Location: feedback.html?success=1");
        exit;
    } else {
        echo "Something went wrong. Please try again.";
    }

    $stmt->close();
}
$conn->close();