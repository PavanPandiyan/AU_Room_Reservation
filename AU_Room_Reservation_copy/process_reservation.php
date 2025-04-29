<?php
// Start session securely
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => true, // Only send cookies over HTTPS
    'httponly' => true, // Prevent JavaScript access
    'samesite' => 'Strict' // CSRF protection
]);
session_start();

// Database connection
$servername = "localhost";
$db_username = "root";
$db_password = "";
$db_name = "au_guesthouse";

$conn = new mysqli($servername, $db_username, $db_password, $db_name);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Ensure the reservations table exists
$create_tables_sql = "
CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    applicant_name VARCHAR(255) NOT NULL,
    applicant_address TEXT NOT NULL,
    guest_name VARCHAR(255) NOT NULL,
    guest_address TEXT NOT NULL,
    staff_id VARCHAR(50) NOT NULL,
    purpose TEXT NOT NULL,
    checkin_date DATE NOT NULL,
    checkout_date DATE NOT NULL,
    room_type VARCHAR(50) NOT NULL,
    num_rooms INT NOT NULL,
    total_cost DECIMAL(10, 2) NOT NULL,
    cost_type VARCHAR(50) NOT NULL,
    room_number TEXT NOT NULL,
    user_id INT NOT NULL,
    status VARCHAR(50) NOT NULL
)";
$conn->query($create_tables_sql);

// CSRF Token Validation
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Invalid CSRF token");
}

// Sanitize and validate input
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

$applicant_name = sanitize_input($_POST['applicant_name']);
$applicant_address = sanitize_input($_POST['applicant_address']);
$guest_name = sanitize_input($_POST['guest_name']);
$guest_address = sanitize_input($_POST['guest_address']);
$staff_id = sanitize_input($_POST['staff_id']);
$purpose = sanitize_input($_POST['purpose']);
$checkin_date = sanitize_input($_POST['checkin_date']);
$checkout_date = sanitize_input($_POST['checkout_date']);
$room_type = sanitize_input($_POST['room_type']);
$num_rooms = intval($_POST['num_rooms']);
$total_cost = floatval($_POST['total_cost']);
$cost_type = sanitize_input($_POST['cost_type'] ?? 'official'); // Ensure cost_type is set
$selected_rooms = isset($_POST['room_ids']) ? explode(',', $_POST['room_ids']) : [];
$user_id = $_SESSION['user_id']; // Get user ID from session

// Fetch room numbers
$room_numbers = [];
if (!empty($selected_rooms)) {
    $placeholders = implode(',', array_fill(0, count($selected_rooms), '?'));
    $sql = "SELECT room_number FROM room_list WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('i', count($selected_rooms)), ...$selected_rooms);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $room_numbers[] = $row['room_number'];
    }
    $room_numbers_str = implode(',', $room_numbers);
} else {
    $room_numbers_str = '';
}

// Insert reservation details
$reservation_sql = "INSERT INTO reservations (applicant_name, applicant_address, guest_name, guest_address, staff_id, purpose, cost_type, checkin_date, checkout_date, room_type, num_rooms, room_number, total_cost, user_id, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($reservation_sql);
$status = 'pending';
$stmt->bind_param("sssssssssidssis", $applicant_name, $applicant_address, $guest_name, $guest_address, $staff_id, $purpose, $cost_type, $checkin_date, $checkout_date, $room_type, $num_rooms, $room_numbers_str, $total_cost, $user_id, $status);

if ($stmt->execute()) {
    // Get the last inserted reservation ID
    $reservation_id = $stmt->insert_id;

    // Insert payment details
    $payment_sql = "INSERT INTO payments (reservation_id, payment_method, amount, payment_status, transaction_id) VALUES (?, ?, ?, ?, ?)";
    $payment_status = ($_POST['payment_method'] == 'online') ? 'Pending' : 'Completed';
    $transaction_id = ($_POST['payment_method'] == 'online') ? null : 'N/A';
    $stmt = $conn->prepare($payment_sql);
    $stmt->bind_param("issss", $reservation_id, $_POST['payment_method'], $total_cost, $payment_status, $transaction_id);
    $stmt->execute();

    $_SESSION['message'] = "Reservation successful!";
    $_SESSION['message_type'] = "success";
    header("Location: user_reservation.php");
    $stmt->close();
    $conn->close();
    exit();
} else {
    $_SESSION['message'] = "Error: " . $stmt->error;
    $_SESSION['message_type'] = "danger";
    $stmt->close();
    $conn->close();
    header("Location: reservation_form.php");
    exit();
}
?>
