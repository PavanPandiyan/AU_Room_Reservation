<?php
require_once 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    // Ensure all required fields exist
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $username = isset($_POST['username']) ? trim($_POST['username']) : null;
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
    $gender = isset($_POST['gender']) ? trim($_POST['gender']) : null;
    $age = isset($_POST['age']) ? intval($_POST['age']) : null;
    $aadhar = isset($_POST['aadhar']) ? trim($_POST['aadhar']) : null;

    if (!$id || !$username || !$email || !$phone || !$gender || !$age || !$aadhar) {
        echo "error: Missing data";
        exit();
    }

    // Prepare and execute update query
    $stmt = $conn->prepare("UPDATE users SET username=?, mailid=?, phone=?, gender=?, age=?, aadhar=? WHERE id=?");
    if (!$stmt) {
        echo "error: Prepare failed - " . $conn->error;
        exit();
    }

    $stmt->bind_param("ssssisi", $username, $email, $phone, $gender, $age, $aadhar, $id);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error: " . $stmt->error;
    }
    exit();
}
require_once 'db_connect.php';

// Debugging: Check if delete request is received
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    if (!$id) {
        echo "error: Invalid user ID";
        exit();
    }

    // Debugging: Check if user exists before deleting
    $check = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        echo "error: User not found";
        exit();
    }

    // Proceed with deletion
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    if (!$stmt) {
        echo "error: Prepare failed - " . $conn->error;
        exit();
    }

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error: " . $stmt->error;
    }
    exit();
}

// Fetch users
$result = $conn->query("SELECT * FROM users");
?>




<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin - Manage Users</title>
        <link rel="stylesheet" href="css/manage_user.css">
        <link rel="stylesheet" href="css/header.css">
        <link rel="stylesheet" href="css/admin_sidebar.css">
    </head>
    <body>
        <?php include 'includes/header.php'; ?>
        <div class="flex1">
        <?php include 'includes/admin_sidebar.php'; ?>
        <div class="container">
    <div class="filter-search">
        <input type="text" id="searchInput" placeholder="Search by Username, Email, or Phone">
        <select id="genderFilter">
            <option value="">All Genders</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>
        <button onclick="filterUsers()">Search</button>
    </div>

    <table id="userTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Gender</th>
                <th>Age</th>
                <th>Aadhar</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr id="row_<?php echo $row['id']; ?>">
            <td><?php echo $row['id']; ?></td>
            <td class="editable-field username"><?php echo $row['username']; ?></td>
            <td class="editable-field email"><?php echo $row['mailid']; ?></td>
            <td class="editable-field phone"><?php echo $row['phone']; ?></td>
            <td class="editable-field gender"><?php echo $row['gender']; ?></td>
            <td class="editable-field age"><?php echo $row['age']; ?></td>
            <td class="editable-field aadhar"><?php echo $row['aadhar']; ?></td>
            <td>
                <button class="btn edit-btn" onclick="editUser(<?php echo $row['id']; ?>)">Edit</button>
                <button class="btn save-btn" onclick="saveUser(<?php echo $row['id']; ?>)" style="display: none;">Save</button>
                <button class="btn delete-btn" onclick="deleteUser(<?php echo $row['id']; ?>)">Delete</button>
            </td>
        </tr>
    <?php endwhile; ?>
</tbody>

    </table>
</div>

            </div>
            <?php include 'includes/footer.php'; ?>
            <script src="js/manage_users.js"></script>
        </body>
        </html>