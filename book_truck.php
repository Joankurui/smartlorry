<?php
include('config.php');
include('navbar.php');

ensure_logged_in();

if(!isset($_GET['truck_id'])) { header('Location: client_dashboard.php'); exit; }
$truck_id=(int)$_GET['truck_id'];
$truck = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM trucks WHERE id=$truck_id"));
if(!$truck){ echo 'Truck not found'; exit; }

if($_SERVER['REQUEST_METHOD']==='POST'){
    $origin = mysqli_real_escape_string($conn,$_POST['origin']);
    $destination = mysqli_real_escape_string($conn,$_POST['destination']);
    $distance = floatval($_POST['distance_km']);
    $cost = $distance * floatval($truck['cost_per_km']);
    $client_id = $_SESSION['user']['id'];
    
    $sql = "INSERT INTO bookings (client_id,truck_id,origin,destination,distance_km,estimated_cost,payment_amount,status) 
            VALUES ($client_id,$truck_id,'$origin','$destination',$distance,$cost,$cost,'pending')";
    
    if(mysqli_query($conn,$sql)){
        mysqli_query($conn, "UPDATE trucks SET status='booked' WHERE id=$truck_id");
        header('Location: payment.php?booking_id=' . mysqli_insert_id($conn)); exit;
    } else $error = mysqli_error($conn);
}
?>
<!doctype html>
<html>
<head><meta charset='utf-8'><title>Book Truck</title></head>
<body>
<h2>Book Truck: <?=htmlspecialchars($truck['plate_number'])?></h2>
<?php if(!empty($error)) echo '<p style="color:red;">'.htmlspecialchars($error).'</p>'; ?>
<form method="post">
  Origin:<br><input name="origin" required><br>
  Destination:<br><input name="destination" required><br>
  Distance (km - approximate):<br><input name="distance_km" type="number" step="0.1" required><br><br>
  <button>Proceed to Payment</button>
</form>
<p><a href="client_dashboard.php">Back</a></p>
</body>
</html>
