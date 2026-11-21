<?php
session_start();

require __DIR__ . '/visitorDb.php';

if (!isset($_SESSION['visitor_id'])) {
    header('Location: visitorLogin.html');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM visitors WHERE visitor_id = ?");
$stmt->execute([$_SESSION['visitor_id']]);
$visitor = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM access_codes WHERE visitor_id = ? ORDER BY created_at DESC LIMIT 10");
$stmt->execute([$_SESSION['visitor_id']]);
$codes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Access Code</title>
        <script src="https://kit.fontawesome.com/6c491f9239.js"></script>
        <link rel="stylesheet" href="access.css"> 
    </head>
    <body>
        <div class="sidebar">
            <div class="logo"></div>
            <ul class="menu">
                <li>
                    <a href="visitorDashboard.html">
                        <i class="fa-regular fa-house"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="active">
                    <a href="access.html">
                        <i class="fa-solid fa-key"></i>
                        <span>Access Code</span>
                    </a>
                </li>
                <li>
                    <a href="visitorFeedback.html">
                        <i class="fa-regular fa-comment-dots"></i>
                        <span>Give Feedback</span>
                    </a>
                </li>
                <li>
                    <a href="visitorNotification.html">
                        <i class="fa-regular fa-bell"></i>
                        <span>Notifications</span>
                    </a>
                </li>
                <li>
                    <a href="visitorContact.html">
                        <i class="fa-solid fa-phone"></i>
                        <span>Contact Us</span>
                    </a>
                </li>
                <li>
                    <a href="visitorSettings.php">
                       <i class="fa-solid fa-gear"></i>
                        <span>Settings</span>
                    </a>
                </li>
                <li>
                    <a href="visitorNavigation.html">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>Navigation Map</span>
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
                    <img src="../images/visitor.jpeg" alt="Ntokozo Mbambo">
                </div>
            </div>
            <div class="visitor-form">
                <div class="form-header">
                    <span class="visitors"><i class="fa-solid fa-users"></i></span>
                    <div>
                        <h2>Generate visitor access code</h2>
                        <p>Create a temporary 6-digit access code for visitors to enter the school</p>
                    </div>
                </div>
                <section class="generator-container">
                    <div class="visitor-form-section">
                        <h2>Visitor Details</h2>
                        <form id="visitorForm" method="post">
                            <div class="form-detail">
                                <label for="fullName">Full Name</label>
                                <input type="text" id="fullName" readonly
                                value="<?= htmlspecialchars($visitor['full_name']) ?>">
                            </div>
                            <div class="form-detail">
                                <label for="idNumber">ID Number</label>
                                <input type="text" id="idNumber" readonly
                                value="<?= htmlspecialchars($visitor['visitor_id']) ?>">
                            </div>
                            <div class="form-detail">
                                <label for="purposeVisit">Purpose of visit</label>
                            </div>
                            <div class="form-detail">
                                <select name="purpose" id="purposeVisit" required>
                                    <option value="admission">Admission / Enquiry</option>
                                    <option value="meeting">Meeting</option>
                                    <option value="academicVisit">Academic Visit</option>
                                    <option value="interview">Interview</option>
                                    <option value="delivery">Delivery</option>
                                    <option value="event">Event / Conference</option>
                                    <option value="business">Business / Official Visit</option>
                                    <option value="staffVisit">Staff Visit</option>
                                    <option value="studentVisit">Student Visit</option>
                                    <option value="maintanance">Contractor / Maintanance</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="form-detail">
                                <button type="submit" class="generate-btn">Generate code</button>
                            </div>
                        </form>
                    </div>
                    <div class="access-code-section">
                        <div class="success-icon">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <h2 id="codeHeading">Visitor Access Code</h2>
                        <p class="code-description" id="codeDescription">Generate the code after filling in the user details</p>
                        <div class="access-code-display" id="accessCode">
                            ------
                        </div>
                        <div class="code-expiry">
                            <span>
                                <i class="fa-regular fa-calendar"></i>
                            </span>
                            <span id="expiryDisplay"> 10-12-2026 </span>
                        </div>
                        <div class="code-info">
                            <span>
                                <i class="fa-solid fa-circle-exclamation"></i>
                            </span>
                           <p>The generated code expires after 24 hours</p>
                        </div> 
                    </div>
                </section>
            </div>
            <div class="recent-codes-section">
                <div class="table-heading">
                    <div>
                        <h2>Recent Visitor access codes</h2>
                        <p>Review visitor codes that you have generated before</p>
                    </div>
                </div>
                <div class="table-responsive">
                    <table>
                        <tbody id="accessCodeTable">
                            <?php foreach ($codes as $c):
                            $active = strtotime($c['expires_at']) > time(); ?>

                            <tr>
                                <td><?= date('d-m-Y H:i', strtotime($c['created_at'])) ?></td>
                                <td><?= htmlspecialchars($visitor['full_name']) ?></td>
                                <td><?= htmlspecialchars(ucfirst($c['purpose'])) ?></td>
                                <td><?= $c['code'] ?></td>
                                <td><?= date('d-m-Y H:i', strtotime($c['expires_at'])) ?></td>
                                <td class="<?= $active ? 'actives' : 'expire' ?>"><?= $active ? 'Active' : 'Expired' ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                       <!-- <thead>
                            <tr>
                                <th>Date Generated</th>
                                <th>Visitor Name</th>
                                <th>Purpose</th>
                                <th>Access Code</th>
                                <th>Valid Until</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="accessCodeTable">
                            <tr>
                                <td> 10-12-2026</td>
                                <td>Ntokozo Mbambo</td>
                                <td>Meeting</td>
                                <td>482961</td>
                                <td>10-12-2026</td>
                                <td class="actives">Active</td>
                            </tr>
                            <tr>
                                <td> 12-06-2026</td>
                                <td>Ntokozo Mbambo</td>
                                <td>Event</td>
                                <td>345786</td>
                                <td>12-06-2026</td>
                                <td class="expire">Expired</td>
                            </tr>
                            <tr>
                                <td> 01-03-2026</td>
                                <td>Ntokozo Mbambo</td>
                                <td>Interview</td>
                                <td>786543</td>
                                <td>01-03-2026</td>
                                <td class="expire">Expired</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6">Karibu Tena Strathmore University</td>
                            </tr>
                        </tfoot> -->
                    </table>
                </div>
            </div>

        </div>
            <script src="access.js"></script>
    </body>
</html>