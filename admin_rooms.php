<?php
// Prevent caching of old data
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

include 'db_connect.php';

$query = "SELECT * FROM rooms WHERE available_rooms > 0";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/admin_rooms.css">
        <title>Document</title>
    </head>
    <body>
        <?php include 'includes/header.php'; ?>
        <div class="flex1">
        <?php include 'includes/admin_sidebar.php'; ?>
<div class="container mt-5">
    <?php if (mysqli_num_rows($result) > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Room Type</th>
                        <th>Image</th>
                        <th>Available Rooms</th>
                        <th>Cost for Official</th>
                        <th>Cost for Others</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['room_type']; ?></td>
                            <td><img src="<?php echo $row['image']; ?>" alt="<?php echo $row['room_type']; ?>"></td>
                            <td><?php echo $row['available_rooms']; ?></td>
                            <td><?php echo $row['cost_official']; ?></td>
                            <td><?php echo $row['cost_others']; ?></td>
                            <td>
                                <a href="book_room.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Book Now</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="alert alert-danger">
                        No rooms are available at this time.
                    </div>
                    <?php endif; ?>
                </div>
                </div>
                <?php
    // Close the database connection
    mysqli_close($conn);
    ?>
    <?php include 'includes/footer.php'; ?>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>