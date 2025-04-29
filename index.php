<?php
session_start();
include 'db_connect.php';

// Handle login logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $query = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];

        // Always redirect to rooms.php after login
        header("Location: rooms.php");
        exit();
    } else {
        echo "<script>alert('Invalid username or password'); window.location.href='user_login.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annamalai University Guesthouse</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="logo/aulogo.png" type="image/png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Welcome to Annamalai University Guesthouse</h1>
            <p>Experience comfort and convenience at its best. Book your stay with us today!</p>
            <?php 
            if (isset($_SESSION['username']) && $_SESSION['username'] != '') {
                echo '<a href="rooms.php" class="btn">Book Now</a>';
            } else {
                echo '<a href="user_login.php" class="btn">Book Now</a>';
            }
            ?>
        </div>
    </section>

    <!-- Rooms Section -->
    <section class="rooms">
        <h2 style="text-align: center;">Room Categories</h2>
        <div class="room-container">
            <?php
            $rooms = [
                ['image' => 'img/h8.jpeg', 'title' => 'Non AC Room', 'description' => 'Comfortable and affordable, perfect for short stays.'],
                ['image' => 'img/h6.jpeg', 'title' => 'AC Room', 'description' => 'Spacious and luxurious, ideal for longer stays.'],
                ['image' => 'img/h5.jpeg', 'title' => 'Suite Room', 'description' => 'Elegant and premium, for a truly special experience.'],
                ['image' => 'img/h7.jpeg', 'title' => 'Dining Hall', 'description' => 'Ideal for families, spacious and comfortable.'],
            ];
            
            foreach ($rooms as $room) {
                echo '<div class="room-card">
                        <img src="' . $room['image'] . '" alt="' . $room['title'] . '" class="room-image">
                        <div class="room-info">
                            <h3>' . $room['title'] . '</h3>
                            <p>' . $room['description'] . '</p>
                        </div>
                    </div>';
            }
            ?>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/script.js"></script>
</body>
</html>
