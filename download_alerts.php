<?php
$mysqli = new mysqli("localhost", "root", "", "bloombot");

if ($mysqli->connect_error) {
    die("Database connection failed: " . $mysqli->connect_error);
}

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=alerts.csv');

$output = fopen('php://output', 'w');
fputcsv($output, ['Plant', 'Message', 'Timestamp']);

$query = "SELECT notifications.*, plants.name AS plant_name
          FROM notifications
          LEFT JOIN plants ON notifications.plant_id = plants.id
          ORDER BY notifications.timestamp DESC";

$result = $mysqli->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $plantName = $row['plant_name'] ?? "Plant #" . $row['plant_id'];
        fputcsv($output, [$plantName, $row['message'], $row['timestamp']]);
    }
}

fclose($output);
exit;
?>
