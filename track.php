<?php
include('config.php');
ensure_logged_in();
include('navbar.php');

$booking_id = intval($_GET['booking_id'] ?? 0);
if (!$booking_id) die('Booking ID required');
$booking = $conn->query("SELECT b.*, t.plate_number FROM bookings b JOIN trucks t ON t.id=b.truck_id WHERE b.id=$booking_id")->fetch_assoc();
$loc = $conn->query("SELECT lat,lng FROM locations WHERE booking_id=$booking_id")->fetch_assoc();
$gmKey = $_ENV['GOOGLE_MAPS_API_KEY'] ?? '';
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?=$gmKey?>"></script>
<title>Track Booking #<?= htmlspecialchars($booking_id) ?></title>
</head>
<body class="bg-gray-50">
<div class="max-w-4xl mx-auto p-4">
  <a href="client_dashboard.php" class="text-blue-600 underline mb-3 inline-block">← Back to dashboard</a>
  <h2 class="text-2xl mb-2">Tracking: <?=htmlspecialchars($booking['plate_number'])?></h2>
  <div id="map" style="height:60vh;" class="rounded shadow"></div>
</div>

<script>
let map, marker;
function initMap(lat=<?= $loc ? $loc['lat'] : '-1.2921' ?>, lng=<?= $loc ? $loc['lng'] : '36.8219' ?>) {
  map = new google.maps.Map(document.getElementById('map'), {center:{lat:parseFloat(lat),lng:parseFloat(lng)}, zoom:12});
  marker = new google.maps.Marker({position:{lat:parseFloat(lat),lng:parseFloat(lng)}, map:map, title:'Truck Location'});
}
initMap();

async function refresh() {
  const res = await fetch('get_location.php?booking_id=<?=$booking_id?>');
  const d = await res.json();
  if (d && d.lat && d.lng) {
    const pos = {lat: parseFloat(d.lat), lng: parseFloat(d.lng)};
    marker.setPosition(pos);
    map.setCenter(pos);
  }
}
setInterval(refresh, 8000);
</script>
</body>
</html>
