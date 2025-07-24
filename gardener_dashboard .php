<?php
session_start();
include('db_connect.php');

// Ensure user is logged in and is a gardener
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'gardener') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch latest sensor data for this gardener (if available)
$sensor_query = mysqli_query($conn, "SELECT * FROM sensor_data WHERE id = $user_id ORDER BY timestamp DESC LIMIT 5");
$sensor_data = mysqli_num_rows($sensor_query) > 0 ? mysqli_fetch_all($sensor_query, MYSQLI_ASSOC) : [];

// Fetch gardener's threshold settings
$threshold_query = mysqli_query($conn, "SELECT * FROM thresholds WHERE id = $user_id LIMIT 1");
$threshold = mysqli_fetch_assoc($threshold_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gardener Dashboard - Bloombot</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>

<!-- Top Navigation -->
<div class="topnav">
    <a href="gardener_dashboard.php">Dashboard</a>
    <a href="about.html">About</a>
    <a href="team.html">Team</a>
    <div class="topnav-right">
        <a href="logout.php">Logout</a>
    </div>
</div>

<!-- Header -->
<div class="header">
    <h1>Welcome, Gardener!</h1>
</div>

<!-- Main Layout -->
<div class="main">
    <!-- Sidebar Menu -->
    <div class="sidebar">
        <h2>Menu</h2>
        <a class="menu-button" href="add_plant.php">🌿 Add/View Plants</a><br>
        <a class="menu-button" href="set_threshold.php">⚙ Set Thresholds</a><br>
        <a class="menu-button" href="view_alerts.php">🔔 View Alerts</a><br>
        <a class="menu-button" href="sensor_data.php">📊 Add Sensor Data</a><br>
        <a class="menu-button" href="profile.php">👤 Profile</a>
    </div>

    <!-- Content Area -->
    <div class="content">
        <h2>Dashboard Overview</h2>
        <p>Here you can manage your plants, set thresholds, and view alerts.</p>

        <!-- Display Latest Sensor Data -->
        <h3>Recent Sensor Readings</h3>
        <?php if (!empty($sensor_data)): ?>
            <table border="1" width="100%" cellpadding="8" cellspacing="0">
                <tr style="background-color: #2c7a5d; color: white;">
                    <th>Timestamp</th>
                    <th>Temperature (°C)</th>
                    <th>Moisture (%)</th>
                    <th>Light Level (%)</th>
                </tr>
                <?php foreach ($sensor_data as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['timestamp']) ?></td>
                        <td><?= htmlspecialchars($row['temperature']) ?></td>
                        <td><?= htmlspecialchars($row['moisture']) ?></td>
                        <td><?= htmlspecialchars($row['light_level']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No sensor data available yet.</p>
        <?php endif; ?>

        <!-- Display Threshold Settings -->
        <h3>Your Threshold Settings</h3>
        <?php if ($threshold): ?>
            <p>Moisture Min: <?= htmlspecialchars($threshold['moisture_min']) ?>%</p>
            <p>Moisture Max: <?= htmlspecialchars($threshold['moisture_max']) ?>%</p>
            <p>Temperature Min: <?= htmlspecialchars($threshold['temperature_min']) ?>°C</p>
            <p>Temperature Max: <?= htmlspecialchars($threshold['temperature_max']) ?>°C</p>
        <?php else: ?>
            <p>No thresholds set yet. Please set them in the "Set Thresholds" section.</p>
        <?php endif; ?>
    <h2>Your Plants and Status</h2>
    <?php
    // Fetch all plants for this gardener
    $plant_query = mysqli_query($conn, "SELECT * FROM plants WHERE id = $user_id");
    if (mysqli_num_rows($plant_query) > 0):
        while ($plant = mysqli_fetch_assoc($plant_query)):

            $plant_id = $plant['id'];
            $plant_name = $plant['name'];

            // Get latest sensor data
            $sensor_sql = "SELECT * FROM sensor_data WHERE plant_id = $plant_id ORDER BY timestamp DESC LIMIT 1";
            $sensor_res = mysqli_query($conn, $sensor_sql);
            $sensor = mysqli_fetch_assoc($sensor_res);

            // Get thresholds
            $threshold_sql = "SELECT * FROM thresholds WHERE plant_id = $plant_id";
            $threshold_res = mysqli_query($conn, $threshold_sql);
            $threshold = mysqli_fetch_assoc($threshold_res);

            // Determine status
            $status = "Healthy";
            if ($sensor) {
                if ($threshold) {
                    if ($sensor['moisture'] < $threshold['min_moisture']) {
                        $status = "Needs Water";
                    } elseif ($sensor['temperature'] < $threshold['min_temp']) {
                        $status = "Too Cold";
                    } elseif ($sensor['temperature'] > $threshold['max_temp']) {
                        $status = "Too Hot";
                    } elseif ($sensor['light_level'] < $threshold['min_light']) {
                        $status = "Too Dark";
                    } elseif ($sensor['light_level'] > $threshold['max_light']) {
                        $status = "Too Bright";
                    }
                } else {
                    $status = "No Thresholds Set";
                }
            } else {
                $status = "No Sensor Data";
            }
    ?>
        <div style="border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;">
            <h3><?= htmlspecialchars($plant_name) ?></h3>
            <p><strong>Status:</strong> <?= $status ?></p>
            <?php if ($sensor): ?>
                <p><strong>Last Reading:</strong> Temp: <?= $sensor['temperature'] ?>°C, Moisture: <?= $sensor['moisture'] ?>%, Light: <?= $sensor['light_level'] ?>%</p>
            <?php endif; ?>
        </div>
    <?php endwhile; else: ?>
        <p>You have no plants added yet.</p>
    <?php endif;?>
</div>

</body>
</html>
