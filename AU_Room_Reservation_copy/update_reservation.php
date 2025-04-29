<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cost_type = $_POST['cost_type']; // Fetch cost_type from the form
    $applicant_name = $_POST['applicant_name'];
    $guest_name = $_POST['guest_name'];
    $room_number = $_POST['room_number'];
    $checkin_date = $_POST['checkin_date'];
    $checkout_date = $_POST['checkout_date'];
    $total_cost = $_POST['total_cost'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO reservations (applicant_name, guest_name, room_number, checkin_date, checkout_date, cost_type, total_cost, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssds", $applicant_name, $guest_name, $room_number, $checkin_date, $checkout_date, $cost_type, $total_cost, $status);

    if ($stmt->execute()) {
        echo "success: Reservation created successfully.";
    } else {
        echo "error: Failed to create reservation.";
    }
    $stmt->close();
}
?>