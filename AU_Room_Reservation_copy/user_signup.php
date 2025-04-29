<?php
// Include database connection file
require_once 'db_connect.php';

function create_user($conn, $username, $email, $password, $phone, $gender, $age, $aadhar) {
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE mailid = ? OR username = ?");
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return "Username or Email already exists!";
    } else if (!preg_match("/^[6-9]\d{9}$/", $phone)) {
        return "Invalid phone number!";
    } else if ($age < 18 || $age > 100) {
        return "Invalid age!";
    } else if (!preg_match("/^\d{12}$/", $aadhar)) {
        return "Invalid Aadhar number!";
    } else {
        // Proceed to insert new user
        $query = "INSERT INTO users (username, password, mailid, phone, gender, age, aadhar) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt2 = $conn->prepare($query);
        $stmt2->bind_param("sssssis", $username, $password_hashed, $email, $phone, $gender, $age, $aadhar);

        if ($stmt2->execute()) {
            return "Account created successfully!";
        } else {
            return "Error creating account! Please try again.";
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username_signup'];
    $email = $_POST['email_signup'];
    $password = $_POST['password_signup'];
    $confirm_password = $_POST['confirm_password_signup'];
    $phone = $_POST['phone_signup'];
    $gender = $_POST['gender_signup'];
    $age = $_POST['age_signup'];
    $aadhar = $_POST['aadhar_signup'];
    
    if ($password !== $confirm_password) {
        $signup_error = "Passwords do not match!";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $signup_error = "Invalid email format!";
    } else if (!preg_match("/^[6-9]\d{9}$/", $phone)) {
        $signup_error = "Invalid phone number! It must be a 10-digit number starting with 6-9.";
    } else if ($age < 18 || $age > 100) {
        $signup_error = "Invalid age! Age must be between 18 and 100.";
    } else if (!preg_match("/^\d{12}$/", $aadhar)) {
        $signup_error = "Invalid Aadhar number! It must be a 12-digit number.";
    } else if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $password)) {
        $signup_error = "Password must be at least 8 characters long, include at least one letter, one number, and one special character.";
    } else {
        $signup_message = create_user($conn, $username, $email, $password, $phone, $gender, $age, $aadhar);
        
        if ($signup_message == "Account created successfully!") {
            header("Location: user_login.php");
            exit();
        } else {
            $signup_error = $signup_message;
        }
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Signup</title>
    <link rel="stylesheet" href="css/user_signup.css">
    <script>
        function showValidationMessage(event) {
            const field = event.target;
            const validationContainer = document.getElementById("validation-container");

            if (field.id === "phone_signup") {
                validationContainer.textContent = "Phone number must be a 10-digit number starting with 6-9.";
            } else if (field.id === "age_signup") {
                validationContainer.textContent = "Age must be between 18 and 100.";
            } else if (field.id === "aadhar_signup") {
                validationContainer.textContent = "Aadhar number must be a 12-digit number.";
            } else if (field.id === "password_signup") {
                validationContainer.textContent = "Password must be at least 8 characters long, include at least one letter, one number, and one special character.";
            } else if (field.id === "email_signup") {
                validationContainer.textContent = "Email must be in a valid format (e.g., user@example.com).";
            } else {
                validationContainer.textContent = "";
            }
        }

        function clearValidationMessage() {
            const validationContainer = document.getElementById("validation-container");
            validationContainer.textContent = "";
        }
    </script>
</head>
<body>
    <div class="login-container">
        <h2>Create an Account</h2>
        <?php
        if (isset($signup_success)) {
            echo "<div class='success'>$signup_success</div>";
        }
        if (isset($signup_error)) {
            echo "<div class='error'>$signup_error</div>";
        }
        ?>
        <form action="user_signup.php" method="post">
            <div id="validation-container" class="validation-container"></div>
            <div class="form-group">
                <label for="username_signup">Username:</label>
                <input type="text" id="username_signup" name="username_signup" required onclick="showValidationMessage(event)" onblur="clearValidationMessage()">
            </div>
            <div class="form-group">
                <label for="email_signup">Email:</label>
                <input type="email" id="email_signup" name="email_signup" required onclick="showValidationMessage(event)" onblur="clearValidationMessage()">
            </div>
            <div class="form-group">
                <label for="password_signup">Password:</label>
                <input type="password" id="password_signup" name="password_signup" required onclick="showValidationMessage(event)" onblur="clearValidationMessage()">
            </div>
            <div class="form-group">
                <label for="confirm_password_signup">Confirm Password:</label>
                <input type="password" id="confirm_password_signup" name="confirm_password_signup" required onclick="showValidationMessage(event)" onblur="clearValidationMessage()">
            </div>
            <div class="form-group">
                <label for="phone_signup">Phone Number:</label>
                <input type="text" id="phone_signup" name="phone_signup" required onclick="showValidationMessage(event)" onblur="clearValidationMessage()">
            </div>
            <div class="form-group">
                <label for="gender_signup">Gender:</label>
                <select id="gender_signup" name="gender_signup" required onclick="showValidationMessage(event)" onblur="clearValidationMessage()">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="age_signup">Age:</label>
                <input type="number" id="age_signup" name="age_signup" required onclick="showValidationMessage(event)" onblur="clearValidationMessage()">
            </div>
            <div class="form-group">
                <label for="aadhar_signup">Aadhar Number:</label>
                <input type="text" id="aadhar_signup" name="aadhar_signup" required onclick="showValidationMessage(event)" onblur="clearValidationMessage()">
            </div>
            <button type="submit" name="signup">Sign Up</button>
        </form>
        <p>Already have an account? <a href="user_login.php">Login</a></p>
        <p><a href="index.php" class="home-button">Home</a></p>
    </div>
</body>
</html>