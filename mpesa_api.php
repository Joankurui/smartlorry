<?php
// mpesa_api.php
require_once 'config.php';

function get_mpesa_token(){
    $env = $_ENV['MPESA_ENV'] ?? 'sandbox';
    $tokenUrl = $env === 'production' ? 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials' : 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
    $key = $_ENV['MPESA_CONSUMER_KEY'];
    $secret = $_ENV['MPESA_CONSUMER_SECRET'];
    $ch = curl_init($tokenUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic '.base64_encode("$key:$secret")]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $res = json_decode(curl_exec($ch), true);
    curl_close($ch);
    return $res['access_token'] ?? null;
}

function stk_push($booking_id, $amount, $phone){
    $env = $_ENV['MPESA_ENV'] ?? 'sandbox';
    $url = $env === 'production' ? 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest' : 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
    $shortcode = $_ENV['MPESA_SHORTCODE'];
    $passkey = $_ENV['MPESA_PASSKEY'];
    $timestamp = date('YmdHis');
    $password = base64_encode($shortcode.$passkey.$timestamp);
    $payload = [
        'BusinessShortCode'=>$shortcode,
        'Password'=>$password,
        'Timestamp'=>$timestamp,
        'TransactionType'=>'CustomerPayBillOnline',
        'Amount'=>floatval($amount),
        'PartyA'=>$phone,
        'PartyB'=>$shortcode,
        'PhoneNumber'=>$phone,
        'CallBackURL'=>$_ENV['MPESA_CALLBACK_URL'],
        'AccountReference'=>"SmartLorry-$booking_id",
        'TransactionDesc'=>"Booking #$booking_id"
    ];
    $token = get_mpesa_token();
    if (!$token) return ['error'=>'no_token'];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json','Authorization:Bearer '.$token]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    $res = json_decode(curl_exec($ch), true);
    curl_close($ch);
    return $res;
}
