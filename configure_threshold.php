<?php
// DB connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'bloombot';
$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Update logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST['min_value'] as $id => $minVal) {
        $maxVal = $_POST['max_value'][$id];
        $stmt = $conn->prepare("UPDATE thresholds SET min_value = ?, max_value = ? WHERE id = ?");
        $stmt->bind_param("ddi", $minVal, $maxVal, $id);
        $stmt->execute();
    }
    echo "<script>alert('Thresholds updated!'); window.location.href='admin_dashboard.php';</script>";
    exit;
}

// Fetch thresholds
$result = $conn->query("SELECT * FROM thresholds");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Configure Global Thresholds</title>
</head>
<body>
    <h2>Configure Global Thresholds</h2>
    <form method="POST">
        <table border="1">
            <tr>
                <th>Parameter</th>
                <th>Min Value</th>
                <th>Max Value</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['parameter']) ?></td>
                <td><input type="number" step="0.1" name="min_value[<?= $row['id'] ?>]" value="<?= $row['min_value'] ?>" required></td>
                <td><input type="number" step="0.1" name="max_value[<?= $row['id'] ?>]" value="<?= $row['max_value'] ?>" required></td>
            </tr>
            <?php } ?>
        </table>
        <br>
        <button type="submit">Update Thresholds</button>
    </form>
</body>
</html>
