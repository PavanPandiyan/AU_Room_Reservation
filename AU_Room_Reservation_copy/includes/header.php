<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/header.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <title>Annamalai University Guesthouse</title>
</head>
<body>
    <header>
        <nav>
            <div class="nav-left">
                <img src="logo/aulogo.png" alt="Logo" class="logo">
                <span class="company-name">Annamalai University GuestHouse</span>
            </div>
            <div class="nav-right">
                <ul id="nav-links">
                    <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>

                    <!-- Show Profile Icon for Admin -->
                    <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']): ?>
                        <li class="profile-container">
                            <a href="admin_profile.php" id="adminProfileIcon" class="profile-icon">
                                <i class="fas fa-user-circle"></i>
                            </a>
                        </li>
                    <!-- Show Profile Icon for User -->
                    <?php elseif (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in']): ?>
                        <li class="profile-container">
                            <a href="user_profile.php" id="userProfileIcon" class="profile-icon">
                                <i class="fas fa-user-circle"></i>
                            </a>
                        </li>
                    <?php else: ?>
                        <button id="loginBtn"><i class="fas fa-sign-in-alt"></i> Login</button>
                    <?php endif; ?>
                </ul>

                <?php if (!isset($_SESSION['admin_logged_in']) && !isset($_SESSION['user_logged_in'])): ?>
                <div id="loginOptions" style="display: none;">
                    <div class="dropdown-content">
                        <a href="admin_login.php" class="option admin-option">
                            <i class="fas fa-user-shield"></i>
                            <span>Admin</span>
                        </a>    
                        <a href="user_login.php" class="option user-option">
                            <i class="fas fa-user"></i>
                            <span>User</span>
                        </a>
                    </div>
                </div>
                <?php endif; ?>

                <button class="menu-icon" onclick="toggleMenu()">☰</button>
            </div>
        </nav>
    </header>
    <script src="js/header.js" defer></script>
</body>
</html>
