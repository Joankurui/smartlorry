<?php
// payment_callback.php
include('config.php');
include('navbar.php');

$input = file_get_contents('php://input');
file_put_contents('mpesa_callback_log.json', $input . PHP_EOL, FILE_APPEND);

$data = json_decode($input, true);
if (!isset($data['Body']['stkCallback'])) { http_response_code(400); exit; }
$cb = $data['Body']['stkCallback'];
$resultCode = $cb['ResultCode'];
$meta = $cb['CallbackMetadata']['Item'] ?? [];

$amount = null; $mpesaReceipt = null; $accRef = null;
foreach ($meta as $item) {
  if ($item['Name'] === 'Amount') $amount = $item['Value'];
  if ($item['Name'] === 'MpesaReceiptNumber') $mpesaReceipt = $item['Value'];
  if ($item['Name'] === 'BillRefNumber' || $item['Name']==='AccountReference') $accRef = $item['Value'];
}

$booking_id = null;
if ($accRef && preg_match('/SmartLorry-(\d+)/',$accRef,$m)) $booking_id = intval($m[1]);

if (!$booking_id && $amount) {
  $res = $conn->query("SELECT * FROM payments WHERE amount = $amount AND status='initiated' ORDER BY created_at DESC LIMIT 1");
  if ($res && $r = $res->fetch_assoc()) $booking_id = $r['booking_id'];
}

if ($booking_id) {
  if ($resultCode === 0) {
    $stmt = $conn->prepare("UPDATE payments SET status='successful', mpesa_receipt=? WHERE booking_id=?");
    $stmt->bind_param("si", $mpesaReceipt, $booking_id);
    $stmt->execute();
    $stmt2 = $conn->prepare("UPDATE bookings SET payment_status='paid', payment_amount=?, updated_at=NOW() WHERE id=?");
    $stmt2->bind_param("di", $amount, $booking_id);
    $stmt2->execute();

    // notify client (pull-based)
    $clientId = $conn->query("SELECT client_id FROM bookings WHERE id=$booking_id")->fetch_assoc()['client_id'] ?? null;
    if ($clientId) {
      $msg = "💰 Payment received for Booking #$booking_id";
      $st = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
      $st->bind_param("is", $clientId, $msg);
      $st->execute();
    }
  } else {
    $conn->query("UPDATE payments SET status='failed' WHERE booking_id=$booking_id");
  }
}

http_response_code(200);
echo json_encode(['ResultCode'=>0,'ResultDesc'=>'Accepted']);
