<?php
include('config.php');
include('navbar.php');

ensure_logged_in();
if(!is_admin()) { echo 'Access denied'; exit; }
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';
if(!$id) { header('Location: admin_dashboard.php'); exit; }

if($action==='approve'){
    mysqli_query($conn, "UPDATE bookings SET status='approved' WHERE id=$id");
} elseif($action==='cancel'){
    // set truck back to available
    $bk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT truck_id FROM bookings WHERE id=$id"));
    if($bk) mysqli_query($conn, "UPDATE trucks SET status='available' WHERE id={$bk['truck_id']}");
    mysqli_query($conn, "UPDATE bookings SET status='cancelled' WHERE id=$id");
} elseif($action==='start'){
    mysqli_query($conn, "UPDATE bookings SET status='on_trip' WHERE id=$id");
    $bk = mysqli_fetch_assoc(mysqli_query($conn, "SELECT truck_id FROM bookings WHERE id=$id"));
    if($bk) mysqli_query($conn, "UPDATE trucks SET status='on_trip' WHERE id={$bk['truck_id']}");
}
header('Location: admin_dashboard.php'); exit;
