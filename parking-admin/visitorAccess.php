<?php
include "db.php";

$result = $conn->query("SELECT * FROM access_codes ORDER BY created_at DESC");

?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Admin - Feedback</title>
        <script src="https://kit.fontawesome.com/6c491f9239.js"></script>
        <link rel="stylesheet" href="visitorAccess.css"> 
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
                <li class="active">
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
                <li>
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
            <section class="access">
            <h1>Generated Access Codes Records</h1>

            <table>
                <thead>
                    <tr>
                    <th>Number</th>
                    <th>Visitor ID</th>
                    <th>Purpose</th>
                    <th>Generated Code</th>
                    <th>Created At</th>
                    <th>Expires At</th>
                    </tr>
                    <tbody>

                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                    <td><?= $row["number"] ?></td>
                    <td><?= htmlspecialchars($row["visitor_id"]) ?></td>
                    <td><?= htmlspecialchars($row["purpose"]) ?></td>
                    <td><?= htmlspecialchars($row["code"]) ?></td>
                    <td><?= $row["created_at"] ?></td>
                    <td><?= $row["expires_at"] ?></td>
                    </tr>
                    <?php endwhile; ?>
                    <tbody>
                </thead>
            </table>
            </section>
        </div>
    </body>
</html>