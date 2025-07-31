<?php
$mysqli = new mysqli("localhost", "root", "", "bloombot.");

if ($mysqli->connect_error) {
    echo json_encode(["status" => "error", "message" => "DB error"]);
    exit;
}

$result = $mysqli->query("SELECT id FROM plants");

while ($row = $result->fetch_assoc()) {
    $plantId = $row['id'];
    $temperature = rand(18, 35);
    $moisture = rand(30, 80);
    $light = rand(1000, 8000);

    $stmt = $mysqli->prepare("INSERT INTO sensor_data (plant_id, temperature, moisture, light_level) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiii", $plantId, $temperature, $moisture, $light);
    $stmt->execute();
}

echo json_encode(["status" => "success", "message" => "Sensor data added"]);
?>

