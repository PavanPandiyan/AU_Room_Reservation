<?php
include('db_connect.php');
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

// Handle form submissions safely
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_payment'])) {
        $user_id = intval($_POST['user_id']);
        $amount = floatval($_POST['amount']);
        $date = $_POST['date'];
        $status = $_POST['status'];
        
        $query = "INSERT INTO payments (user_id, amount, date, status) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "idss", $user_id, $amount, $date, $status);
        mysqli_stmt_execute($stmt);
    } elseif (isset($_POST['edit_payment'])) {
        $payment_id = intval($_POST['payment_id']);
        $user_id = intval($_POST['user_id']);
        $amount = floatval($_POST['amount']);
        $date = $_POST['date'];
        $status = $_POST['status'];
        
        $query = "UPDATE payments SET user_id=?, amount=?, date=?, status=? WHERE payment_id=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "idssi", $user_id, $amount, $date, $status, $payment_id);
        mysqli_stmt_execute($stmt);
    } elseif (isset($_POST['delete_payment'])) {
        $payment_id = intval($_POST['payment_id']);
        
        $query = "DELETE FROM payments WHERE payment_id=?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $payment_id);
        mysqli_stmt_execute($stmt);
    }
}

// Fetch payment data
$query = "SELECT * FROM payments";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Payments</title>
    <link rel="stylesheet" href="css/admin_payments.css">
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="flex1">
<?php include 'includes/admin_sidebar.php'; ?>
    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>User ID</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['user_id'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($row['amount']) ?></td>
                        <td><?= htmlspecialchars($row['date'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <button onclick='editPayment(<?= json_encode($row) ?>)'>Edit</button>
                            <form method='POST' action='' style='display:inline-block;'>
                                <input type='hidden' name='payment_id' value='<?= htmlspecialchars($row['id']) ?>'>
                                <button type='submit' name='delete_payment'>Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <form method="POST" action="" id="editPaymentForm" style="display:none;">
            <h2>Edit Payment</h2>
            <input type="hidden" id="edit_payment_id" name="payment_id">
            <label for="edit_user_id">User ID:</label>
            <input type="text" id="edit_user_id" name="user_id" required>
            <label for="edit_amount">Amount:</label>
            <input type="text" id="edit_amount" name="amount" required>
            <label for="edit_date">Date:</label>
            <input type="date" id="edit_date" name="date" required>
            <label for="edit_status">Status:</label>
            <select id="edit_status" name="status" required>
                <option value="Pending">Pending</option>
                <option value="Completed">Completed</option>
                <option value="Failed">Failed</option>
            </select>
            <button type="submit" name="edit_payment">Update Payment</button>
        </form>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
<script>
    function editPayment(payment) {
        document.getElementById('editPaymentForm').style.display = 'block';
        document.getElementById('edit_payment_id').value = payment.id;
        document.getElementById('edit_user_id').value = payment.user_id ?? '';
        document.getElementById('edit_amount').value = payment.amount;
        document.getElementById('edit_date').value = payment.date ?? '';
        document.getElementById('edit_status').value = payment.status;
    }
</script>
</body>
</html>
