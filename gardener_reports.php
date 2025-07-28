<?php
session_start();
include 'db_connect.php';
// Fetch plants and sensor data for this gardener
$query = "
    SELECT plants.id AS plant_id, plants.name AS plant_name, sensor_data.*
    FROM plants
    LEFT JOIN sensor_data ON plants.id = sensor_data.plant_id
    WHERE plants.gardener_username = ?
    ORDER BY sensor_data.timestamp DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $gardener_username);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Your Plant Reports</title>
</head>
<body>
    <h2>Report - All Your Plants & Sensor Data</h2>

    <a href="download_reports.php">Download CSV Report</a><br><br>

    <table border="1">
        <tr>
            <th>Plant Name</th>
            <th>Soil Moisture</th>
            <th>Temperature</th>
            <th>Humidity</th>
            <th>Timestamp</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= $row['soil_moisture'] ?></td>
            <td><?= $row['temperature'] ?></td>
            <td><?= $row['humidity'] ?></td>
            <td><?= $row['timestamp'] ?></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
