<?php
include('config.php');
include('navbar.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;

$booking_id = intval($_POST['booking_id']);
$amount = floatval($_POST['amount']);
$phone = preg_replace('/\D/','',$_POST['phone'] ?? '');
if (!$booking_id || !$amount || !$phone) { header('Location: client_dashboard.php?msg=invalid'); exit; }

// normalize phone -> 2547XXXXXXXX
if (strlen($phone) == 9 && substr($phone,0,1) == '7') $phone = '254'.$phone;
if (substr($phone,0,1) == '0') $phone = '254'.substr($phone,1);

$env = $_ENV['MPESA_ENV'] ?? 'sandbox';
$tokenUrl = $env === 'production' ? 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials' : 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
$stkUrl = $env === 'production' ? 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest' : 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

$consumerKey = $_ENV['MPESA_CONSUMER_KEY'];
$consumerSecret = $_ENV['MPESA_CONSUMER_SECRET'];
$shortcode = $_ENV['MPESA_SHORTCODE'];
$passkey = $_ENV['MPESA_PASSKEY'];
$callback = $_ENV['MPESA_CALLBACK_URL'];

// Get token
$ch = curl_init($tokenUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic '.base64_encode("$consumerKey:$consumerSecret")]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$tokenRes = json_decode(curl_exec($ch), true);
curl_close($ch);
$accessToken = $tokenRes['access_token'] ?? null;
if (!$accessToken) { header('Location: client_dashboard.php?msg=tok_fail'); exit; }

// STK push payload
$timestamp = date('YmdHis');
$password = base64_encode($shortcode.$passkey.$timestamp);
$payload = [
  'BusinessShortCode' => $shortcode,
  'Password' => $password,
  'Timestamp' => $timestamp,
  'TransactionType' => 'CustomerPayBillOnline',
  'Amount' => $amount,
  'PartyA' => $phone,
  'PartyB' => $shortcode,
  'PhoneNumber' => $phone,
  'CallBackURL' => $callback,
  'AccountReference' => 'SmartLorry-'.$booking_id,
  'TransactionDesc' => "Booking #$booking_id payment"
];

$ch = curl_init($stkUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json','Authorization:Bearer '.$accessToken]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
$response = json_decode(curl_exec($ch), true);
curl_close($ch);

// insert payment record
$stmt = $conn->prepare("INSERT INTO payments (booking_id, amount, phone, status) VALUES (?, ?, ?, 'initiated')");
$stmt->bind_param("ids", $booking_id, $amount, $phone);
$stmt->execute();

if (isset($response['ResponseCode']) && $response['ResponseCode'] === '0') {
  header('Location: client_dashboard.php?msg=stk_sent');
  exit;
} else {
  header('Location: client_dashboard.php?msg=stk_failed');
  exit;
}
