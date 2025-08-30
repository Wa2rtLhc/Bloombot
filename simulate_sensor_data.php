<?php
session_start();
include 'db_connect.php';

// Check if gardener is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'gardener') {
    header("Location: login.php?message=Please log in as gardener to access settings");
    exit();
}

$user_id = $_SESSION['user_id'];

// Load gardener settings
$settings = [
    'city' => 'Nairobi',
    'alert_type' => 'popup', // popup, email, sms
    'theme' => 'light',
    'units' => 'C'
];

$stmt = $conn->prepare("SELECT * FROM gardener_settings WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
    $settings = $result->fetch_assoc();
}
// Fetch gardener info (email & phone)
$stmtUser = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmtUser->bind_param("i", $user_id);
$stmtUser->execute();
$gardener = $stmtUser->get_result()->fetch_assoc();

// Fetch all plants
$plantsResult = $conn->query("SELECT * FROM plants");

while ($plant = $plantsResult->fetch_assoc()) {
    $plant_id = $plant['id'];

    // Simulate sensor data
    $temperature = rand(18, 35);
    $moisture = rand(30, 80);
    $light_level = rand(1000, 8000);

    // Insert sensor data
    $stmtInsert = $conn->prepare(
        "INSERT INTO sensor_data (plant_id, temperature, moisture, light_level) VALUES (?, ?, ?, ?)"
    );
    $stmtInsert->bind_param("iiii", $plant_id, $temperature, $moisture, $light_level);
    $stmtInsert->execute();
    
    // Fetch thresholds (gardener first, then global)
    $thresholdQuery = $conn->prepare("SELECT * FROM thresholds WHERE plant_id = ? LIMIT 1");
    $thresholdQuery->bind_param("i", $plant_id);
    $thresholdQuery->execute();
    $thresholdResult = $thresholdQuery->get_result();

    if ($thresholdResult && $thresholdResult->num_rows > 0) {
        $thresholds = $thresholdResult->fetch_assoc();
    } else {
        $globalResult = $conn->query("SELECT * FROM global_thresholds LIMIT 1");
        $thresholds = $globalResult ? $globalResult->fetch_assoc() : null;
    }

    if (!$thresholds) continue; // skip if no thresholds

    // Check sensor values
    $alerts = [];
    if ($temperature < $thresholds['temperature_min']) $alerts[] = "Temperature too low for Plant #$plant_name.Consider moving your plant indoors or covering it with a cloth to retain warmth";
    if ($temperature > $thresholds['temperature_max']) $alerts[] = "Temperature too high for Plant #$plant_name.Consider moving your plant to a cooler, shaded area to prevent heat stress.";
    if ($moisture < $thresholds['moisture_min']) $alerts[] = "Moisture too low for Plant #$plant_name.Water your plant thoroughly, ensuring water reaches the root zone.";
    if ($moisture > $thresholds['moisture_max']) $alerts[] = "Moisture too high for Plant #$plant_name.Check soil drainage and reduce watering frequency to prevent root rot.";
    if ($light_level < $thresholds['light_min']) $alerts[] = "Light too low for Plant #$plant_name.Move your plant to a brighter location or consider using a grow light.";
    if ($light_level > $thresholds['light_max']) $alerts[] = "Light too high for Plant #$plant_name.Provide shade during peak sunlight hours to prevent leaf burn.";

    // Insert alerts and send notifications
    foreach ($alerts as $message) {
        // Avoid duplicates in last 10 minutes
        $checkDup = $conn->prepare(
            "SELECT * FROM notifications WHERE plant_id=? AND message=? AND timestamp >= NOW() - INTERVAL 10 MINUTE"
        );
        $checkDup->bind_param("is", $plant_id, $message);
        $checkDup->execute();
        $dupResult = $checkDup->get_result();

        if ($dupResult->num_rows === 0) {
            // Insert notification
            $stmtNotif = $conn->prepare(
                "INSERT INTO notifications (plant_id, message, timestamp) VALUES (?, ?, NOW())"
            );
            $stmtNotif->bind_param("is", $plant_id, $message);
            $stmtNotif->execute();

            // Send alert based on settings
            switch ($settings['alert_type']) {
                case 'popup':
                    $_SESSION['popup_alerts'][] = $message;
                    break;

                case 'email':
                    if (!empty($gardener['email'])) {
                        mail($gardener['email'], "Bloombot Alert", $message);
                    }
                    break;

                case 'sms':
                    if (!empty($gardener['phone_number'])) {
                        // Mock SMS for testing
                        $mockMessage = "MOCK SMS to {$gardener['phone_number']}: $message";
                        file_put_contents('mock_sms_log.txt', $mockMessage . PHP_EOL, FILE_APPEND);
                    }
                    break;
            }
        }
    }
}

// Return JSON response if needed
echo json_encode(["status" => "success", "message" => "Sensor data simulated and alerts processed."]);
?>
