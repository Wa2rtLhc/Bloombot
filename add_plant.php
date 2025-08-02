<?php
session_start();

// Connect to MySQL
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'bloombot.';

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    die("Unauthorized. Please log in.");
}

$gardener_username = $_SESSION['username']; // Get username from session

// If the form is submitted
if (isset($_POST['submit'])) {
    // Get values safely
    $name = $_POST['name'] ?? '';
    $type = $_POST['type'] ?? '';
    $location = $_POST['location'] ?? '';
    $moisture_level = $_POST['moisture_level'] ?? '';
    $temperature = $_POST['temperature'] ?? '';
    $light_requirement = $_POST['light_requirement'] ?? '';
    $planted_date = $_POST['planted_date'] ?? '';
    $status = $_POST['status'] ?? '';
    $notes = $_POST['notes'] ?? '';

    if (
        !empty($name) && !empty($type) && !empty($location) &&
        !empty($moisture_level) && !empty($temperature) &&
        !empty($light_requirement) && !empty($planted_date) &&
        !empty($status)
    ) {
        // Prepare SQL with gardener_username included
        $stmt = $conn->prepare("INSERT INTO plants (name, type, location, moisture_level, temperature, light_requirement, planted_date, status, notes, gardener_username)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("ssssssssss", $name, $type, $location, $moisture_level, $temperature, $light_requirement, $planted_date, $status, $notes, $gardener_username);

        if ($stmt->execute()) {
            echo "<script>alert('Plant added successfully!'); window.location.href='gardener_dashboard .php';</script>";
        } else {
            echo "Error adding plant: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "<script>alert('Please fill all required fields');</script>";
    }
}

$conn->close();
?>

<!-- HTML FORM -->
<!DOCTYPE html>
<html>
<head>
    <title>Add Plant</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>
    <div class="container">
        <h2>Add New Plant</h2>
        <form method="POST" action="add_plant.php">
            <label>Plant Name</label>
            <input type="text" name="name" required>

            <label>Plant Type</label>
            <input type="text" name="type" required>

            <label>Location</label>
            <input type="text" name="location" required>

            <label>Moisture Level</label>
            <input type="number" name="moisture_level" required>

            <label>Temperature</label>
            <input type="number" name="temperature" required>

            <label>Light Requirement</label>
            <input type="text" name="light_requirement" required>

            <label>Planted Date</label>
            <input type="date" name="planted_date" required>

            <label>Status</label>
            <input type="text" name="status" required>

            <label>Notes</label>
            <textarea name="notes" rows="4"></textarea>

            <button type="submit" name="submit">Add Plant</button>
        </form>
    </div>
</body>
</html>
