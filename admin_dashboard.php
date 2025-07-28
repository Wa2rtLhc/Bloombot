<?php
session_start();
include('db_connect.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
// Fetch users
$user_result = $conn->query("SELECT * FROM users");

if (!$user_result) {
    die("Query failed: " . $conn->error);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Bloombot</title>
    <link rel="stylesheet" href="CSS/style.css?v=3">
    
</head>
<body>
<div class="topnav">
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="about.html">About</a>
    <a href="contact.html">Contact Us</a>
    <a href="profile.php">My Profile</a>
    <a href="global_thresholds.php">My Plants</a>
    <div class="topnav-right">
        <a href="logout.php">Logout</a>
    </div>
</div>
    <div class="header">
        <h1>Admin Dashboard</h1>
    </div>
    <div class="main">
        <div class="sidebar">
        <h2>Menu</h2>
        <a class="menu-button" href="admin_dashboard.php">🏠 Dashboard</a>
        <a class="menu-button" href="configure_threshold.php">⚙ Configure Global Thresholds</a>
        <a class="menu-button" href="view_alerts.php">🔔 View Alerts</a>
        <a class="menu-button" href="profile.php">👤 Profile</a>
    </div>


        <div class="content">
            <h2>Welcome, Admin!</h2>
            <?php while ($user = $user_result->fetch_assoc()): ?>
    <div class="user-section">
        <h3><?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars($user['role']) ?>)</h3>
        <p>Email: <?= htmlspecialchars($user['email']) ?></p>
        <a href="edit_user.php?username=<?= urlencode($user['username']) ?>">Edit</a> | 
        <a href="delete_user.php?username=<?= urlencode($user['username']) ?>" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>

        <?php
            $gardener_username = $user['username'];
            $plant_query = "SELECT * FROM plants WHERE gardener_username = ?";
            $stmt = $conn->prepare($plant_query);
            $stmt->bind_param("s", $gardener_username);
            $stmt->execute();
            $plant_result = $stmt->get_result();
        ?>

        <?php if ($plant_result->num_rows > 0): ?>
            <table class="plant-table">
                <tr>
                    <th>Plant Name</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                <?php while ($plant = $plant_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($plant['name']) ?></td>
                        <td><?= htmlspecialchars($plant['type']) ?></td>
                        <td><?= htmlspecialchars($plant['status'] ?? 'Not Set') ?></td>
                        <td>
                            <a href="edit_plant.php?id=<?= $plant['id'] ?>">Edit</a> | 
                            <a href="delete_plant.php?id=<?= $plant['id'] ?>" onclick="return confirm('Delete this plant?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p><em>No plants added by this user.</em></p>
        <?php endif; ?>
    </div>
<?php endwhile; ?>



            

</body>
</html>
