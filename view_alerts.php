<?php
session_start();

// Check if user is logged in and is a gardener
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'gardener') {
    header("Location: login.html");
    exit;
}

// Connect to DB
$conn = new mysqli("localhost", "root", "", "bloombot.");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];
$alerts = [];

// Prepare query safely
$query = "SELECT message, seen, plants.name AS plant_name 
          FROM notifications  
          JOIN plants ON notifications.plant_id = plants.id 
          WHERE plants.gardener_username = ? 
          ORDER BY seen DESC";

$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $alerts[] = $row;
    }
    $stmt->close();
} else {
    die("Failed to prepare SQL statement.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Plant Alerts</title>
    <link rel="stylesheet" href="CSS/style.css">
    <style>
        body {
            background-image: url('bg.jpg');
            background-size: cover;
            font-family: Arial, sans-serif;
            color: white;
            padding: 40px;
            text-align: center;
        }
        .container {
            background-color: rgba(0,0,0,0.7);
            padding: 30px;
            border-radius: 15px;
            max-width: 800px;
            margin: auto;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            color: white;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }
        a {
            color: #ffeb3b;
            text-decoration: none;
            display: block;
            margin-top: 20px;
        }
        h2 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Plant Alerts</h2>
    <?php if (empty($alerts)): ?>
        <p>No alerts found for your plants.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Plant</th>
                    <th>Alert Message</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($alerts as $alert): ?>
                    <tr>
                        <td><?= htmlspecialchars($alert['plant_name']) ?></td>
                        <td><?= htmlspecialchars($alert['message']) ?></td>
                        <td><?= htmlspecialchars($alert['timestamp']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <a href="gardener_dashboard .php">← Back to Dashboard</a>
</div>
</body>
</html>