<?php
include('config.php');
include('navbar.php');

if (!isset($_GET['booking_id'])) exit;
$id = (int)$_GET['booking_id'];
$loc = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM locations WHERE booking_id=$id ORDER BY updated_at DESC LIMIT 1"));
echo json_encode($loc ?: []);
?>
