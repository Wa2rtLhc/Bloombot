<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? null;
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;

    if ($username && $email && $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'gardener')");
        $stmt->bind_param("sss", $username, $email, $hashedPassword);
        
        if ($stmt->execute()) {
            echo "✅ Sign up successful! You can now <a href='login.html'>log in</a>.";
        } else {
            echo "❌ Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "❗ Please fill in all fields.";
}
}
?>