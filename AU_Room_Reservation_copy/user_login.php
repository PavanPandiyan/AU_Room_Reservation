<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'db_connect.php'; // Include database connection

// Redirect already logged-in users
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header("Location: index.php"); // Redirect to rooms.php instead of the user profile
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Query to fetch the user
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            header("Location: rooms.php"); // FIXED: Redirect to rooms.php
            exit();
        } else {
            $login_error = "Invalid username or password!";
        }
    } else {
        $login_error = "Invalid username or password!";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
    <link rel="stylesheet" href="css/user_login.css">
</head>
<body>
    <div class="login-container">
        <h2>User Login</h2>
        <?php if (isset($login_error)): ?>
            <div class='error'><?php echo $login_error; ?></div>
        <?php endif; ?>
        <form action="user_login.php" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="login">Login</button>
        </form>
        <p>Don't have an account? <a href="user_signup.php">Sign Up</a></p>
        <p><a href="forgot_password.php">Forgot Password?</a></p> <!-- Added Forgot Password link -->
        <p><a href="index.php" class="home-button">Home</a></p>
    </div>
</body>
</html>
