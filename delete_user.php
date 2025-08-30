<?php
session_start();


// DB connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'bloombot.';

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user ID
$id = $_GET['id'] ?? '';
if (!$id) {
    die("Invalid user ID");
}

// Prevent admin from deleting themselves accidentally (optional)
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $id) {
    die("You can't delete yourself.");
}

// Delete user
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo "<script>alert('User deleted successfully!'); window.location.href='admin_dashboard.php';</script>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>