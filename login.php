<?php
session_start();
include 'db_connect.php';

if (!isset($_POST['email']) || !isset($_POST['password'])) {
    echo "⚠ Email or password not received from form.";
    exit();
}

$email = $_POST['email'];
$password = $_POST['password'];

echo "📥 Submitted Email: $email<br>";
echo "🔒 Submitted Password: $password<br>";

// Step 1: Prepare and execute
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
if (!$stmt)
    die("❌ Prepare failed: " . $conn->error);
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Step 2: Check user result
if ($user) {
    echo "✅ User found: " . $user['email'] . "<br>";
    echo "🧠 Hashed password in DB: " . $user['password'] . "<br>";

    if (password_verify($password, $user['password'])) {
        echo "🔓 Password verified!<br>";

        if ($user['role'] === 'guest') {
            echo "❌ Guest users are not allowed to log in.";
            exit();
        }
        // Step 3: Set session variables
        $_SESSION['user_id'] = $user['id'];

        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];

        echo "✅ Redirecting to dashboard...<br>";
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
echo "❌ No user found with that email.";
}
?>