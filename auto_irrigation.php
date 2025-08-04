<?php
include('../config/db.php');
include('weather_api.php'); // your weather logic

// Get all plants + their latest sensor readings
$query = "SELECT s.plant_id, s.moisture, t.moisture_min, p.name, p.user_id
          FROM sensor_data s
          JOIN (
              SELECT plant_id, MAX(timestamp) AS latest
              FROM sensor_data GROUP BY plant_id
          ) latest ON s.plant_id = latest.plant_id AND s.timestamp = latest.latest
          JOIN thresholds t ON s.plant_id = t.plant_id
          JOIN plants p ON s.plant_id = p.plant_id";

$result = $conn->query($query);

// Fake weather API — replace with your actual check
$rainExpected = getRainStatus(); // returns true or false

while ($row = $result->fetch_assoc()) {
    $moisture = $row['moisture'];
    $moisture_min = $row['moisture_min'];
    $plant_id = $row['plant_id'];
    $plant_name = $row['name'];
    $user_id = $row['user_id'];

    if ($moisture < $moisture_min && !$rainExpected) {
        // 1. Log activity
        $stmt = $conn->prepare("INSERT INTO gardener_activities (user_id, plant_id, activity_type, timestamp)
                                VALUES (?, ?, 'Auto-Watered', NOW())");
        $stmt->bind_param("ii", $user_id, $plant_id);
        $stmt->execute();

        // 3. Notify gardener
        $message = "Auto-watering: Moisture was low for $plant_name. Irrigation triggered.";
        $stmt3 = $conn->prepare("INSERT INTO notifications (plant_id, message, timestamp, status)
                                 VALUES (?, ?, NOW(), 'unread')");
        $stmt3->bind_param("is", $plant_id, $message);
        $stmt3->execute();
    }
}
echo "Auto irrigation complete.";
?>
