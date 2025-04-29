<!-- Sidebar Navigation -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/user_sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <title>Dashboard</title>
</head>

<body>
    <div class="sidenav">
        <a href="user_profile.php" class="nav-link">
            <i class="fa-solid fa-user"></i> Profile
        </a>
        <a href="user_reservation.php" class="nav-link">
            <i class="fa-solid fa-calendar-check"></i> Reservations
        </a>

        <a href="user_payments.php" class="nav-link">
            <i class="fa-solid fa-wallet"></i> Payments
        </a>
        <a href="user_history.php" class="nav-link">
            <i class="fa-solid fa-clock-rotate-left"></i> History
        </a>

        <a href="logout.php" class="nav-link">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
    </div>
    <script src="js/user_sidebar.js"></script>

</body>

</html>
