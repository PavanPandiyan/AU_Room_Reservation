<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservation_id = $_POST['reservation_id'];
    $payment_status = $_POST['payment_status'];
    $transaction_id = $_POST['transaction_id'] ?? null; // Optional transaction ID

    // Validate payment status
    if (!in_array($payment_status, ['paid', 'offline', 'unpaid', 'pending'])) {
        echo "error: Invalid payment status.";
        exit();
    }

    // Update payment status and transaction ID in the database
    $stmt = $conn->prepare("UPDATE payments SET payment_status = ?, transaction_id = ? WHERE reservation_id = ?");
    $stmt->bind_param("ssi", $payment_status, $transaction_id, $reservation_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "success: Payment status updated.";
        } else {
            echo "error: No rows updated. Check if the reservation ID exists.";
        }
    } else {
        echo "error: Failed to update payment status.";
    }
    $stmt->close();
}
?>
