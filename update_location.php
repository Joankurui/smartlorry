<?php
include('config.php');
// driver posts booking_id, lat, lng
if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;
$booking_id = intval($_POST['booking_id'] ?? 0);
$lat = floatval($_POST['lat'] ?? 0);
$lng = floatval($_POST['lng'] ?? 0);
if (!$booking_id || !$lat || !$lng) { echo 'Missing'; exit; }
$stmt = $conn->prepare("INSERT INTO locations (booking_id, lat, lng, updated_at) VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE lat=VALUES(lat), lng=VALUES(lng), updated_at=NOW()");
$stmt->bind_param("idd", $booking_id, $lat, $lng);
$stmt->execute();
echo 'OK';
