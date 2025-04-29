<?php
// Include database connection file
require_once 'db_connect.php';

// Start the session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Fetch user details from database
$query = "SELECT username, mailid, phone, gender, age, aadhar FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch booking statistics
$totalBookingsQuery = "SELECT COUNT(*) AS total FROM reservations WHERE user_id = ?";
$canceledBookingsQuery = "SELECT COUNT(*) AS canceled FROM reservations WHERE user_id = ? AND status = 'canceled'";
$confirmedBookingsQuery = "SELECT COUNT(*) AS confirmed FROM reservations WHERE user_id = ? AND status = 'confirmed'";
$pendingBookingsQuery = "SELECT COUNT(*) AS pending FROM reservations WHERE user_id = ? AND status = 'pending'";

$stmt1 = $conn->prepare($totalBookingsQuery);
$stmt1->bind_param("i", $user_id);
$stmt1->execute();
$totalBookingsResult = $stmt1->get_result();
$totalBookings = $totalBookingsResult->fetch_assoc()['total'];

$stmt2 = $conn->prepare($canceledBookingsQuery);
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$canceledBookingsResult = $stmt2->get_result();
$canceledBookings = $canceledBookingsResult->fetch_assoc()['canceled'];

// Check if the query executed successfully
if ($canceledBookingsResult === false) {
    $canceledBookings = 0; // Default to 0 if query fails
}

$stmt3 = $conn->prepare($confirmedBookingsQuery);
$stmt3->bind_param("i", $user_id);
$stmt3->execute();
$confirmedBookingsResult = $stmt3->get_result();
$confirmedBookings = $confirmedBookingsResult->fetch_assoc()['confirmed'];

$stmt4 = $conn->prepare($pendingBookingsQuery);
$stmt4->bind_param("i", $user_id);
$stmt4->execute();
$pendingBookingsResult = $stmt4->get_result();
$pendingBookings = $pendingBookingsResult->fetch_assoc()['pending'];

$stmt->close();
$stmt1->close();
$stmt2->close();
$stmt3->close();
$stmt4->close();

$conn->close();

// Get the current hour
$current_hour = date('H');

// Determine the time of day message
if ($current_hour >= 5 && $current_hour < 12) {
    $greeting = "Good morning";
} elseif ($current_hour >= 12 && $current_hour < 17) {
    $greeting = "Good afternoon";
} elseif ($current_hour >= 17 && $current_hour < 21) {
    $greeting = "Good evening";
} else {
    $greeting = "Good night";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/user_profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <title>User Profile</title>
</head>
<body>
    <?php include("includes/header.php"); ?>
    <div class="dashboard-wrapper">
        <?php include("includes/user_sidebar.php"); ?>

        <div class="dashboard-content">
            <div class="welcome-section">
                <h2><?php echo $greeting . ", " . htmlspecialchars($user['username']); ?>! 👋</h2>
                <p class="subtext">Welcome to your dashboard. Here’s a quick overview of your activity.</p>
            </div>

            <div class="user-info">
                <div class="info-card"><i class="fa-solid fa-envelope"></i> <?php echo htmlspecialchars($user['mailid']); ?></div>
                <div class="info-card"><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($user['phone']); ?></div>
                <div class="info-card"><i class="fa-solid fa-venus-mars"></i> <?php echo htmlspecialchars($user['gender']); ?></div>
                <div class="info-card"><i class="fa-solid fa-user"></i> Age: <?php echo htmlspecialchars($user['age']); ?></div>
                <div class="info-card"><i class="fa-solid fa-id-card"></i> Aadhar: <?php echo htmlspecialchars($user['aadhar']); ?></div>
            </div>

            <div class="dashboard-cards">
                <div class="card total">
                    <i class="fa-solid fa-calendar-check"></i>
                    <h3>Total Bookings</h3>
                    <p><?php echo $totalBookings; ?></p>
                </div>
                <div class="card canceled">
                    <i class="fa-solid fa-ban"></i>
                    <h3>Canceled Bookings</h3>
                    <p><?php echo $canceledBookings; ?></p>
                </div>
                <div class="card confirmed">
                    <i class="fa-solid fa-check-circle"></i>
                    <h3>Confirmed Bookings</h3>
                    <p><?php echo $confirmedBookings; ?></p>
                </div>
                <div class="card pending">
                    <i class="fa-solid fa-hourglass-half"></i>
                    <h3>Pending Bookings</h3>
                    <p><?php echo $pendingBookings; ?></p>
                </div>
            </div>
        </div>
    </div>
    <?php include("includes/footer.php"); ?>
</body>
</html>
