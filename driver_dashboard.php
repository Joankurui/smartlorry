<?php
include('config.php');
include('navbar.php');

ensure_logged_in();
if ($_SESSION['user']['role'] !== 'driver') { header('Location: login.php'); exit; }
$driver_id = (int)$_SESSION['user']['id'];
$trucks = $conn->query("SELECT * FROM trucks WHERE driver_id = $driver_id");
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="assets/css/style.css">
<title>Driver Dashboard</title></head>
<body class="bg-gray-50">
<div class="max-w-4xl mx-auto p-6">
  <h1 class="text-2xl font-semibold mb-4">Driver Dashboard</h1>
  <p class="mb-4">Welcome, <?=htmlspecialchars($_SESSION['user']['name'])?></p>

  <?php while($t = $trucks->fetch_assoc()): ?>
    <div class="bg-white p-4 rounded shadow mb-4">
      <div class="flex justify-between items-center">
        <div>
          <div class="font-bold"><?=htmlspecialchars($t['plate_number'])?></div>
          <div class="text-sm text-gray-600"><?=htmlspecialchars($t['model'])?></div>
        </div>
        <div>
          <button onclick="startTracking(<?= $t['id'] ?>)" class="bg-green-600 text-white px-3 py-1 rounded">Start Live</button>
          <button onclick="stopTracking()" class="bg-red-600 text-white px-3 py-1 rounded ml-2">Stop</button>
        </div>
      </div>
      <div id="status-<?=$t['id']?>" class="mt-3 text-sm text-gray-600">Status: idle</div>
    </div>
  <?php endwhile; ?>
</div>

<script>
let watchId = null;
let currentBookingId = null;

function startTracking(truckId) {
  fetch('get_active_booking.php?truck_id=' + truckId)
    .then(r => r.json())
    .then(b => {
      if (!b || !b.id) { alert('No active booking for this truck'); return; }
      currentBookingId = b.id;
      if (!navigator.geolocation) { alert('Geolocation not supported'); return; }
      if (watchId) navigator.geolocation.clearWatch(watchId);
      watchId = navigator.geolocation.watchPosition(pos => {
        const lat = pos.coords.latitude;
        const lng = pos.coords.longitude;
        fetch('update_location.php', {
          method: 'POST',
          headers: {'Content-Type':'application/x-www-form-urlencoded'},
          body: new URLSearchParams({booking_id: currentBookingId, lat, lng})
        }).then(r => r.text()).then(txt => {
          document.getElementById('status-'+truckId).innerText = 'Location sent at ' + new Date().toLocaleTimeString();
        }).catch(err => console.error(err));
      }, err => alert('GPS error: ' + err.message), {enableHighAccuracy:true, maximumAge:5000});
    });
}

function stopTracking(){
  if (watchId) navigator.geolocation.clearWatch(watchId);
  watchId = null;
  currentBookingId = null;
  alert('Tracking stopped');
}
</script>
</body>
</html>
