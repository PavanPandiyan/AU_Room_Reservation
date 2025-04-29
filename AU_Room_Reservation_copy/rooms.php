<?php
session_start();
$servername = "localhost";
$db_username = "root";
$db_password = "";
$db_name = "au_guesthouse";

// Connect to database
$conn = new mysqli($servername, $db_username, $db_password, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch room types dynamically
$room_types_query = "SELECT DISTINCT room_type FROM room_list";
$room_types_result = $conn->query($room_types_query);

// Initialize variables
$checkin_date = '';
$checkout_date = '';
$room_type = '';
$cost_type = 'official'; // Initialize cost_type

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['room_type']) && isset($_GET['checkin_checkout_date'])) {
    $room_type = $_GET['room_type'];
    $date_range = explode(" to ", $_GET['checkin_checkout_date']);
    $checkin_date = $date_range[0] ?? null;
    $checkout_date = $date_range[1] ?? null;
    $cost_type = $_GET['cost_type'] ?? 'official';

    if (!$checkout_date) {
        echo "<script>alert('Invalid date format. Please select both check-in and check-out dates.'); window.location.href='rooms.php';</script>";
        exit;
    }

    $query = "SELECT r.id, r.room_number, r.room_type, r.cost_official, r.cost_others, 
                     COALESCE(r.image, 'default-room.jpg') AS image,
                     CASE WHEN EXISTS (
                         SELECT 1 FROM reservations res 
                         WHERE FIND_IN_SET(r.room_number, res.room_number) > 0
                         AND res.status != 'cancelled'
                         AND (
                             (res.checkin_date < ? AND res.checkout_date > ?) OR
                             (res.checkin_date >= ? AND res.checkout_date <= ?)
                         )
                     ) THEN 1 ELSE 0 END AS is_booked
              FROM room_list r
              WHERE r.room_type = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $checkin_date, $checkout_date, $checkin_date, $checkout_date, $room_type);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Availability</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/rooms.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-4">
        <!-- Search Form -->
        <form method="GET" class="mb-3">
            <div class="form-row align-items-center">
                <div class="col-md-4">
                    <label for="room_type">Room Type</label>
                    <select name="room_type" id="room_type" class="form-control" required>
                        <option value="">Select Room Type</option>
                        <?php while ($row = $room_types_result->fetch_assoc()): ?>
                            <option value="<?php echo $row['room_type']; ?>" <?php echo isset($_GET['room_type']) && $_GET['room_type'] == $row['room_type'] ? 'selected' : ''; ?>>
                                <?php echo $row['room_type']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="checkin_checkout_date">Check-in & Check-out Dates</label>
                    <input type="text" id="checkin_checkout_date" name="checkin_checkout_date" class="form-control" placeholder="Select Dates" required 
                           value="<?php echo isset($_GET['checkin_checkout_date']) ? $_GET['checkin_checkout_date'] : ''; ?>">
                </div>
                <div class="col-md-2">
                    <label for="cost_type">Cost Type</label>
                    <select name="cost_type" id="cost_type" class="form-control" required>
                        <option value="official" <?php echo ($cost_type == 'official') ? 'selected' : ''; ?>>Official</option>
                        <option value="others" <?php echo ($cost_type == 'others') ? 'selected' : ''; ?>>Others</option>
                    </select>
                </div>
                <div class="col-md-2 text-center">
                    <button type="submit" class="btn btn-primary btn-block">Search</button>
                </div>
            </div>
        </form>

        <!-- Room List -->
        <form method="POST" action="reservation_form.php">
            <input type="hidden" id="room_ids" name="room_ids" value="">
            <input type="hidden" id="room_numbers" name="room_numbers" value="">
            <input type="hidden" id="checkin_date" name="checkin_date" value="<?php echo htmlspecialchars($checkin_date); ?>">
            <input type="hidden" id="checkout_date" name="checkout_date" value="<?php echo htmlspecialchars($checkout_date); ?>">
            <input type="hidden" id="room_type" name="room_type" value="<?php echo htmlspecialchars($room_type); ?>">
            <input type="hidden" id="cost_type" name="cost_type" value="<?php echo htmlspecialchars($cost_type); ?>">

            <div class="row" style="max-height: 750px; overflow-y: auto;">
                <?php if (!empty($rooms)): ?>
                    <?php foreach ($rooms as $room): ?>
                        <div class="col-md-4">
                            <div class="card room-card <?php echo (!empty($room['is_booked']) && $room['is_booked'] > 0) ? 'booked' : ''; ?>" data-room-id="<?php echo htmlspecialchars($room['id']); ?>" data-room-number="<?php echo htmlspecialchars($room['room_number']); ?>" data-official="<?php echo htmlspecialchars($room['cost_official']); ?>" data-others="<?php echo htmlspecialchars($room['cost_others']); ?>">
                                <img src="<?php echo htmlspecialchars($room['image']); ?>" class="card-img-top" alt="Room Image">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($room['room_type']); ?> - Room <?php echo htmlspecialchars($room['room_number']); ?></h5>
                                    <p>
                                        <strong>Price:</strong><br>
                                        ✅ <b>Official:</b> ₹<?php echo htmlspecialchars($room['cost_official']); ?><br>
                                        ✅ <b>Others:</b> ₹<?php echo htmlspecialchars($room['cost_others']); ?>
                                    </p>
                                    <?php if (!empty($room['is_booked']) && $room['is_booked'] > 0): ?>
                                        <p class="text-danger font-weight-bold">Booked</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            <?php else: ?>
                                <div class="alert alert-danger text-center w-100">No rooms available for the selected dates.</div>
                                <?php endif; ?>
                            </div>

            <div class="text-center mt-3">
                <button type="submit" name="confirm_booking" class="btn btn-success">Book Now</button>
            </div>
                        </form>
                    </div>
                    
    
                    <?php include 'includes/footer.php'; ?>               
                    
                    <script>
    document.addEventListener("DOMContentLoaded", function () {
        flatpickr("#checkin_checkout_date", {
            mode: "range",
            dateFormat: "Y-m-d",
            minDate: "today"
        });

        document.querySelector("form[action='reservation_form.php']").addEventListener("submit", function (e) {
            const selectedRooms = document.querySelectorAll(".room-card.selected");
            if (selectedRooms.length === 0) {
                alert("Please select at least one room to book.");
                e.preventDefault();
            } else {
                const roomIds = Array.from(selectedRooms).map(card => card.dataset.roomId);
                const roomNumbers = Array.from(selectedRooms).map(card => card.dataset.roomNumber);
                document.getElementById("room_ids").value = roomIds.join(',');
                document.getElementById("room_numbers").value = roomNumbers.join(',');
            }
        });

        // Add event listener to cards to select the card when clicked
        document.querySelectorAll(".room-card").forEach(function(card) {
            card.addEventListener("click", function() {
                this.classList.toggle("selected");
            });
        });
    });
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>