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

// Fetch user booking history (Fixed the SQL query syntax)
$query = "SELECT r.room_type, res.created_at, res.checkin_date, res.checkout_date, res.status
          FROM reservations res
          JOIN room_list r ON res.room_number = r.room_number
          WHERE res.user_id = ?
          ORDER BY res.created_at DESC"; 

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Booking History</title>
    <link rel="stylesheet" href="css/user_history.css">
</head>
<body>
    <?php include("includes/header.php"); ?>
    <div class="flex1">
    <?php include("includes/user_sidebar.php"); ?>

    <div class="history-container">
        <table>
            <thead>
                <tr>
                    <th>Room Type</th>
                    <th>Booking Date</th>
                    <th>Check-In Date</th>
                    <th>Check-Out Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['room_type']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td><?php echo htmlspecialchars($row['checkin_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['checkout_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    </div>
    <?php include("includes/footer.php"); ?>
</body>
</html>