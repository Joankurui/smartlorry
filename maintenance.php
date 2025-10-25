<?php
include('config.php');
ensure_logged_in();
if ($_SESSION['user']['role'] != 'admin') {
    die("Access denied");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $truck_id = $_POST['truck_id'];
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $cost = $_POST['cost'];
    mysqli_query($conn, "INSERT INTO maintenance (truck_id, description, cost) VALUES ('$truck_id', '$desc', '$cost')");
}

$trucks = mysqli_query($conn, "SELECT * FROM trucks");
$records = mysqli_query($conn, "SELECT m.*, t.plate_number FROM maintenance m JOIN trucks t ON m.truck_id=t.id ORDER BY maintenance_date DESC");
?>

<h2>Truck Maintenance</h2>
<form method="POST">
  <label>Truck:</label>
  <select name="truck_id" required>
    <?php while($t = mysqli_fetch_assoc($trucks)): ?>
      <option value="<?=$t['id']?>"><?=$t['plate_number']?></option>
    <?php endwhile; ?>
  </select><br>
  <label>Description:</label>
  <input type="text" name="description" required><br>
  <label>Cost (Ksh):</label>
  <input type="number" name="cost" step="0.01"><br>
  <button type="submit">Add Maintenance</button>
</form>

<h3>Maintenance Records</h3>
<table border="1">
<tr><th>ID</th><th>Truck</th><th>Description</th><th>Cost</th><th>Date</th></tr>
<?php while($m = mysqli_fetch_assoc($records)): ?>
<tr>
  <td><?=$m['id']?></td>
  <td><?=$m['plate_number']?></td>
  <td><?=$m['description']?></td>
  <td><?=$m['cost']?></td>
  <td><?=$m['maintenance_date']?></td>
</tr>
<?php endwhile; ?>
</table>
