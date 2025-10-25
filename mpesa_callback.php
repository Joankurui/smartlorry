<?php
include('config.php');

$data = file_get_contents('php://input');
$log = fopen("mpesa_callback_log.txt", "a");
fwrite($log, $data);
fclose($log);

$callback = json_decode($data, true);

if (isset($callback['Body']['stkCallback'])) {
    $stk = $callback['Body']['stkCallback'];
    $resultCode = $stk['ResultCode'];
    $booking_id = str_replace('SMARTLORRY-', '', $stk['CallbackMetadata']['Item'][1]['Value']);

    if ($resultCode == 0) {
        $amount = $stk['CallbackMetadata']['Item'][0]['Value'];
        $mpesa_receipt = $stk['CallbackMetadata']['Item'][1]['Value'];

        mysqli_query($conn, "UPDATE payments 
                             SET status='successful', mpesa_receipt='$mpesa_receipt' 
                             WHERE booking_id='$booking_id'");

        mysqli_query($conn, "UPDATE bookings SET payment_status='paid' WHERE id='$booking_id'");
    } else {
        mysqli_query($conn, "UPDATE payments SET status='failed' WHERE booking_id='$booking_id'");
    }
}
?>
