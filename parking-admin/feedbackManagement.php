<?php
include "db.php";

$studentResult = $conn->query("SELECT * FROM student_feedback ORDER BY created_at DESC");
$staffResult = $conn->query("SELECT * FROM staff_feedback ORDER BY created_at DESC");
$visitorResult = $conn->query("SELECT * FROM visitor_feedback ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Admin - Feedback</title>
        <script src="https://kit.fontawesome.com/6c491f9239.js"></script>
        <link rel="stylesheet" href="feedbackManagement.css"> 
        <!-- <style>
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
            th { background: #f2f2f2; }
        </style> -->
    </head>
    <body>
        <div class="sidebar">
            <div class="logo">

            </div>
            <ul class="menu">
                <li>
                    <a href="adminDashboard.html">
                        <i class="fa-regular fa-house"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="ParkingSpaces.html">
                        <i class="fa-solid fa-square-parking"></i>
                        <span>Parking Spaces</span>
                    </a>
                </li>
                <li>
                    <a href="userManagement.php">
                        <i class="fa-solid fa-users"></i>
                        <span>Manage Users</span>
                    </a>
                </li>
                <li>
                    <a href="visitorAccess.php">
                        <i class="fa-solid fa-question"></i>
                        <span>Visitor Requests</span>
                    </a>
                </li>
                <li>
                    <a href="parkingReports.php">
                        <i class="fa-solid fa-chart-simple"></i>
                        <span>Parking Reports</span>
                    </a>
                </li>
                <li class="active">
                    <a href="feedbackManagement.php">
                        <i class="fa-regular fa-comment-dots"></i>
                        <span>Feedback Management</span>
                    </a>
                </li>
                <li class="logout">
                    <a href="../role-selection/roleSelection.html">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Log out</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="main--content">
            <div class="header--wrapper">
                <div class="header--title">
                    <span>Hello,</span>
                    <h2>Welcome Back</h2>
                </div>
                <div class="user--info">
                    <div class="search--box">
                        <i class="fa-solid fa-magnifying-glass" id="search"></i>
                        <input type="text" placeholder="search"> 
                    </div>
                    <img src="../images/parking-placeholder.png" alt="Parking Administrator">
                </div>
            </div>
            <section class="feedback">
            <h1>Student Feedback</h1>

            <table>
                <thead>
                    <tr>
                    <th>Name</th>
                    <th>Experience</th>
                    <th>Rating</th>
                    <th>Message</th>
                    <th>Date</th>
                    </tr> 
                    <tbody>
                    <?php while ($row = $studentResult->fetch_assoc()): ?>
                    <tr>
                    <td><?= $row["name"] ?></td>
                    <td><?= htmlspecialchars($row["experience"]) ?></td>
                    <td><?= str_repeat("★", $row["rating"]) ?></td>
                    <td><?= htmlspecialchars($row["message"]) ?></td>
                    <td><?= $row["created_at"] ?></td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </thead>
            </table>
            </section>
            <section class="feedback">
            <h1>Staff Feedback</h1>

            <table>
                <thead>
                    <tr>
                    <th>Name</th>
                    <th>Experience</th>
                    <th>Rating</th>
                    <th>Message</th>
                    <th>Date</th>
                    </tr>
                    <tbody>
                    <?php while ($row = $staffResult->fetch_assoc()): ?>
                    <tr>
                    <td><?= $row["name"] ?></td>
                    <td><?= htmlspecialchars($row["experience"]) ?></td>
                    <td><?= str_repeat("★", $row["rating"]) ?></td>
                    <td><?= htmlspecialchars($row["message"]) ?></td>
                    <td><?= $row["created_at"] ?></td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </thead>
            </table>
            </section>
            <section class="feedback">
            <h1>Visitor Feedback</h1>

            <table>
                <thead>
                    <tr>
                    <th>Name</th>
                    <th>Experience</th>
                    <th>Rating</th>
                    <th>Message</th>
                    <th>Date</th>
                    </tr>
                    <tbody>

                        <?php while ($row = $visitorResult->fetch_assoc()): ?>
                        <tr>
                    <td><?= $row["name"] ?></td>
                    <td><?= htmlspecialchars($row["experience"]) ?></td>
                    <td><?= str_repeat("★", $row["rating"]) ?></td>
                    <td><?= htmlspecialchars($row["message"]) ?></td>
                    <td><?= $row["created_at"] ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </thead>
            </table>
        </div>  
    </body>
</html>