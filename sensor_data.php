<?php
session_start();
include('db_connect.php');

$msg = "";

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get plants for this gardener to populate dropdown
$plants = [];
$result = mysqli_query($conn, "SELECT id, name FROM plants WHERE id = $user_id");
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $plants[] = $row;
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $plant_id = $_POST['plant_id'] ?? '';
    $temperature = $_POST['temperature'] ?? '';
    $moisture = $_POST['moisture'] ?? '';
    $light_level = $_POST['light_level'] ?? '';

    // Validate
    if ($plant_id && is_numeric($temperature) && is_numeric($moisture) && is_numeric($light_level)) {
        $stmt = $conn->prepare("INSERT INTO sensor_data (id, plant_id, temperature, moisture, light_level) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iidd", $user_id, $plant_id, $temperature, $moisture, $light_level);

        if ($stmt->execute()) {
            $msg = "✅ Sensor data added successfully.";
        } else {
            $msg = "❌ Database error: " . $stmt->error;
        }
    } else {
        $msg = "❌ Please fill all fields correctly.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Sensor Data</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>

<!-- Navigation -->
<div class="topnav">
    <a href="gardener_dashboard.php">Dashboard</a>
    <a href="sensor_data.php">Add Sensor Data</a>
    <a href="logout.php" class="topnav-right">Logout</a>
</div>

<div class="main">
    <div class="content">
        <h2>Add Sensor Data</h2>

        <?php if (!empty($msg)): ?>
            <p><?= htmlspecialchars($msg) ?></p>
        <?php endif; ?>

        <form action="sensor_data.php" method="post">
            <label for="plant_id">Select Plant:</label>
            <select name="plant_id" required>
                <option value="">-- Choose a plant --</option>
                <?php foreach ($plants as $plant): ?>
                    <option value="<?= $plant['id'] ?>"><?= htmlspecialchars($plant['name']) ?></option>
                <?php endforeach; ?>
            </select><br><br>

            <label>Temperature (°C):</label>
            <input type="number" name="temperature" step="0.1" required><br><br>

            <label>Soil Moisture (%):</label>
            <input type="number" name="moisture" step="0.1" required><br><br>

            <label>Light Level (%):</label>
            <input type="number" name="light_level" step="0.1" required><br><br>

            <input type="submit" value="Add Sensor Data">
        </form>
    </div>
</div>

</body>
</html>
