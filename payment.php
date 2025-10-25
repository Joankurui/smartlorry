<?php
include('config.php');
include('navbar.php');

ensure_logged_in();

$booking_id = (int)($_GET['booking_id'] ?? 0);
$booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM bookings WHERE id=$booking_id"));
if(!$booking) die("Invalid booking");

if($_SERVER['REQUEST_METHOD']==='POST'){
    // Simulate payment success
    mysqli_query($conn, "UPDATE bookings SET payment_status='paid', status='active' WHERE id=$booking_id");
    echo "<script>alert('Payment successful!');window.location='client_dashboard.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Payment - SmartLorry</title>
<style>
body { font-family: Arial; margin: 40px; }
button { background: green; color: white; padding: 10px 20px; border: none; }
</style>
</head>
<body>
<h2>Confirm Payment</h2>
<p><b>Booking ID:</b> <?= $booking_id ?></p>
<p><b>Total Amount:</b> KSh <?= number_format($booking['payment_amount'], 2) ?></p>
<form method="post">
  <button type="submit">Simulate Payment (Pay Now)</button>
</form>
</body>
</html>
