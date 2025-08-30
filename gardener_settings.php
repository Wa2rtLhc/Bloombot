<?php
session_start();
include 'db_connect.php';

// Check if gardener is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'gardener') {
    header("Location: login.php?message=Please log in as gardener to access settings");
    exit();
}

$user_id = $_SESSION['user_id'];

// Load current settings
$settings = [
    'city' => 'Nairobi',
    'alert_type' => 'popup',
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

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $city = $_POST['city'] ?? 'Nairobi';
    $alert_type = $_POST['alert_type'] ?? 'popup';
    $theme = $_POST['theme'] ?? 'light';
    $units = $_POST['units'] ?? 'C';

    // Save settings (insert or update)
    $stmt = $conn->prepare("REPLACE INTO gardener_settings (user_id, city, alert_type, theme, units) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $city, $alert_type, $theme, $units);
    if ($stmt->execute()) {
        $message = "✅ Settings saved successfully!";
        $settings = ['city'=>$city,'alert_type'=>$alert_type,'theme'=>$theme,'units'=>$units];
        $testMessage = "This is a test alert to confirm your Bloombot settings.";
        switch ($alert_type) {
            case 'popup':
                $_SESSION['popup_alerts'][] = $testMessage;
                break;

            case 'email':
                if (!empty($gardener['email'])) {
                    mail($gardener['email'], "Bloombot Test Alert", $testMessage);
                }
                break;

            case 'sms':
                if (!empty($gardener['phone_number'])) {
                    // Mock SMS for testing
                    $mockMessage = "MOCK SMS to {$gardener['phone_number']}: $testMessage";
                    file_put_contents('mock_sms_log.txt', $mockMessage . PHP_EOL, FILE_APPEND);

                    // Uncomment below for real Twilio SMS
                    /*
                    try {
                        $twilio->messages->create(
                            $gardener['phone_number'],
                            ['from' => $twilioNumber, 'body' => $testMessage]
                        );
                    } catch (Exception $e) {
                        error_log("Twilio SMS failed: " . $e->getMessage());
                    }
                    */
                }
                break;
        }

    } else {
        $message = "❌ Failed to save settings. Try again.";
}
 }
?>