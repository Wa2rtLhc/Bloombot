<?php
session_start();
include('db_connect.php'); // your DB connection file

// Ensure gardener is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$gardener_username = $_SESSION['username'];

// Fetch plants for the logged-in gardener
$query = "SELECT * FROM plants WHERE gardener_username = ?";
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

        .plant h3 {
            margin: 0;
            color: #2c7a5d;
        }

        .plant p {
            margin: 5px 0;
        }

        .no-plants {
            text-align: center;
            padding: 30px;
            color: #888;
        }
    </style>
</head>
<body>
<div class="topnav">
    <a href="gardener_dashboard .php">Dashboard</a>  
    <a href="about.html">About</a>
    <a href="contact.html">Contact Us</a>   
    <a href="profile.php">My Profile</a>
    <a href="view_plants.php">My Plants</a>
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
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p><strong>Type:</strong> <?php echo htmlspecialchars($row['type']); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($row['status'] ?? 'Not Set'); ?></p>
                    <p><strong>Thresholds:</strong> 
                        Moisture: <?php echo $row['moisture_max'] ?? 'N/A'; ?>%, 
                        Temperature: <?php echo $row['temperature_max'] ?? 'N/A'; ?>°C
                    </p>
                    <a href="edit_plant.php?id=<?= $plant['id'] ?>">Edit</a> | 
                            <a href="delete_plant.php?id=<?= $plant['id'] ?>" onclick="return confirm('Delete this plant?')">Delete</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-plants">You have not added any plants yet.</div>
        <?php endif; ?>
    </div>
</body>
</html>
