<?php
session_start();

// Database connection
$servername = "localhost";
$db_username = "root";
$db_password = "";
$db_name = "au_guesthouse";

$conn = new mysqli($servername, $db_username, $db_password, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user payments
$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session
$sql = "SELECT p.id, r.applicant_name, r.guest_name, p.payment_method, p.amount, p.payment_status, p.transaction_id, p.created_at FROM payments p JOIN reservations r ON p.reservation_id = r.id WHERE r.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Payments</title>
    <link rel="stylesheet" href="css/user_payments.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="flex1">
        <?php include 'includes/user_sidebar.php'; ?>

        <div class="container mt-4">
            <div class="table-container">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Payment ID</th>
                            <th>Applicant Name</th>
                            <th>Guest Name</th>
                            <th>Payment Method</th>
                            <th>Amount</th>
                            <th>Payment Status</th>
                            <th>Transaction ID</th>
                            <th>Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['applicant_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['guest_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                                <td><?php echo htmlspecialchars($row['amount']); ?></td>
                                <td><?php echo htmlspecialchars($row['payment_status']); ?></td>
                                <td><?php echo htmlspecialchars($row['transaction_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
