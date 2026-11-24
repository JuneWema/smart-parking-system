<?php
include "db.php";

$studentResult = $conn->query("SELECT * FROM students ORDER BY created_at DESC");
$staffResult = $conn->query("SELECT * FROM staffs ORDER BY created_at DESC");
$visitorResult = $conn->query("SELECT * FROM visitors ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Parking Reports</title>
        <script src="https://kit.fontawesome.com/6c491f9239.js"></script>
        <link rel="stylesheet" href="parkingReports.css"> 
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
                <li class="active">
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
            <section class="report">
            <h1>Students Report</h1>

            <table>
                <thead>
                    <tr>
                    <th>Student ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Vehicle Number</th>
                    <th>Car Type</th>
                    <th>Created At</th>
                    </tr>
                    <tbody>

                    <?php while ($row = $studentResult->fetch_assoc()): ?>
                    <tr>
                    <td><?= $row["student_id"] ?></td>
                    <td><?= htmlspecialchars($row["full_name"]) ?></td>
                    <td><?= htmlspecialchars($row["email"]) ?></td>
                    <td><?= htmlspecialchars($row["phone"]) ?></td>
                    <td><?= htmlspecialchars($row["reg_number"]) ?></td>
                    <td><?= htmlspecialchars($row["car_type"]) ?></td>
                    <td><?= $row["created_at"] ?></td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                    </thead>
            </table>
                    </section>
                    <section class="report">
            
            <h1>Staffs Report</h1>

            <table>
                <thead>
                    <tr>
                    <th>Staff ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Vehicle Number</th>
                    <th>Car Type</th>
                    <th>Created At</th>
                    </tr>
                    <tbody>

                    <?php while ($row = $staffResult->fetch_assoc()): ?>
                    <tr>
                    <td><?= $row["staff_id"] ?></td>
                    <td><?= htmlspecialchars($row["full_name"]) ?></td>
                    <td><?= htmlspecialchars($row["email"]) ?></td>
                    <td><?= htmlspecialchars($row["phone"]) ?></td>
                    <td><?= htmlspecialchars($row["reg_number"]) ?></td>
                    <td><?= htmlspecialchars($row["car_type"]) ?></td>
                    <td><?= $row["created_at"] ?></td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                    </thead>
            </table>
                    </section>
                    <section class="report">

            <h1>Visitors Report</h1>

            <table>
                <thead>
                    <tr>
                    <th>Visitor ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Vehicle Number</th>
                    <th>Car Type</th>
                    <th>Created At</th>
                    </tr>
                    <tbody>

                    <?php while ($row = $visitorResult->fetch_assoc()): ?>
                    <tr>
                    <td><?= $row["visitor_id"] ?></td>
                    <td><?= htmlspecialchars($row["full_name"]) ?></td>
                    <td><?= htmlspecialchars($row["email"]) ?></td>
                    <td><?= htmlspecialchars($row["phone"]) ?></td>
                    <td><?= htmlspecialchars($row["reg_number"]) ?></td>
                    <td><?= htmlspecialchars($row["car_type"]) ?></td>
                    <td><?= $row["created_at"] ?></td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                    </thead>
            </table>
                    </section>
        </div>  
    </body>
</html>