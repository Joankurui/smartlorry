<?php
include('config.php');
include('navbar.php');

ensure_logged_in();
$uid = $_SESSION['user']['id'];
$notifications = [];

// bookings
$bkQ = $conn->query("SELECT id,status,updated_at FROM bookings WHERE client_id=$uid AND updated_at > NOW() - INTERVAL 30 SECOND");
while($b = $bkQ->fetch_assoc()){
  $notifications[] = ['type'=>'booking','message'=>"Booking #{$b['id']} updated: {$b['status']}", 'time'=>$b['updated_at']];
}

// payments
$payQ = $conn->query("SELECT p.booking_id, p.status, p.amount, p.updated_at FROM payments p JOIN bookings b ON b.id=p.booking_id WHERE b.client_id=$uid AND p.updated_at > NOW() - INTERVAL 30 SECOND");
while($p = $payQ->fetch_assoc()){
  $msg = $p['status']==='successful' ? "Payment of KES {$p['amount']} for Booking #{$p['booking_id']} received" : "Payment update for Booking #{$p['booking_id']}: {$p['status']}";
  $notifications[] = ['type'=>'payment','message'=>$msg,'time'=>$p['updated_at']];
}

if (count($notifications)) echo json_encode(['type'=>'update','data'=>$notifications]);
else echo json_encode(['type'=>'none']);
