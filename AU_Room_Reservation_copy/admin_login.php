<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize error message
$admin_login_error = $_SESSION['admin_login_error'] ?? '';
unset($_SESSION['admin_login_error']);

// Include database connection
include('db_connect.php');

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['admin_login'])) {
    // Get form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    try {
        // Use prepared statement for security
        $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            // Debugging output
            echo 'Entered Password: ' . $password . '<br>';
            echo 'Stored Hashed Password: ' . $row['password'] . '<br>';
            
            if (password_verify($password, $row['password'])) {
                echo 'Password verified!<br>';
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $row['username'];
                header("Location: admin_dashboard.php");
                exit();
            } else {
                echo 'Password verification failed!<br>';
                $admin_login_error = "Invalid password!";
            }
        } else {
            $admin_login_error = "Username not found!";
        }
    } catch (Exception $e) {
        $admin_login_error = "Database error: " . $e->getMessage();
    }

    // Store error and redirect back
    if (!empty($admin_login_error)) {
        $_SESSION['admin_login_error'] = $admin_login_error;
        header("Location: admin_login.php");
        exit();
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
    <title>Admin Login</title>
    <link rel="icon" href="logo/aulogo.png" type="image/png">
    <link rel="stylesheet" href="css/admin_login.css"> 
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <?php if (!empty($admin_login_error)): ?>
            <div class='error'><?php echo htmlspecialchars($admin_login_error); ?></div>
        <?php endif; ?>
        <form action="admin_login.php" method="post"> 
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="admin_login">Login</button>
        </form>
        <p><a href="index.php" class="home-button">Home</a></p>
    </div>
</body>
</html>
