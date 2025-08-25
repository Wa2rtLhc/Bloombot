<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;
    // ✅ Step 1: Check for existing email or username
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // ✅ Friendly message instead of fatal error
        echo "<p style='color:red;'>⚠ Username or Email already exists. Please try another.</p>";
    } else {
        // ✅ Step 2: Try inserting new user
        $insert = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $insert->bind_param("ssss", $username, $email, $password, $role);

        if ($insert->execute()) {
            echo "<p style='color:green;'>✅ Registration successful! You can now <a href='login.php'>login</a>.</p>";
        } else {
            // ✅ Catch unexpected DB errors safely
            echo "<p style='color:red;'>❌ Something went wrong. Please try again later.</p>";
   }
    }

    $stmt->close();
    $conn->close();
}
?>