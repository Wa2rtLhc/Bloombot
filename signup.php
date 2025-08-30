<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    $role = $_POST['role'] ?? 'gardener'; // Default role

    // 1. Check for existing email or username
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<p style='color:red;'>⚠ Username or Email already exists. Please try another.</p>";
    } else {
        // 2. Hash the password before saving
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $phone_number = $_POST['phone_number'] ?? null;

$insert = $conn->prepare("INSERT INTO users (username, email, password, role, phone_number) VALUES (?, ?, ?, ?, ?)");
$insert->bind_param("sssss", $username, $email, $hashed_password, $role, $phone_number);

        if ($insert->execute()) {
            echo "<p style='color:green;'>✅ Registration successful! You can now <a href='login.html'>login</a>.</p>";
        } else {
            echo "<p style='color:red;'>❌ Something went wrong. Please try again later.</p>";
        }

        $insert->close();
    }

    $stmt->close();
    $conn->close();
}
?>
