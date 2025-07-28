<?php
session_start();
include('db_connect.php');

// Ensure user is logged in and is a gardener
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'gardener') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Fetch user's plants
$plant_query = mysqli_query($conn, "SELECT id, name FROM plants WHERE gardener_username = '$username'");
$plants = mysqli_fetch_all($plant_query, MYSQLI_ASSOC);

// Handle form submission
$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $plant_id = $_POST['plant_id'];
    $temperature = $_POST['temperature'];
    $moisture = $_POST['moisture'];
    $light_level = $_POST['light_level'];

    if ($plant_id && $temperature !== '' && $moisture !== '' && $light_level !== '') {
        $stmt = $conn->prepare("INSERT INTO sensor_data (plant_id, temperature, moisture, light_level, timestamp) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiii", $plant_id, $temperature, $moisture, $light_level);

        if ($stmt->execute()) {
            $message = "Sensor data added successfully!";
        } else {
            $message = "Error adding data.";
        }
    } else {
        $message = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manual Sensor Input - Bloombot</title>
    <link rel="stylesheet" href="CSS/style.css?v=4">
</head>
<body>

<div class="topnav">
    <a href="gardener_dashboard .php">Dashboard</a>
    <a href="about.html">About</a>
    <a href="contact.html">Contact</a>
    <a href="profile.php">My Profile</a>
    <a href="view_plants.php">My Plants</a>
    <div class="topnav-right">
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="header">
    <h1>Manual Sensor Data Entry</h1>
</div>

<div class="main">
    <div class="content">
        <?php if (!empty($message)) echo "<p><strong>$message</strong></p>"; ?>

        <form action="" method="POST">
            <label for="plant_id">Select Plant:</label><br>
            <select name="plant_id" required>
                <option value="">-- Select Plant --</option>
                <?php foreach ($plants as $plant): ?>
                    <option value="<?= $plant['id'] ?>"><?= htmlspecialchars($plant['name']) ?> (ID: <?= $plant['id'] ?>)</option>
                <?php endforeach; ?>
            </select><br><br>

            <label for="temperature">Temperature (°C):</label><br>
            <input type="number" name="temperature" step="1" required><br><br>

            <label for="moisture">Moisture (%):</label><br>
            <input type="number" name="moisture" step="1" required><br><br>

            <label for="light_level">Light Level (%):</label><br>
            <input type="number" name="light_level" step="1" required><br><br>

            <button type="submit">Add Sensor Data</button>
        </form>
    </div>
</div>

</body>
</html>
