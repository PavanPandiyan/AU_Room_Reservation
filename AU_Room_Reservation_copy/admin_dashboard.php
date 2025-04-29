<?php
// Include database connection
require_once 'db_connect.php';

// Fetch statistics
$totalUsers = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$totalReservations = $conn->query("SELECT COUNT(*) AS total FROM reservations")->fetch_assoc()['total'];

// Fetch total rooms by type
$roomTypes = $conn->query("SELECT room_type, COUNT(*) AS total FROM room_list GROUP BY room_type");
$roomsData = [];
while ($row = $roomTypes->fetch_assoc()) {
    $roomsData[$row['room_type']] = $row['total'];
}

// Fetch reservation stats for the chart
$reservationStats = $conn->query("SELECT status, COUNT(*) as count FROM reservations GROUP BY status");
$reservationData = [];
while ($row = $reservationStats->fetch_assoc()) {
    $reservationData[$row['status']] = $row['count'];
}

// Fetch active users based on the users table
$activeUsers = $conn->query("SELECT COUNT(*) AS active FROM users WHERE status = 'active'")->fetch_assoc()['active'];

// Fetch recent reservations
$recentReservations = $conn->query("
    SELECT r.id, u.username, rm.room_type AS room_name, r.checkin_date, r.checkout_date, r.status 
    FROM reservations r 
    JOIN users u ON r.user_id = u.id 
    JOIN room_list rm ON r.room_number = rm.room_number 
    ORDER BY r.id DESC LIMIT 5
");

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin_dashboard.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="css/admin_sidebar.css">
    <link rel="icon" href="logo/aulogo.png" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <?php include 'includes/header.php'; ?>
    <div class="admin-dashboard">
        <?php include 'includes/admin_sidebar.php'; ?>

        <div class="dashboard-content">
            <h2>Welcome, Admin!</h2>

            <div class="dashboard-cards">
                <div class="card">
                    <i class="fas fa-users"></i>
                    <h3>Total Users</h3>
                    <p><?php echo $totalUsers; ?></p>
                </div>
                
                <div class="card">
                    <i class="fas fa-user-check"></i>
                    <h3>Active Users</h3>
                    <p><?php echo $activeUsers; ?></p>
                </div>
                <!-- Display Rooms by Type -->
                <?php foreach ($roomsData as $type => $count) { ?>
                <div class="card">
                    <i class="fas fa-bed"></i>
                    <h3><?php echo htmlspecialchars($type); ?></h3>
                    <p><?php echo $count; ?> Rooms</p>
                </div>
                <?php } ?>
                
                <div class="card">
                    <i class="fas fa-calendar-check"></i>
                    <h3>Total Reservations</h3>
                    <p><?php echo $totalReservations; ?></p>
                </div>

            </div>

            <div class="chart_and_table">
                <div class="dashboard-charts">
                    <h3>Reservation Status</h3>
                    <canvas id="reservationChart"></canvas>
                </div>

                <div class="recent-reservations">
                    <h3>Recent Reservations</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Room</th>
                                <th>Check-In</th>
                                <th>Check-Out</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($reservation = $recentReservations->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $reservation['id']; ?></td>
                                <td><?php echo htmlspecialchars($reservation['username']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['room_name']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['checkin_date']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['checkout_date']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['status']); ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        const ctx = document.getElementById('reservationChart').getContext('2d');
        const reservationData = {
            labels: ['Confirmed', 'Pending', 'Canceled'],
            datasets: [{
                label: 'Reservations',
                data: [
                    <?php echo isset($reservationData['confirmed']) ? $reservationData['confirmed'] : 0; ?>,
                    <?php echo isset($reservationData['pending']) ? $reservationData['pending'] : 0; ?>,
                    <?php echo isset($reservationData['canceled']) ? $reservationData['canceled'] : 0; ?>
                ],
                backgroundColor: ['#4CAF50', '#FF9800', '#FF5252'],
                borderWidth: 1
            }]
        };

        new Chart(ctx, {
            type: 'doughnut',
            data: reservationData
        });
    </script>

</body>
</html>