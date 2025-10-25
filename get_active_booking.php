<?php
include('config.php');
include('navbar.php');

ensure_logged_in();
$truck_id = intval($_GET['truck_id'] ?? 0);
if (!$truck_id) { echo json_encode([]); exit; }
$res = $conn->query("SELECT * FROM bookings WHERE truck_id=$truck_id AND status IN ('approved','on_trip','in_transit') ORDER BY id DESC LIMIT 1");
if ($res && $r = $res->fetch_assoc()) echo json_encode($r);
else echo json_encode([]);
