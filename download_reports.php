<?php
session_start();
include 'db_connect.php';
// Fetch data
$query = "
    SELECT plants.name AS plant_name, sensor_data.*
    FROM plants
    LEFT JOIN sensor_data ON plants.id = sensor_data.plant_id
    WHERE plants.gardener_username = ?
    ORDER BY sensor_data.timestamp DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $gardener_username);
$stmt->execute();
$result = $stmt->get_result();

// Headers for CSV
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="gardener_report.csv"');

// Output CSV
$output = fopen('php://output', 'w');
fputcsv($output, ['Plant Name', 'Soil Moisture', 'Temperature', 'Humidity', 'Timestamp']);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['plant_name'],
        $row['soil_moisture'],
        $row['temperature'],
        $row['humidity'],
        $row['timestamp']
    ]);
}

fclose($output);
exit;
