<?php
session_start();

// --- 1. Database Connection ---
$host = "localhost";       // usually localhost
$db_user = "root";         // your DB username
$db_pass = "";             // your DB password
$db_name = "bloombot.";     // your database name

$mysqli = new mysqli($host, $db_user, $db_pass, $db_name);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// --- 2. Admin Access Check ---
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php?message=Please login as admin");
    exit();
}

// --- 3. Get Form Data ---
$username = trim($_POST['username']);
$email = trim($_POST['email']);
$password = trim($_POST['password']);
$role = $_POST['role'];

// --- 4. Validate Input ---
if(empty($username) || empty($email) || empty($password) || empty($role)){
    die("All fields are required.");
}

// --- 5. Hash Password ---
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// --- 6. Insert User ---
$stmt = $mysqli->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
if(!$stmt){
    die("Prepare failed: " . $mysqli->error);
}

$stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

if($stmt->execute()){
    echo "User added successfully. <a href='admin_dashboard.php'>Back to Dashboard</a>";
}else{
    if($mysqli->errno == 1062){
        echo "Error: Username or email already exists.";
    } else {
        echo "Error: " . $mysqli->error;
    }
}

// --- 7. Close Statements and Connection ---
$stmt->close();
$mysqli->close();
?>
