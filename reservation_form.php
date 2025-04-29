<?php 
// Start session only if not already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Generate CSRF token if not already set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Database connection
$servername = "localhost";
$db_username = "root";
$db_password = "";
$db_name = "au_guesthouse";

$conn = new mysqli($servername, $db_username, $db_password, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$total_cost = 0;
$selected_rooms = explode(',', $_POST['room_ids'] ?? []);
$selected_room_numbers = explode(',', $_POST['room_numbers'] ?? []);
$selected_room_type = $_POST['room_type'] ?? '';
$num_rooms_required = count($selected_rooms);
$checkin_date = $_POST['checkin_date'] ?? '';
$checkout_date = $_POST['checkout_date'] ?? '';
$cost_type = $_POST['cost_type'] ?? 'official';

// Fetch distinct room types
$room_types = [];
$type_sql = "SELECT DISTINCT room_type FROM room_list";
$type_result = $conn->query($type_sql);
while ($row = $type_result->fetch_assoc()) {
    $room_types[] = $row['room_type'];
}

// Predefined cost types
$cost_types = ['official', 'others'];

// Fetch selected room details
$room_costs = [];
$room_numbers = [];

if (!empty($selected_rooms)) {
    $placeholders = implode(',', array_fill(0, count($selected_rooms), '?'));
    $sql = "SELECT id, room_number, room_type, cost_official, cost_others FROM room_list WHERE id IN ($placeholders)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('i', count($selected_rooms)), ...$selected_rooms);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $room_costs[$row['id']] = [
            'official' => $row['cost_official'],
            'others' => $row['cost_others']
        ];
        $room_numbers[$row['id']] = $row['room_number'];
        if (empty($selected_room_type)) {
            $selected_room_type = $row['room_type']; // Automatically set selected room type if not already set
        }
    }
}

// Calculate total cost
if (!empty($selected_rooms) && !empty($checkin_date) && !empty($checkout_date)) {
    $num_days = max(1, ceil((strtotime($checkout_date) - strtotime($checkin_date)) / (60 * 60 * 24)));
    foreach ($selected_rooms as $room_id) {
        if (isset($room_costs[$room_id][$cost_type])) {
            $total_cost += $num_days * $room_costs[$room_id][$cost_type];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Form</title>
    <link rel="stylesheet" href="css/reservation_form.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-4">
        <h2 class="text-center">Reservation Form</h2>
        <form method="POST" action="process_reservation.php">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="room_ids" value="<?php echo htmlspecialchars(implode(',', $selected_rooms)); ?>">
            <input type="hidden" name="room_numbers" value="<?php echo htmlspecialchars(implode(',', $selected_room_numbers)); ?>">
            <input type="hidden" name="cost_type" value="<?php echo htmlspecialchars($cost_type); ?>">
            <div class="row">
                <!-- Left Column -->
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr><th>Applicant Name</th><td><input type="text" name="applicant_name" class="form-control" required></td></tr>
                        <tr><th>Applicant Address</th><td><input type="text" name="applicant_address" class="form-control" required></td></tr>
                        <tr><th>Guest Name</th><td><input type="text" name="guest_name" class="form-control" required></td></tr>
                        <tr><th>Guest Address</th><td><input type="text" name="guest_address" class="form-control" required></td></tr>
                        <tr><th>Staff ID / Student No.</th><td><input type="text" name="staff_id" class="form-control"></td></tr>
                        <tr>
                            <th>Purpose of Visit</th>
                            <td>
                                <select name="purpose" class="form-control" required>
                                    <option value="Official">Official</option>
                                    <option value="Personal">Personal</option>
                                    <option value="PCP">PCP</option>
                                    <option value="Exam">Exam</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>Cost Type</th>
                            <td>
                                <input type="text" name="cost_type_display" class="form-control" value="<?php echo htmlspecialchars($cost_type); ?>" readonly>
                            </td>
                        </tr>
            
                    </table>
                </div>

                <!-- Right Column -->
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>Check-in Date</th>
                            <td><input type="date" name="checkin_date" class="form-control" value="<?php echo htmlspecialchars($checkin_date); ?>" required></td>
                        </tr>
                        <tr>
                            <th>Check-out Date</th>
                            <td><input type="date" name="checkout_date" class="form-control" value="<?php echo htmlspecialchars($checkout_date); ?>" required></td>
                        </tr>
                        <tr>
                            <th>Room Type</th>
                            <td><input type="text" name="room_type" class="form-control" value="<?php echo htmlspecialchars($selected_room_type); ?>" readonly></td>
                        </tr>
                        <tr>
                            <th>Number of Rooms</th>
                            <td><input type="number" name="num_rooms" class="form-control" value="<?php echo $num_rooms_required; ?>" readonly></td>
                        </tr>
                        <tr>
                            <th>Room Numbers</th>
                            <td>
                                <?php if (!empty($selected_room_numbers)): ?>
                                    <ul>
                                        <?php foreach ($selected_room_numbers as $room_number): ?>
                                            <li><?php echo htmlspecialchars($room_number); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <span class="text-danger">No room selected</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Total Cost</th>
                            <td><input type="text" id="total_cost" name="total_cost" class="form-control" value="<?php echo htmlspecialchars($total_cost); ?>" readonly></td>
                        </tr>
                        <tr>
                            <th>Payment Method</th>
                            <td>
                                <select name="payment_method" class="form-control" required id="payment_method">
                                    <option value="offline">Offline</option>
                                    <option value="online">Online</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary" id="confirm_booking">Confirm Booking</button>
            </div>
        </form>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Online Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Total Cost: <span id="modal_total_cost"><?php echo htmlspecialchars($total_cost); ?></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="proceed_payment">Proceed to Pay</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        function updateTotalCost() {
            let checkinDate = document.querySelector('input[name="checkin_date"]').value;
            let checkoutDate = document.querySelector('input[name="checkout_date"]').value;
            let costType = document.querySelector('input[name="cost_type"]').value;
            let totalCost = 0;

            if (checkinDate && checkoutDate) {
                let numDays = Math.max(1, Math.ceil((new Date(checkoutDate) - new Date(checkinDate)) / (1000 * 60 * 60 * 24)));
                
                document.querySelectorAll('input[name="selected_rooms[]"]:checked').forEach(checkbox => {
                    let costPerDay = parseFloat(checkbox.dataset[costType]);
                    if (!isNaN(costPerDay)) totalCost += numDays * costPerDay;
                });
            }
            document.getElementById('total_cost').value = totalCost.toFixed(2);
            document.getElementById('modal_total_cost').innerText = totalCost.toFixed(2);
        }

        const checkinDateInput = document.querySelector('input[name="checkin_date"]');
        const checkoutDateInput = document.querySelector('input[name="checkout_date"]');
        const confirmBookingButton = document.getElementById('confirm_booking');
        const proceedPaymentButton = document.getElementById('proceed_payment');

        if (checkinDateInput) {
            checkinDateInput.addEventListener("change", updateTotalCost);
        }
        if (checkoutDateInput) {
            checkoutDateInput.addEventListener("change", updateTotalCost);
        }
        if (confirmBookingButton) {
            confirmBookingButton.addEventListener("click", function(event) {
                let paymentMethod = document.getElementById('payment_method').value;
                if (paymentMethod === 'online') {
                    event.preventDefault();
                    $('#paymentModal').modal('show');
                } else {
                    // Handle offline booking
                    event.preventDefault();
                    $.ajax({
                        type: 'POST',
                        url: 'process_reservation.php',
                        data: $('form').serialize(),
                        success: function(response) {
                            alert('Booking successful!');
                            window.location.href = 'user_reservation.php';
                        },
                        error: function() {
                            alert('An error occurred while processing your booking.');
                        }
                    });
                }
            });
        }
        if (proceedPaymentButton) {
            proceedPaymentButton.addEventListener("click", function () {
                let totalCost = parseFloat(document.getElementById('total_cost').value) * 100; // Convert to paise
                let options = {
                    "key": "rzp_test_RI5pXpNNWlIb29", // Replace with your Razorpay key
                    "amount": totalCost,
                    "currency": "INR",
                    "name": "AU Room Reservation",
                    "description": "Room Booking Payment",
                    "handler": function (response) {
                        // Handle successful payment
                        $.ajax({
                            type: 'POST',
                            url: 'process_reservation.php',
                            data: $('form').serialize() + '&razorpay_payment_id=' + response.razorpay_payment_id,
                            success: function (response) {
                                alert('Payment successful! Booking confirmed.');
                                window.location.href = 'user_reservation.php';
                            },
                            error: function () {
                                alert('An error occurred while processing your booking.');
                            }
                        });
                    },
                    "prefill": {
                        "name": document.querySelector('input[name="applicant_name"]').value,
                        "email": "", // Optionally add email
                        "contact": "" // Optionally add contact number
                    },
                    "theme": {
                        "color": "#3399cc"
                    }
                };
                let rzp = new Razorpay(options);
                rzp.open();
            });
        }
    });
    </script>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
