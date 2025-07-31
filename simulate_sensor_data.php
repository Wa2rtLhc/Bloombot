<?php
$mysqli = new mysqli("localhost", "root", "", "bloombot.");

if ($mysqli->connect_error) {
    echo json_encode(["status" => "error", "message" => "DB error"]);
    exit;
}

$result = $mysqli->query("SELECT id FROM plants");

while ($row = $result->fetch_assoc()) {
    $plant_id = $row['id'];
    $temperature = rand(18, 35);
    $moisture = rand(30, 80);
    $light_level = rand(1000, 8000);

    // 1. Insert sensor data
    $stmt = $mysqli->prepare("INSERT INTO sensor_data (plant_id, temperature, moisture, light_level) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiii", $plant_id, $temperature, $moisture, $light_level);
    $stmt->execute();

    // 2. Fetch thresholds for the plant
    $thresholdQuery = "SELECT * FROM thresholds WHERE plant_id = $plant_id LIMIT 1";
    $thresholdResult = $mysqli->query($thresholdQuery);

    if ($thresholdResult && $thresholdResult->num_rows > 0) {
        $thresholds = $thresholdResult->fetch_assoc();

        $alerts = [];

        // Temperature checks
        if ($temperature < $thresholds['temperature_min']) {
            $alerts[] = "Temperature too low for Plant #$plant_id.";
        } elseif ($temperature > $thresholds['temperature_max']) {
            $alerts[] = "Temperature too high for Plant #$plant_id.";
        }

        // Moisture checks
        if ($moisture < $thresholds['moisture_min']) {
            $alerts[] = "Moisture too low for Plant #$plant_id.";
        } elseif ($moisture > $thresholds['moisture_max']) {
            $alerts[] = "Moisture too high for Plant #$plant_id.";
        }

        // Light level checks
        if ($light_level < $thresholds['light_min']) {
            $alerts[] = "Light level too low for Plant #$plant_id.";
        } elseif ($light_level > $thresholds['light_max']) {
            $alerts[] = "Light level too high for Plant #$plant_id.";
        }

        // 3. Insert alerts only if not repeated in last 10 minutes
        foreach ($alerts as $message) {
            $checkDuplicate = "SELECT * FROM notifications 
                               WHERE plant_id = $plant_id 
                               AND message = '$message' 
                               AND timestamp >= NOW() - INTERVAL 10 MINUTE";
            $duplicateResult = $mysqli->query($checkDuplicate);
            if ($duplicateResult->num_rows === 0) {
                $insertAlertQuery = "INSERT INTO notifications (plant_id, message, timestamp)
                                     VALUES ($plant_id, '$message', NOW())";
                $mysqli->query($insertAlertQuery);
            }
        }
    }
}

echo json_encode(["status" => "success", "message" => "Sensor data added"]);
?>
