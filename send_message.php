<?php
session_start();
include('db_connect.php');

// If the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL; // NULL if guest
    $email = trim($_POST['email']) ?: 'No Subject';
    $message = trim($_POST['message']);

    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO notifications (id, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $id, $email, $message);

        if ($stmt->execute()) {
            echo "success"; // you can handle this in contact.html (e.g., show a success alert)
        } else {
            echo "error"; // you can show an error alert
        }

        $stmt->close();
    } else {
        echo "empty"; // no message entered
    }
}
$conn->close();
?>
