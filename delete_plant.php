<?php
// DB connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'bloombot.';
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'] ?? '';
if (!$id) {
    die("Invalid plant ID");
}

$stmt = $conn->prepare("DELETE FROM plants WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>alert('Plant deleted successfully!'); window.location.href='admin_dashboard.php';</script>";
} else {
    echo "Error deleting plant: " . $stmt->error;
}
?>
