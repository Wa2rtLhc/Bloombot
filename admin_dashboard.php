<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit;
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Bloombot</title>
    <link rel="stylesheet" href="CSS/style.css">
    <style>
        body {
            background-image: url('bg.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            font-family: Arial, sans-serif;
            color: white;
            margin: 0;
            padding: 0;
        }

        .dashboard-container {
            background-color: rgba(0, 0, 0, 0.7);
            max-width: 1000px;
            margin: 80px auto;
            padding: 40px;
            border-radius: 20px;
            text-align: center;
        }

        h1 {
            color: #ffeb3b;
            margin-bottom: 30px;
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 25px;
        }

        .card {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
            transition: 0.3s;
        }

        .card:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: scale(1.05);
        }

        .card a {
            text-decoration: none;
            color: #ffffff;
            font-size: 18px;
            font-weight: bold;
        }

        .logout {
            margin-top: 30px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #e53935;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }

        .logout:hover {
            background-color: #c62828;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Welcome Admin, <?php echo htmlspecialchars($username); ?> 👨‍💼</h1>

        <div class="card-grid">
            <div class="card"><a href="view_users.php">Manage Users</a></div>
            <div class="card"><a href="view_all_plants.php">View All Plants</a></div>
            <div class="card"><a href="view_alerts.php">View System Alerts</a></div>
            <div class="card"><a href="system_logs.php">View System Logs</a></div>
            <div class="card"><a href="about.html">About Bloombot</a></div>
        </div>

        <a href="logout.php" class="logout">Logout</a>
    </div>
</body>
</html>
