<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'db_connect.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset'])) {
    $mailid = trim($_POST['email']); // Changed variable name
    $new_password = isset($_POST['new_password']) ? trim($_POST['new_password']) : ''; // Ensure new_password is set

    // Query to check if the mailid exists
    $query = "SELECT * FROM users WHERE mailid = ?"; // Updated column name
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $mailid); // Changed variable name
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT); // Hash the new password

        // Update the password in the database
        $query = "UPDATE users SET password = ? WHERE mailid = ?"; // Updated column name
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $hashed_password, $mailid); // Changed variable name
        $stmt->execute();

        $reset_message = "Your password has been reset successfully.";
    } else {
        $reset_error = "No account found with that email address.";
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
    <title>Forgot Password</title>
    <link rel="stylesheet" href="css/user_login.css">
</head>
<body>
    <div class="login-container">
        <h2>Forgot Password</h2>
        <?php if (isset($reset_error)): ?>
            <div class='error'><?php echo $reset_error; ?></div>
        <?php endif; ?>
        <?php if (isset($reset_message)): ?>
            <div class='message'><?php echo $reset_message; ?></div>
        <?php endif; ?>
        <form action="forgot_password.php" method="post">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <button type="submit" name="reset">Reset Password</button>
        </form>
        <p><a href="user_login.php">Back to Login</a></p>
    </div>
</body>
</html>
