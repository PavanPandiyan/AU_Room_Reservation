<?php
session_start();

$servername = "localhost";
$db_username = "root";
$db_password = "";
$db_name = "au_guesthouse";

$conn = new mysqli($servername, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed."]));
}

// ✅ Handle POST Requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ✅ Fetch Individual Rooms by Type
    if (isset($_POST['fetch_rooms'])) {
        $room_type = $_POST['room_type'] ?? '';
        $stmt = $conn->prepare("SELECT * FROM room_list WHERE room_type = ?");
        $stmt->bind_param("s", $room_type);
        $stmt->execute();
        $result = $stmt->get_result();
        $rooms = [];

        while ($row = $result->fetch_assoc()) {
            $rooms[] = $row;
        }

        echo json_encode($rooms);
        exit();
    }

    // ✅ Update Room Data
    if (isset($_POST['update_room'])) {
        $room_id = intval($_POST['room_id']);
        $room_number = $_POST['room_number'];
        $cost_official = floatval($_POST['cost_official']);
        $cost_others = floatval($_POST['cost_others']);
        $status = $_POST['status'];

        $stmt = $conn->prepare("UPDATE room_list SET room_number = ?, cost_official = ?, cost_others = ?, status = ? WHERE id = ?");
        $stmt->bind_param("sddsi", $room_number, $cost_official, $cost_others, $status, $room_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Update failed."]);
        }
        exit();
    }

    // ✅ Delete Room
    if (isset($_POST['delete_room'])) {
        $room_id = intval($_POST['room_id']);
        $stmt = $conn->prepare("DELETE FROM room_list WHERE id = ?");
        $stmt->bind_param("i", $room_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Delete failed."]);
        }
        exit();
    }

    // ✅ Add New Room Type with Image and Multiple Room Numbers
    if (isset($_POST['add_room_type'])) {
        $room_type = $_POST['room_type'] ?? '';
        $room_numbers = $_POST['room_numbers'] ?? '';
        $cost_official = floatval($_POST['cost_official']);
        $cost_others = floatval($_POST['cost_others']);

        // Handle Image Upload
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $image_path = 'uploads/default.png'; // Default image
        if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] == UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['room_image']['tmp_name'];
            $image_name = basename($_FILES['room_image']['name']);
            $image_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $image_name);
            $target_file = $target_dir . $image_name;

            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($imageFileType, $allowed_types)) {
                if (move_uploaded_file($tmp_name, $target_file)) {
                    $image_path = $target_file;
                }
            }
        }

        if (empty($room_type) || empty($room_numbers)) {
            echo json_encode(["status" => "error", "message" => "Room type and room numbers are required."]);
            exit();
        }

        // Insert each room number as a separate entry
        $room_numbers_array = array_map('trim', explode(',', $room_numbers));
        $stmt = $conn->prepare("INSERT INTO room_list (room_type, room_number, cost_official, cost_others, status, image) VALUES (?, ?, ?, ?, 'available', ?)");

        foreach ($room_numbers_array as $room_number) {
            $stmt->bind_param("sddss", $room_type, $room_number, $cost_official, $cost_others, $image_path);
            $stmt->execute();
        }

        echo json_encode(["status" => "success"]);
        exit();
    }
}

// ✅ Fetch Room Types for Display
$room_types_query = "
    SELECT room_type, 
           COUNT(*) AS total_rooms, 
           SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) AS available_rooms, 
           SUM(CASE WHEN status = 'booked' THEN 1 ELSE 0 END) AS booked_rooms, 
           COALESCE(MIN(NULLIF(image, '')), 'uploads/default.png') AS room_image,
           MIN(cost_official) AS cost_official,
           MIN(cost_others) AS cost_others
    FROM room_list 
    GROUP BY room_type";
$room_types_result = $conn->query($room_types_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Room Management</title>
    <link rel="stylesheet" href="css/admin_room_management.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="container">
        <?php include 'includes/admin_sidebar.php'; ?>
        <div class="main-content">
            <div class="room-container">
                <?php while ($row = mysqli_fetch_assoc($room_types_result)) { ?>
                    <div class="room-card">
                        <img src="<?= htmlspecialchars($row['room_image']); ?>" alt="Room Image">
                        <h3><?= htmlspecialchars($row['room_type']); ?></h3>
                        <p><strong>Total Rooms:</strong> <?= $row['total_rooms']; ?></p>
                        <p><strong>Available:</strong> <?= $row['available_rooms']; ?></p>
                        <p><strong>Booked:</strong> <?= $row['booked_rooms']; ?></p>
                        <p><strong>Cost (Official):</strong> ₹<?= number_format($row['cost_official'], 2); ?></p>
                        <p><strong>Cost (Others):</strong> ₹<?= number_format($row['cost_others'], 2); ?></p>
                        <button class="view-rooms-btn" data-type="<?= htmlspecialchars($row['room_type']); ?>">View Rooms</button>
                    </div>
                <?php } ?>
                <button id="openAddRoomTypeModal">Add New Room Type</button>
            </div>
        </div>
    </div>

    <!-- ✅ View Rooms Modal -->
    <div class="modal" id="viewRoomsModal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h3>Room Details</h3>
            <div id="roomDetails" class="room-grid"></div>
        </div>
    </div>

    <!-- ✅ Add New Room Type Modal -->
    <div class="modal" id="addRoomTypeModal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h3>Add New Room Type</h3>
            <form id="addRoomTypeForm" enctype="multipart/form-data">
                <label for="room_type">Room Type:</label>
                <input type="text" name="room_type" required>

                <label for="room_numbers">Room Numbers (comma-separated):</label>
                <input type="text" name="room_numbers" placeholder="e.g., 101, 102, 103" required>

                <label for="room_image">Room Image:</label>
                <input type="file" name="room_image" accept="image/*">

                <label for="cost_official">Cost (Official):</label>
                <input type="number" name="cost_official" step="0.01" required>

                <label for="cost_others">Cost (Others):</label>
                <input type="number" name="cost_others" step="0.01" required>

                <input type="hidden" name="add_room_type" value="true">
                <button type="submit">Add Room Type</button>
            </form>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
    $(document).ready(function () {
        // Open the Add New Room Type Modal
        $(document).on("click", "#openAddRoomTypeModal", function () {
            $("#addRoomTypeModal").show();
        });

        // Close Modals
        $(document).on("click", ".close-modal", function () {
            $(this).closest(".modal").hide();
        });

        // Submit the Add Room Type Form
        $(document).on("submit", "#addRoomTypeForm", function (e) {
            e.preventDefault();

            // Create FormData object to handle file upload
            let formData = new FormData(this);

            $.ajax({
                url: "admin_room_management.php",
                type: "POST",
                data: formData,
                contentType: false, // Important for file upload
                processData: false, // Important for file upload
                success: function (response) {
                    let res = JSON.parse(response);
                    if (res.status === "success") {
                        alert("New room type added successfully!");
                        location.reload();
                    } else {
                        alert(res.message);
                    }
                }
            });
        });

        // Fetch and Display Rooms
        $(document).on("click", ".view-rooms-btn", function () {
            let roomType = $(this).data("type");

            $.post("admin_room_management.php", { fetch_rooms: true, room_type: roomType }, function(response) {
                let rooms = JSON.parse(response);
                let roomDetails = rooms.length === 0 ? "<p>No rooms available for this type.</p>" : "";

                rooms.forEach(room => {
                    roomDetails += `
                        <div class='room-detail-card' data-id='${room.id}'>
                            <p><strong>Room Number:</strong> <input type="text" value="${room.room_number}" class="room-input" disabled></p>
                            <p><strong>Cost (Official):</strong> ₹<input type="number" value="${parseFloat(room.cost_official).toFixed(2)}" class="room-input" disabled></p>
                            <p><strong>Cost (Others):</strong> ₹<input type="number" value="${parseFloat(room.cost_others).toFixed(2)}" class="room-input" disabled></p>
                            <p><strong>Status:</strong> 
                                <select class="room-status" disabled>
                                    <option value="available" ${room.status === 'available' ? 'selected' : ''}>Available</option>
                                    <option value="booked" ${room.status === 'booked' ? 'selected' : ''}>Booked</option>
                                </select>
                            </p>
                            <button class='edit-room'>Edit</button>
                            <button class='save-room' style="display:none;">Save</button>
                            <button class='cancel-edit' style="display:none;">Cancel</button>
                            <button class='delete-room'>Delete</button>
                        </div>`;
                });

                $("#roomDetails").html(roomDetails);
                $("#viewRoomsModal").show();
            });
        });

        // Delete Room
        $(document).on("click", ".delete-room", function () {
            let card = $(this).closest(".room-detail-card");
            let roomId = card.data("id");

            if (confirm("Are you sure you want to delete this room?")) {
                $.post("admin_room_management.php", { delete_room: true, room_id: roomId }, function (response) {
                    let res = JSON.parse(response);
                    if (res.status === "success") {
                        alert("Room deleted successfully!");
                        card.remove();
                    } else {
                        alert("Error deleting room.");
                    }
                });
            }
        });

        // Edit Room
        $(document).on("click", ".edit-room", function () {
            let card = $(this).closest(".room-detail-card");
            card.find(".room-input, .room-status").prop("disabled", false);
            card.find(".edit-room").hide();
            card.find(".save-room, .cancel-edit").show();
        });

        // Cancel Edit
        $(document).on("click", ".cancel-edit", function () {
            let card = $(this).closest(".room-detail-card");
            card.find(".room-input, .room-status").prop("disabled", true);
            card.find(".edit-room").show();
            card.find(".save-room, .cancel-edit").hide();
        });

        // Save Room Update
        $(document).on("click", ".save-room", function () {
            let card = $(this).closest(".room-detail-card");
            let roomId = card.data("id");
            let roomNumber = card.find(".room-input:eq(0)").val();
            let costOfficial = card.find(".room-input:eq(1)").val();
            let costOthers = card.find(".room-input:eq(2)").val();
            let status = card.find(".room-status").val();

            $.post("admin_room_management.php", {
                update_room: true,
                room_id: roomId,
                room_number: roomNumber,
                cost_official: costOfficial,
                cost_others: costOthers,
                status: status
            }, function (response) {
                let res = JSON.parse(response);
                if (res.status === "success") {
                    alert("Room updated successfully!");
                    card.find(".room-input, .room-status").prop("disabled", true);
                    card.find(".edit-room").show();
                    card.find(".save-room, .cancel-edit").hide();
                } else {
                    alert("Error updating room.");
                }
            });
        });
    });
    </script>
</body>
</html>
