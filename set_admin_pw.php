<?php
include('config.php');
include('navbar.php');

$pw = password_hash('admin123', PASSWORD_BCRYPT);
mysqli_query($conn, "UPDATE users SET password='$pw' WHERE email='admin@smartlorry.test'");
echo "Admin password set to: admin123\n";
?>
