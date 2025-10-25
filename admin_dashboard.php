<?php
include('config.php');
include('navbar.php');

ensure_logged_in();
if(!is_admin()){ echo 'Access denied'; exit; }
$bookings = mysqli_query($conn, "SELECT b.*, u.name AS client, t.plate_number FROM bookings b LEFT JOIN users u ON u.id=b.client_id LEFT JOIN trucks t ON t.id=b.truck_id ORDER BY b.booking_date DESC");
?>
<!doctype html><html><head><meta charset='utf-8'><title>Admin Dashboard</title><link rel='stylesheet' href='assets/css/style.css'></head><body>
<div class="container">
  <h2>Admin Dashboard</h2>
  <p>Welcome, <?=htmlspecialchars($_SESSION['user']['name'])?> | <a href="index.php">Home</a></p>

  <h3>Bookings</h3>
  <table>
    <tr><th>ID</th><th>Client</th><th>Truck</th><th>Origin</th><th>Destination</th><th>Status</th><th>Action</th></tr>
    <?php while($b=mysqli_fetch_assoc($bookings)): ?>
      <tr>
        <td><?=$b['id']?></td>
        <td><?=htmlspecialchars($b['client'])?></td>
        <td><?=htmlspecialchars($b['plate_number'])?></td>
        <td><?=htmlspecialchars($b['origin'])?></td>
        <td><?=htmlspecialchars($b['destination'])?></td>
        <td><?=$b['status']?></td>
        <td>
          <?php if($b['status']=='pending'): ?>
            <a href="approve_booking.php?id=<?=$b['id']?>&action=approve">Approve</a> |
            <a href="approve_booking.php?id=<?=$b['id']?>&action=cancel">Cancel</a>
          <?php elseif($b['status']=='approved'): ?>
            <a href="approve_booking.php?id=<?=$b['id']?>&action=start">Start Trip</a>
          <?php elseif($b['status']=='on_trip'): ?>
            <a href="track.php?booking_id=<?=$b['id']?>">Track</a>
          <?php else: ?>
            -
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>

  <h3>Trucks</h3>
  <table>
    <tr><th>ID</th><th>Plate</th><th>Model</th><th>Status</th></tr>
    <?php $tq=mysqli_query($conn,"SELECT * FROM trucks"); while($tr=mysqli_fetch_assoc($tq)): ?>
      <tr><td><?=$tr['id']?></td><td><?=htmlspecialchars($tr['plate_number'])?></td><td><?=htmlspecialchars($tr['model'])?></td><td><?=$tr['status']?></td></tr>
    <?php endwhile; ?>
  </table>

</div>
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script>
  Pusher.logToConsole = false;

  var pusher = new Pusher('YOUR_KEY', {
    cluster: 'YOUR_CLUSTER'
  });

  var channel = pusher.subscribe('smartlorry-channel');
  channel.bind('new-booking', function(data) {
    alert(data.message);
    location.reload(); // auto-refresh bookings table
  });
</script>
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script>
  Pusher.logToConsole = false;

  var pusher = new Pusher('YOUR_KEY', {
    cluster: 'YOUR_CLUSTER'
  });

  var channel = pusher.subscribe('smartlorry-channel');
  channel.bind('new-booking', function(data) {
    alert(data.message);
    location.reload(); // auto-refresh bookings table
  });
</script>
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script>
  Pusher.logToConsole = false;

  var pusher = new Pusher('YOUR_KEY', {
    cluster: 'YOUR_CLUSTER'
  });

  var channel = pusher.subscribe('smartlorry-channel');
  channel.bind('new-booking', function(data) {
    alert(data.message);
    location.reload(); // auto-refresh bookings table
  });
</script>
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script>
  Pusher.logToConsole = false;

  var pusher = new Pusher('YOUR_KEY', {
    cluster: 'YOUR_CLUSTER'
  });

  var channel = pusher.subscribe('smartlorry-channel');
  channel.bind('new-booking', function(data) {
    alert(data.message);
    location.reload(); // auto-refresh bookings table
  });
</script>
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script>
  Pusher.logToConsole = false;

  var pusher = new Pusher('YOUR_KEY', {
    cluster: 'YOUR_CLUSTER'
  });

  var channel = pusher.subscribe('smartlorry-channel');
  channel.bind('new-booking', function(data) {
    alert(data.message);
    location.reload(); // auto-refresh bookings table
  });
</script>

<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script>
  Pusher.logToConsole = false;

  var pusher = new Pusher('YOUR_KEY', {
    cluster: 'YOUR_CLUSTER'
  });

  var channel = pusher.subscribe('smartlorry-channel');
  channel.bind('new-booking', function(data) {
    alert(data.message);
    location.reload(); // auto-refresh bookings table
  });
</script>
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script>
  Pusher.logToConsole = false;

  var pusher = new Pusher('YOUR_KEY', {
    cluster: 'YOUR_CLUSTER'
  });

  var channel = pusher.subscribe('smartlorry-channel');
  channel.bind('new-booking', function(data) {
    alert(data.message);
    location.reload(); // auto-refresh bookings table
  });
</script>

</body>

</html>
