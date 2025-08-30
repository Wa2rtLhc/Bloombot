<?php
session_start();
include('db_connect.php'); 

// Ensure gardener is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$gardener_username = $_SESSION['username'];

// Fetch plants + thresholds + latest sensor values
$query = "
    SELECT p.id, p.name, p.type, p.status,
           t.moisture_min, t.moisture_max, t.temperature_min, t.temperature_max,
           s.temperature AS latest_temp, s.moisture AS latest_moisture, s.light_level, s.timestamp
    FROM plants p
    LEFT JOIN thresholds t ON p.id = t.plant_id
    LEFT JOIN (
        SELECT sd1.*
        FROM sensor_data sd1
        INNER JOIN (
            SELECT plant_id, MAX(timestamp) AS latest_time
            FROM sensor_data
            GROUP BY plant_id
        ) sd2
        ON sd1.plant_id = sd2.plant_id AND sd1.timestamp = sd2.latest_time
    ) s ON p.id = s.plant_id
    WHERE p.gardener_username = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $gardener_username);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Plants - Bloombot</title>
    <link rel="stylesheet" href="CSS/style.css">
    <style>
        .plant-list {
            max-width: 900px;
            margin: 40px auto;
            background: #f9f9f9;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .plant {
            background: white;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 5px solid #4CAF50;
            border-radius: 8px;
        }
        .plant h3 { margin: 0; color: #2c7a5d; }
        .plant p { margin: 5px 0; }
        .no-plants { text-align: center; padding: 30px; color: #888; }
    </style>
</head>
<body>
<div class="topnav">
    <a href="gardener_dashboard .php">Dashboard</a>  
    <a href="about.html">About</a>
    <a href="contact.html">Contact Us</a>   
    <a href="profile.php">My Profile</a>
    <a href="view_plants.php" class="active">My Plants</a>
    <div class="topnav-right">
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="header">
    <h1>My Plants</h1>
    <p>View all plants you're monitoring</p>
</div>

<div class="plant-list">
<?php if ($result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>
        <div class="plant">
            <h3><?= htmlspecialchars($row['name']) ?></h3>
            <p><strong>Type:</strong> <?= htmlspecialchars($row['type']) ?></p>
            <p><strong>Status:</strong> <?= !empty($row['status']) ? htmlspecialchars($row['status']) : 'Not Set' ?></p>
            
            <p><strong>Latest Sensor Data:</strong>
                Moisture: <?= $row['latest_moisture'] !== null ? $row['latest_moisture'] . "%" : "No data" ?>, 
                Temperature: <?= $row['latest_temp'] !== null ? $row['latest_temp'] . "°C" : "No data" ?>,
                Light: <?= $row['light_level'] !== null ? $row['light_level'] : "No data" ?>
            </p>

            <p><strong>Thresholds:</strong>
                Moisture: <?= $row['moisture_min'] ?>% - <?= $row['moisture_max'] ?>%, 
                Temperature: <?= $row['temperature_min'] ?>°C - <?= $row['temperature_max'] ?>°C
            </p>

            <a href="edit_plant.php?id=<?= $row['id'] ?>">Edit</a> | 
            <a href="delete_plant.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this plant?')">Delete</a>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <div class="no-plants">You have not added any plants yet.</div>
<?php endif; ?>
</div>
</body>
</html>
