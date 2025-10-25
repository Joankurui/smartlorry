<?php
include('config.php');
include('navbar.php');

$booking_id = $_GET['booking_id'] ?? null;
if(!$booking_id) die('Booking ID missing');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Track My Lorry</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY"></script>
</head>
<body>
<h2>Live Truck Tracking</h2>
<div id="map" style="width:100%;height:500px;"></div>

<script>
let map, marker;

function initMap(lat = -1.2921, lng = 36.8219) {
  map = new google.maps.Map(document.getElementById("map"), {
    zoom: 8,
    center: { lat, lng },
  });
  marker = new google.maps.Marker({ position: { lat, lng }, map });
}

async function updateLocation() {
  const res = await fetch("get_location.php?booking_id=<?= $booking_id ?>");
  const data = await res.json();
  if (data.lat && data.lng) {
    const pos = { lat: parseFloat(data.lat), lng: parseFloat(data.lng) };
    marker.setPosition(pos);
    map.setCenter(pos);
  }
}

initMap();
setInterval(updateLocation, 5000); // refresh every 5s
</script>
</body>
</html>
