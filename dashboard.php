<?php
include('config.php');
include('functions.php');
ensure_logged_in();
include('navbar.php');

$uid = $_SESSION['user']['id'];

// fetch available trucks
$trucks = mysqli_query($conn, "SELECT * FROM trucks WHERE status='available'");
?>
<!doctype html>
<html>
<head>
<meta charset='utf-8'>
<title>Client Dashboard</title>
<link rel='stylesheet' href='assets/css/style.css'>
<style>
body { font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px; }
table { border-collapse: collapse; width: 100%; background: #fff; }
th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
th { background: #2d89ef; color: white; }
h2, h3 { color: #2d89ef; }
a { color: #2d89ef; text-decoration: none; }
a:hover { text-decoration: underline; }
.notification {
    background: #ffefc5;
    border: 1px solid #ffc107;
    padding: 10px;
    margin-bottom: 15px;
    color: #333;
    display: none;
}
button { background: #2d89ef; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; }
button:hover { background: #1b5fab; }
input[type="text"] { padding: 5px; border: 1px solid #ccc; border-radius: 4px; }
</style>
</head>

<body>
<div class="container">
  <h2>Client Dashboard</h2>
  <p>Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?> | 
  <a href="logout.php">Logout</a></p>

  <!-- Notification area -->
  <div id="notification" class="notification"></div>

  <h3>Available Lorries</h3>
  <table>
    <tr><th>Plate</th><th>Model</th><th>Capacity</th><th>Cost/km</th><th>Action</th></tr>
    <?php while ($t = mysqli_fetch_assoc($trucks)): ?>
      <tr>
        <td><?= htmlspecialchars($t['plate_number']) ?></td>
        <td><?= htmlspecialchars($t['model']) ?></td>
        <td><?= htmlspecialchars($t['capacity']) ?></td>
        <td><?= htmlspecialchars($t['cost_per_km']) ?></td>
        <td><a href="booking_form.php?truck_id=<?= $t['id'] ?>">Book</a></td>
      </tr>
    <?php endwhile; ?>
  </table>

  <h3>Your Bookings</h3>
  <?php
  $bq = mysqli_query($conn, "SELECT b.*, t.plate_number 
                             FROM bookings b 
                             LEFT JOIN trucks t ON t.id = b.truck_id 
                             WHERE b.client_id = $uid 
                             ORDER BY b.booking_date DESC");

  if (mysqli_num_rows($bq) == 0): ?>
    <p>No bookings yet.</p>
  <?php else: ?>
    <table>
      <tr>
        <th>ID</th>
        <th>Truck</th>
        <th>Origin</th>
        <th>Destination</th>
        <th>Status</th>
        <th>Payment</th>
        <th>Track</th>
      </tr>
      <?php while ($b = mysqli_fetch_assoc($bq)): ?>
        <tr>
          <td><?= htmlspecialchars($b['id']) ?></td>
          <td><?= htmlspecialchars($b['plate_number']) ?></td>
          <td><?= htmlspecialchars($b['origin']) ?></td>
          <td><?= htmlspecialchars($b['destination']) ?></td>
          <td><?= htmlspecialchars($b['status']) ?></td>
          <td>
            <?php if ($b['payment_status'] == 'unpaid'): ?>
              <form method="POST" action="initiate_payment.php" style="display:inline;">
                <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                <input type="hidden" name="amount" value="<?= $b['payment_amount'] ?>">
                <input type="text" name="phone" placeholder="2547..." required>
                <button type="submit">Pay Now</button>
              </form>
            <?php else: ?>
              ✅ Paid
            <?php endif; ?>
          </td>
          <td>
            <?php if (in_array($b['status'], ['on_trip', 'approved'])): ?>
              <a href="track.php?booking_id=<?= $b['id'] ?>">Track</a>
            <?php else: ?>
              -
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php endif; ?>
</div>

<!-- ✅ Real-time Notifications -->
<script>
async function checkNotifications() {
  try {
    const res = await fetch('check_notifications.php');
    const msg = await res.text();
    if (msg.trim() && msg !== 'none') {
      const box = document.getElementById('notification');
      box.innerHTML = msg;
      box.style.display = 'block';
      setTimeout(() => { box.style.display = 'none'; }, 8000);
    }
  } catch (err) {
    console.error('Notification error', err);
  }
}
setInterval(checkNotifications, 10000);
</script>
</body>
</html>
