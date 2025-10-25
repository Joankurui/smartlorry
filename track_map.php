<?php
include('config.php');
ensure_logged_in();
include('navbar.php');


// Get truck and booking info
$truck_id = isset($_GET['truck_id']) ? (int)$_GET['truck_id'] : 0;
$truck = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM trucks WHERE id=$truck_id"));
if (!$truck) { die("Truck not found."); }

$booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM bookings WHERE truck_id=$truck_id ORDER BY id DESC LIMIT 1"));
if (!$booking) { die("No booking found for this truck."); }
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Track Truck: <?= htmlspecialchars($truck['plate_number']) ?></title>

  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

  <style>
    body { font-family: Arial; background: #f4f4f4; margin: 0; padding: 0; }
    #map { height: 90vh; width: 100%; margin: 0 auto; }
    h2 { text-align: center; padding: 15px; }
  </style>
</head>
<body>

<h2>Tracking Truck: <?= htmlspecialchars($truck['plate_number']) ?> 🚛</h2>
<div id="map"></div>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
let map = L.map('map').setView([-1.286389, 36.817223], 7); // default: Nairobi

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© OpenStreetMap contributors'
}).addTo(map);

let marker = null;

// Fetch location every 5 seconds
async function updateLocation() {
  const response = await fetch(`get_location.php?booking_id=<?= $booking['id'] ?>`);
  const data = await response.json();

  if (data.lat && data.lng) {
    const latlng = [parseFloat(data.lat), parseFloat(data.lng)];
    if (!marker) {
      marker = L.marker(latlng).addTo(map).bindPopup("Truck Location");
      map.setView(latlng, 8);
    } else {
      marker.setLatLng(latlng);
    }
  }
}
setInterval(updateLocation, 5000);
updateLocation();
</script>

</body>
</html>
