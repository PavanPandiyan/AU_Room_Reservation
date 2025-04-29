<?php
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reservation_id']) && isset($_POST['status'])) {
    $reservation_id = $_POST['reservation_id'];
    $new_status = $_POST['status'];

    // Update reservation status
    $update_query = "UPDATE reservations SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("si", $new_status, $reservation_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error updating status.";
    }
} else {
    echo "Invalid request.";
}
?>
