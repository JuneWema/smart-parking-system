<?php
// This page only DISPLAYS things. Saving is done by update_profile.php
// and change_password.php.

require "settingsDb.php";
require "settingsAuth.php";

// Makes text safe to print inside HTML
function e($text) {
    return htmlspecialchars($text ?? "", ENT_QUOTES, "UTF-8");
}

// Vehicle types for the dropdown
$vehicleTypes = [
    "saloon"  => "Saloon",
    "suv"     => "SUV",
    "van"     => "Van",
    "pick-up" => "Pick-up",
    "other"   => "Other"
];

// Message left by update_profile.php or change_password.php (shown once)
$flash = $_SESSION["flash"] ?? null;
unset($_SESSION["flash"]);

$profileFlash  = ($flash && $flash["section"] === "profile")  ? $flash : null;
$passwordFlash = ($flash && $flash["section"] === "password") ? $flash : null;

// Load the student's details to fill the form
$stmt = $conn->prepare(
    "SELECT full_name, email, phone, student_id,
            reg_number, color, car_type
     FROM students WHERE " . KEY_COLUMN . " = ?"
);
$stmt->bind_param("s", $studentId);
$stmt->bind_param("i", $studentId);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) {
    die("Student not found.");
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://kit.fontawesome.com/6c491f9239.js"></script>
        <link rel="stylesheet" href="settings.css"> 
        <title>Settings</title>
    </head>
    <body>
        <div class="sidebar">
            <div class="logo"></div>
            <ul class="menu">
                <li>
                    <a href="dashboard.html">
                        <i class="fa-regular fa-house"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="booking.html">
                        <i class="fa-solid fa-calendar"></i>
                        <span>Book Space</span>
                    </a>
                </li>
                <li>
                    <a href="feedback.html">
                        <i class="fa-regular fa-comment-dots"></i>
                        <span>Give Feedback</span>
                    </a>
                </li>
                <li>
                    <a href="notification.html">
                        <i class="fa-regular fa-bell"></i>
                        <span>Notifications</span>
                    </a>
                </li>
                <li>
                    <a href="contact.html">
                        <i class="fa-solid fa-phone"></i>
                        <span>Contact Us</span>
                    </a>
                </li>
                <li class="active">
                    <a href="settings.php">
                       <i class="fa-solid fa-gear"></i>
                        <span>Settings</span>
                    </a>
                </li>
                <li>
                    <a href="navigation.html">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>Navigation Map</span>
                    </a>
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
                    <img src="../images/studentDp.jpg" alt="Joyce Omondi">
                </div>
            </div>
            <div class="setting-section">
                <div class="setting-form-container">
                    <form action="update_profile.php" method="POST" id="settingsForm">
                        <div class="row-1">
                            <div class="rol1-text">
                                <p class="heading">Settings</p>
                                <p class="desc">Manage your account settings and preferences</p>
                            </div>
                            <div class="rol1-btn">
                                <button type="submit" class="submit">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                    <span>Save Changes</span>
                                </button>
                            </div>
                        </div>

                        <?php if ($profileFlash):
                            
                        ?>
                        <div class="alert <?= $profileFlash['success'] ? 'success' : 'error' ?>">
                            <?= e($profileFlash['text']) ?>
                        </div>
                        <?php endif;
                        ?>
                        <div class="profile-info-section">
                            <div class="profile-section">
                                <p class="sec-heading">Personal Information</p>
                                <p class="sec-desc">Update your personal details</p>

                                <div class="profile-info-card">
                                    <div class="pr-img-box">
                                        <img src="../images/studentDp.jpg" alt="<?= e($student['full_name']) ?>" class="pr-img">
                                        <i class="fa-solid fa-pencil" id="pencil"></i>
                                    </div>
                                   <div class="pr-input-box">
                                        <div class="pr-row1">
                                            <div class="full-name-box">
                                                <label for="fullName">Full Name</label>
                                                <input type="text" name="fullName" id="fullName" class="full-name"
                                                placeholder="Enter full name"
                                                value="<?= e($student['full_name']) ?>" required>
                                            </div>
                                            <div class="full-name-box">
                                                <label for="email">Email</label>
                                                <input type="email" name="email" id="email" class="full-name"
                                                placeholder="Enter your email"
                                                value="<?= e($student['email']) ?>" required>
                                            </div>
                                        </div>
                                        <div class="pr-row2">
                                            <div class="full-name-box">
                                                <label for="telephone">Phone Number</label>
                                                <input type="tel" name="telephone" id="telephone" class="full-name"
                                                placeholder="+254 704 100 100"
                                                value="<?= e($student['phone']) ?>">
                                            </div>
                                            <div class="full-name-box">
                                                <label for="studentId">Student ID</label>
                                                <input type="text" name="studentId" id="studentId" class="full-name"
                                                value="<?= e($student['student_id']) ?>" readonly>
                                            </div>
                                       </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="vehivle-information-section profile-info-section">
                            <div class="profile-section">
                                <p class="sec-heading">Vehicle Information</p>
                                <p class="sec-desc">Update your vehicle details</p>
                                <div class="account-setting-card profile-info-card">
                                    <div class="pr-input-box">
                                        <div class="pr-row1">
                                            <div class="full-name-box">
                                                <label for="regNumber">Vehicle Number</label>
                                                <input type="text" name="regNumber" id="regNumber" class="full-name"
                                                placeholder="e.g, KDA 691E"
                                                value="<?= e($student['reg_number']) ?>">
                                            </div>
                                            <div class="full-name-box">
                                                <label for="color">Vehicle color</label>
                                                <input type="text" name="color" id="color" class="full-name"
                                                placeholder="e.g, silver"
                                                value="<?= e($student['color']) ?>">
                                            </div>
                                            <div class="full-name-box">
                                                <label for="vehicleType">Vehicle Type</label>
                                                <select name="vehicleType" id="vehicleType" class="full-name">
                                                    <?php foreach ($vehicleTypes as $value => $label): ?>
                                                    <option value="<?= $value ?>"
                                                        <?= $student['car_type'] === $value ? 'selected' : '' ?>>
                                                        <?= $label ?>
                                                    </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="security-section profile-info-section">
                        <div class="profile-section">
                            <p class="sec-heading">Security</p>
                            <p class="sec-desc">Manage your password and account security</p>

                            <?php if ($passwordFlash): ?>
                            <div class="alert <?= $passwordFlash['success'] ? 'success' : 'error' ?>">
                                <?= e($passwordFlash['text']) ?>
                            </div>
                            <?php endif; ?>
                            <form action="passwordChange.php" method="POST"
                                  class="password-box <?= $passwordFlash ? '' : 'hidden' ?>"
                                  id="passwordBox">

                                <div class="full-name-box">
                                    <label for="currentPassword">Current Password</label>
                                    <input type="password" name="currentPassword" id="currentPassword"
                                    class="full-name" required>
                                </div>
                                <div class="full-name-box">
                                    <label for="newPassword">New Password (at least 8 characters)</label>
                                    <input type="password" name="newPassword" id="newPassword"
                                    class="full-name" minlength="8" required>
                                </div>
                                <div class="full-name-box">
                                    <label for="confirmPassword">Confirm New Password</label>
                                    <input type="password" name="confirmPassword" id="confirmPassword"
                                    class="full-name" minlength="8" required>
                                </div>
                                <button type="submit" class="submit">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                    <span>Update Password</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

<script>
    // Show / hide the change password form
    const toggleButton = document.getElementById("togglePassword");
    const passwordBox = document.getElementById("passwordBox");

    toggleButton.addEventListener("click", function () {
        passwordBox.classList.toggle("hidden");
    });
</script>