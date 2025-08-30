<?php
include 'db_connect.php';
$id = intval($_GET['id']);
$mysqli->query("UPDATE alerts SET status='read' WHERE id=$id");
?>