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
        <title>Manage Users</title>
        <script src="https://kit.fontawesome.com/6c491f9239.js"></script>
        <link rel="stylesheet" href="userManagement.css"> 
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
                <li class="active">
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
             <section class="manage">
            <h1>Manage Students</h1>
            <div class="add_user">
                <h3>Add a Student</h3>
                <p>Click <a href="../student/registration.html">here</a> to add a student</p>
            </div>
            <table>
                <thead>
                    <tr>
                    <th>Student ID</th>
                    <th>Phone</th>
                    <th>Vehicle Number</th>
                    <th>Vehicle Type</th>
                    <th>Created At</th>
                    <th>Delete</th>
                    <th>Update</th>
                    </tr>
                    <tbody>

                    <?php while ($row = $studentResult->fetch_assoc()): ?>
                    <tr data-type="student" data-id="<?= htmlspecialchars($row['student_id']) ?>">
                    <td><?= htmlspecialchars($row["student_id"]) ?></td>
                    <td><?= htmlspecialchars($row["phone"]) ?></td>
                    <td><?= htmlspecialchars($row["reg_number"]) ?></td>
                    <td><?= htmlspecialchars($row["car_type"]) ?></td>
                    <td><?= $row["created_at"] ?></td>
                    <td><button type="button" class="delete-btn"><i class="fa-solid fa-trash"></i></button></td>
                    <td><button type="button" class="edit-btn"><i class="fa-solid fa-pen"></i></button></td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                    </thead>
            </table>
                    </section>
                    <section class="manage">
            
            <h1>Manage Staffs</h1>
            <div class="add_user">
                <h3>Add a Staff</h3>
                <p>Click <a href="../staff/staffRegistration.html">here</a> to add a staff</p>
            </div>
            <table>
                <thead>
                <tr>
                    <th>Staff ID</th>
                    <th>Phone</th>
                    <th>Vehicle Number</th>
                    <th>Vehicle Type</th>
                    <th>Created At</th>
                    <th>Delete</th>
                    <th>Update</th>
                </tr>
                <tbody>

                <?php while ($row = $staffResult->fetch_assoc()): ?>
                <tr data-type="staff" data-id="<?= htmlspecialchars($row['staff_id']) ?>">
                    <td><?= htmlspecialchars($row["staff_id"]) ?></td>
                    <td><?= htmlspecialchars($row["phone"]) ?></td>
                    <td><?= htmlspecialchars($row["reg_number"]) ?></td>
                    <td><?= htmlspecialchars($row["car_type"]) ?></td>
                    <td><?= $row["created_at"] ?></td>
                    <td><button type="button" class="delete-btn"><i class="fa-solid fa-trash"></i></button></td>
                    <td><button type="button" class="edit-btn"><i class="fa-solid fa-pen"></i></button></td>
                </tr>
                <?php endwhile; ?>
                </tbody>
                </thead>
            </table>
                </section>
            
             <section class="manage">
            <h1> Manage Visitors</h1>
            <div class="add_user">
                <h3>Add a Visitor</h3>
                <p>Click <a href="../visitor/visitorRegistration.html">here</a> to add a visitor</p>
            </div>
            <table>
                <thead>
                    <tr>
                    <th>Visitor ID</th>
                    <th>Phone</th>
                    <th>Vehicle Number</th>
                    <th>Vehicle Type</th>
                    <th>Created At</th>
                    <th>Delete</th>
                    <th>Update</th>
                    </tr>
                    <tbody>

                    <?php while ($row = $visitorResult->fetch_assoc()): ?>
                    <tr data-type="visitor" data-id="<?= htmlspecialchars($row['visitor_id']) ?>">
                    <td><?= htmlspecialchars($row["visitor_id"]) ?></td>
                    <td><?= htmlspecialchars($row["phone"]) ?></td>
                    <td><?= htmlspecialchars($row["reg_number"]) ?></td>
                    <td><?= htmlspecialchars($row["car_type"]) ?></td>
                    <td><?= $row["created_at"] ?></td>
                    <td><button type="button" class="delete-btn"><i class="fa-solid fa-trash"></i></button></td>
                    <td><button type="button" class="edit-btn"><i class="fa-solid fa-pen"></i></button></td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                    </thead>
            </table>
                </section> 
        </div> 
        
        <!-- <div id="editModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); align-items:center; justify-content:center;">
            <div style="background:#fff; padding:20px; border-radius:8px; min-width:300px;">
                <h3>Edit User</h3>
                <input type="hidden" id="editType">
                <input type="hidden" id="editId">
                <p><label>Phone<br><input type="text" id="editPhone"></label></p>
                <p><label>Vehicle Number<br><input type="text" id="editReg"></label></p>
                <p><label>Vehicle Type<br><input type="text" id="editCar"></label></p>
                <button type="button" id="saveEdit">Save</button>
                <button type="button" id="cancelEdit">Cancel</button>
            </div>
        </div> --> 
    </body>
</html>