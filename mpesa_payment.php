<?php
include('config.php');
ensure_logged_in();

$booking_id = $_POST['booking_id'];
$phone = $_POST['phone'];
$amount = $_POST['amount'];

// Fetch keys
$consumerKey = "YOUR_CONSUMER_KEY";
$consumerSecret = "YOUR_CONSUMER_SECRET";
$BusinessShortCode = "174379"; // Test paybill
$Passkey = "YOUR_PASSKEY";

// Get Access Token
$url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
$credentials = base64_encode("$consumerKey:$consumerSecret");

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Basic $credentials"));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$token = json_decode($response)->access_token;

// Initiate STK Push
$url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
$timestamp = date('YmdHis');
$password = base64_encode($BusinessShortCode.$Passkey.$timestamp);

$curl_post_data = [
  'BusinessShortCode' => $BusinessShortCode,
  'Password' => $password,
  'Timestamp' => $timestamp,
  'TransactionType' => 'CustomerPayBillOnline',
  'Amount' => $amount,
  'PartyA' => $phone,
  'PartyB' => $BusinessShortCode,
  'PhoneNumber' => $phone,
  'CallBackURL' => 'https://yourdomain.com/mpesa_callback.php',
  'AccountReference' => 'SMARTLORRY-' . $booking_id,
  'TransactionDesc' => 'Booking Payment'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  'Content-Type:application/json',
  "Authorization:Bearer $token"
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($curl_post_data));
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if (isset($data['MerchantRequestID'])) {
    // Save to payments table
    mysqli_query($conn, "INSERT INTO payments (booking_id, amount, phone, status) 
                         VALUES ('$booking_id', '$amount', '$phone', 'initiated')");
    echo "Payment request sent to your phone. Enter M-Pesa PIN to confirm.";
} else {
    echo "Failed to initiate payment. Please try again.";
}
?>
