<?php
// Include database connection
require_once 'db_connect.php';

// Check if 'room_list' table exists
$checkRoomsTable = $conn->query("SHOW TABLES LIKE 'room_list'");
if ($checkRoomsTable->num_rows == 0) {
    die("Error: Table 'room_list' does not exist. Please create the 'room_list' table.");
}

// Handle search and filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filterStatus = isset($_GET['status']) ? $_GET['status'] : '';

$query = "SELECT r.id, u.username, rl.room_type AS room_name, r.checkin_date, r.checkout_date, r.status, r.created_at, r.applicant_name, r.guest_name, r.total_cost, r.num_rooms, r.room_number, r.payment_status 
          FROM reservations r 
          JOIN users u ON r.user_id = u.id 
          JOIN room_list rl ON r.room_number = rl.room_number 
          WHERE (u.username LIKE ? OR rl.room_type LIKE ?)";

if ($filterStatus) {
    $query .= " AND r.status = ?";
}

$query .= " ORDER BY r.id DESC";

$stmt = $conn->prepare($query);
$searchParam = "%$search%";

if ($filterStatus) {
    $stmt->bind_param("sss", $searchParam, $searchParam, $filterStatus);
} else {
    $stmt->bind_param("ss", $searchParam, $searchParam);
}

$stmt->execute();
$result = $stmt->get_result();

// Handle Reservation Cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_reservation'])) {
    $reservation_id = $_POST['reservation_id'];

    // Fetch room_id from reservation
    $stmt = $conn->prepare("SELECT room_number, num_rooms FROM reservations WHERE id = ?");
    $stmt->bind_param("i", $reservation_id);
    $stmt->execute();
    $room_result = $stmt->get_result();
    $room = $room_result->fetch_assoc();

    if ($room) {
        $room_number = $room['room_number'];
        $num_rooms = $room['num_rooms'];

        // Update reservation status to 'Cancelled'
        $stmt = $conn->prepare("UPDATE reservations SET status = 'Cancelled' WHERE id = ?");
        $stmt->bind_param("i", $reservation_id);
        $stmt->execute();

        // Increment available rooms
        $stmt = $conn->prepare("UPDATE room_list SET available_rooms = available_rooms + ? WHERE room_number = ?");
        $stmt->bind_param("is", $num_rooms, $room_number);
        $stmt->execute();
    }

    echo "<script>alert('Reservation canceled successfully!'); window.location.href='admin_reservation.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Room Reservations</title>
    <link rel="stylesheet" href="css/admin_reservation.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="flex1">
    <?php include 'includes/admin_sidebar.php'; ?>
    <div class="content-container">
        <!-- Search and Filter -->
        <div class="filter-search">
            <input type="text" id="search" placeholder="Search by user or room..." value="<?php echo htmlspecialchars($search); ?>">
            <select id="status-filter" onchange="applyFilter()">
                <option value="">All Statuses</option>
                <option value="confirmed" <?php if ($filterStatus == 'confirmed') echo 'selected'; ?>>Confirmed</option>
                <option value="pending" <?php if ($filterStatus == 'pending') echo 'selected'; ?>>Pending</option>
                <option value="cancelled" <?php if ($filterStatus == 'cancelled') echo 'selected'; ?>>Cancelled</option>
            </select>
            <button id="filterBtn" onclick="applyFilter()">Filter</button>
        </div>

        <!-- Scrollable Table -->
        <div class="reservations-table-wrapper">
            <table class="reservations-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Applicant Name</th>
                        <th>Guest Name</th>
                        <th>Room Type</th>
                        <th>Number of Rooms</th>
                        <th>Room Numbers</th>
                        <th>Check-In</th>
                        <th>Check-Out</th>
                        <th>Total Cost</th>
                        <th>Cost Type</th>
                        <th>Status</th>
                        <th>Payment Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="reservation-list">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr data-id="<?php echo $row['id']; ?>">
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['applicant_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['guest_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['room_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['num_rooms']); ?></td>
                            <td><?php echo htmlspecialchars($row['room_number']); ?></td>
                            <td><?php echo $row['checkin_date']; ?></td>
                            <td><?php echo $row['checkout_date']; ?></td>
                            <td><?php echo $row['total_cost']; ?></td>
                            <td><?php echo htmlspecialchars($row['cost_type']); ?></td>
                            <td>
                                <span class="status status-<?php echo strtolower($row['status']); ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="payment-status payment-<?php echo strtolower($row['payment_status']); ?>">
                                    <?php 
                                    if ($row['payment_status'] == 'paid') {
                                        echo "Paid (Online)";
                                    } elseif ($row['payment_status'] == 'offline') {
                                        echo "Paid (Offline)";
                                    } else {
                                        echo ucfirst($row['payment_status']); // e.g., Unpaid
                                    }
                                    ?>
                                </span>
                            </td>
                            <td>
                                <form class="status-update-form" method="POST">
                                    <input type="hidden" name="reservation_id" value="<?php echo $row['id']; ?>">
                                    <select name="status">
                                        <option value="confirmed" <?php echo ($row['status'] == 'confirmed') ? 'selected' : ''; ?>>Confirm</option>
                                        <option value="cancelled" <?php echo ($row['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancel</option>
                                    </select>
                                    <button type="submit" name="cancel_reservation" class="btn-edit">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
function applyFilter() {
    const search = document.getElementById("search").value;
    const status = document.getElementById("status-filter").value;
    window.location.href = `admin_reservation.php?search=${search}&status=${status}`;
}

$(document).ready(function () {
    // Handle AJAX status update
    $(".status-update-form").submit(function (e) {
        e.preventDefault();
        let form = $(this);
        let reservationId = form.find("input[name='reservation_id']").val();
        let newStatus = form.find("select[name='status']").val();

        $.ajax({
            url: "update_status.php",
            type: "POST",
            data: { reservation_id: reservationId, status: newStatus },
            success: function (response) {
                if (response.startsWith("success")) {
                    alert("Status updated successfully!");
                    location.reload();
                } else {
                    alert(response);
                }
            },
            error: function () {
                alert("Error updating status.");
            }
        });
    });

    // Handle AJAX payment status update
    $(".payment-update-form").submit(function (e) {
        e.preventDefault();
        let form = $(this);
        let reservationId = form.find("input[name='reservation_id']").val();
        let newPaymentStatus = form.find("select[name='payment_status']").val();

        $.ajax({
            url: "update_payment_status.php",
            type: "POST",
            data: { reservation_id: reservationId, payment_status: newPaymentStatus },
            success: function (response) {
                if (response.startsWith("success")) {
                    alert("Payment status updated successfully!");
                    location.reload();
                } else {
                    alert(response);
                }
            },
            error: function () {
                alert("Error updating payment status.");
            }
        });
    });
});
</script>

</body>
</html>