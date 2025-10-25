<?php
include('config.php');
include('navbar.php');

$booking_id = intval($_GET['booking_id'] ?? 0);
if (!$booking_id) { echo json_encode([]); exit; }
$res = $conn->query("SELECT lat, lng, updated_at FROM locations WHERE booking_id=$booking_id LIMIT 1");
if ($res && $r = $res->fetch_assoc()) echo json_encode($r);
else echo json_encode([]);
