<?php
session_start();
require_once 'db_connect.php'; // Replace with your actual DB connection file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$sql = "SELECT username, email, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - Bloombot</title>
    <link rel="stylesheet" href="style.css"> <!-- Use your existing CSS -->
    <style>
        .profile-container {
            max-width: 600px;
            margin: 60px auto;
            background: #fff;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }
        .profile-title {
            text-align: center;
            color: #2c7a5d;
            margin-bottom: 20px;
        }
        .profile-info {
            font-size: 18px;
            margin: 10px 0;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #2c7a5d;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="profile-container">
    <h2 class="profile-title">My Profile</h2>
    <div class="profile-info"><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></div>
    <div class="profile-info"><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></div>
    <div class="profile-info"><strong>Role:</strong>
        <span class="role-badge <?= $user['role'] ?>">
            <?= ucfirst($user['role']) ?>
        </span>
    </div>
    <a href="edit_profile.php" class="edit-button">Edit Profile</a>
    <div class="back-link">
        <a href="gardener_dashboard .php">← Back to Dashboard</a>
    </div>
</div>

</body>
</html>
