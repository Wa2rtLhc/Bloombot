<?php
session_start();
include 'db_connect.php'; // your database connection

// Show messages (like session expired)
if (!empty($_GET['message'])) {
    echo '<div class="alert">'.htmlspecialchars($_GET['message']).'</div>';
}

// Only run if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Step 1: Prepare SQL to prevent SQL injection
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("❌ Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Step 2: Check user exists
    if ($user) {
        // Make sure password is hashed in DB
        if (password_verify($password, trim($user['password']))) {

            if ($user['role'] === 'guest') {
                echo "❌ Guest users are not allowed to log in.";
                exit();
            }

            // Step 3: Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['last_activity'] = time(); // for session timeout

            // Step 4: Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($user['role'] === 'gardener') {
                header("Location: gardener_dashboard .php");
            } else {
                echo "⚠ Unknown role. Contact admin.";
            }
            exit();
        } else {
            echo "❌ Password does not match.";
        }
    } else {
        echo "❌ No user found with that email.";
  }
}
?>
