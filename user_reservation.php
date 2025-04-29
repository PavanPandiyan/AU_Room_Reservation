<?php
session_start();
require_once 'db_connect.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filterStatus = isset($_GET['status']) ? $_GET['status'] : '';

// Fetch reservations for the logged-in user
$query = "SELECT r.id, r.applicant_name, r.applicant_address, r.guest_name, r.guest_address, r.staff_id, r.purpose, r.room_type, r.num_rooms, r.room_number, r.checkin_date, r.checkout_date, r.cost_type, r.total_cost, r.created_at, r.user_id, r.status, r.payment_status 
          FROM reservations r 
          WHERE r.user_id = ? AND (r.applicant_name LIKE ? OR r.guest_name LIKE ? OR r.room_number LIKE ?)";

if ($filterStatus) {
    $query .= " AND r.status = ?";
}

$query .= " ORDER BY r.id DESC";

$stmt = $conn->prepare($query);
$searchParam = "%$search%";

if ($filterStatus) {
    $stmt->bind_param("issss", $user_id, $searchParam, $searchParam, $searchParam, $filterStatus);
} else {
    $stmt->bind_param("isss", $user_id, $searchParam, $searchParam, $searchParam);
}

$stmt->execute();
$result = $stmt->get_result();

// Handle reservation cancellation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel_reservation'])) {
    $reservation_id = $_POST['reservation_id'];

    // Verify the reservation belongs to the user
    $check_query = "SELECT * FROM reservations WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ii", $reservation_id, $user_id);
    $stmt->execute();
    $check_result = $stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Update reservation status to "Cancelled"
        $update_query = "UPDATE reservations SET status = 'cancelled' WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("i", $reservation_id);
        $stmt->execute();

        // Fetch room details
        $room_query = "SELECT room_number, num_rooms FROM reservations WHERE id = ?";
        $stmt = $conn->prepare($room_query);
        $stmt->bind_param("i", $reservation_id);
        $stmt->execute();
        $room_result = $stmt->get_result();
        $room = $room_result->fetch_assoc();
        $room_number = $room['room_number'];
        $num_rooms = $room['num_rooms'];

        // Update room availability based on number of rooms booked
        $update_room_query = "UPDATE room_list SET available_rooms = available_rooms + ? WHERE room_number = ?";
        $stmt = $conn->prepare($update_room_query);
        $stmt->bind_param("is", $num_rooms, $room_number);
        $stmt->execute();

        echo "<script>alert('Reservation cancelled successfully!'); window.location.href='user_reservation.php';</script>";
    } else {
        echo "<script>alert('Unauthorized action!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations</title>
    <link rel="stylesheet" href="css/user_reservation.css">
    <!-- Add this in <head> or before </body> -->

</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="flex1">
    <?php include 'includes/user_sidebar.php'; ?>
    <div class="content-container">

        <div class="filter-search">
            <input type="text" id="search" placeholder="Search by applicant name, guest name, or room number..." value="<?php echo htmlspecialchars($search); ?>">
            <select id="status-filter">
                <option value="">All Statuses</option>
                <option value="confirmed" <?php if ($filterStatus == 'confirmed') echo 'selected'; ?>>Confirmed</option>
                <option value="pending" <?php if ($filterStatus == 'pending') echo 'selected'; ?>>Pending</option>
                <option value="cancelled" <?php if ($filterStatus == 'cancelled') echo 'selected'; ?>>Cancelled</option>
            </select>
            <button onclick="applyFilter()">Filter</button>
        </div>

        <div class="table-container">
            <table class="reservations-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Applicant Name</th>
                        <th>Applicant Address</th>
                        <th>Guest Name</th>
                        <th>Guest Address</th>
                        <th>Staff ID</th>
                        <th>Purpose</th>
                        <th>Room Type</th>
                        <th>Number of Rooms</th>
                        <th>Room Number</th>
                        <th>Check-In</th>
                        <th>Check-Out</th>
                        <th>Cost Type</th>
                        <th>Total Cost</th>
                        <th>Status</th>
                        <th>Payment Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['applicant_name']; ?></td>
                            <td><?php echo $row['applicant_address']; ?></td>
                            <td><?php echo $row['guest_name']; ?></td>
                            <td><?php echo $row['guest_address']; ?></td>
                            <td><?php echo $row['staff_id']; ?></td>
                            <td><?php echo $row['purpose']; ?></td>
                            <td><?php echo $row['room_type']; ?></td>
                            <td><?php echo $row['num_rooms']; ?></td>
                            <td><?php echo $row['room_number']; ?></td>
                            <td><?php echo $row['checkin_date']; ?></td>
                            <td><?php echo $row['checkout_date']; ?></td>
                            <td><?php echo $row['cost_type']; ?></td>
                            <td><?php echo $row['total_cost']; ?></td>
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
                                <?php if ($row['status'] == 'pending' || $row['status'] == 'confirmed'): ?>
                                    <form class="status-update-form" method="POST">
                                        <input type="hidden" name="reservation_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="status" value="cancelled">
                                        <button type="submit" name="cancel_reservation" class="btn-cancel">Cancel</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted">Not Available</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function applyFilter() {
    const search = document.getElementById("search").value;
    const status = document.getElementById("status-filter").value;
    window.location.href = `user_reservation.php?search=${search}&status=${status}`;
}

$(document).ready(function () {
    // Handle AJAX status update
    $(".status-update-form").submit(function (e) {
        e.preventDefault();
        let form = $(this);
        let reservationId = form.find("input[name='reservation_id']").val();
        let newStatus = form.find("input[name='status']").val();

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
});

function payOnline(reservationId) {
    if (confirm("Are you sure you want to pay online for this reservation?")) {
        $.ajax({
            url: "update_payment_status.php",
            type: "POST",
            data: { reservation_id: reservationId, payment_status: "paid" },
            success: function (response) {
                if (response.startsWith("success")) {
                    alert("Payment successful! Payment status updated.");
                    location.reload();
                } else {
                    alert(response); // Show error message from the server
                }
            },
            error: function () {
                alert("Error processing payment. Please try again.");
            }
        });
    }
}
</script>
</body>
</html>