<?php
$mysqli = new mysqli("localhost", "root", "", "bloombot.");

if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

$query = "SELECT notifications.*, plants.name AS plant_name
          FROM notifications
          LEFT JOIN plants ON notifications.plant_id = plants.id
          ORDER BY notifications.timestamp DESC";

$result = $mysqli->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Alerts - Bloombot</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f9f9f9;
        }
        h2 {
            margin-bottom: 20px;
        }
        .btn {
            text-decoration: none;
            background-color: #2196F3;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            display: inline-block;
        }
        .btn:hover {
            background-color: #1976D2;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background-color: white;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>

<a href="gardener_dashboard .php" class="btn">← Back to Dashboard</a>
<a href="download_alerts.php" class="btn" style="background-color: #4CAF50;">⬇ Download CSV</a>

<h2>All Alerts</h2>

<table>
    <thead>
        <tr>
            <th>Plant</th>
            <th>Message</th>
            <th>Timestamp</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['plant_name'] ?? "Plant #" . $row['plant_id']) ?></td>
                    <td><?= htmlspecialchars($row['message']) ?></td>
                    <td><?= htmlspecialchars($row['timestamp']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="3">No alerts found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
