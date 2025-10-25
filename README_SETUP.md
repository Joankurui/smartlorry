Additional setup notes:
- The included database.sql creates a demo admin with a placeholder password string.
  After importing the DB, set a usable admin password by running a PHP snippet:
  <?php
    include('config.php');
    $pw = password_hash('admin123', PASSWORD_BCRYPT);
    mysqli_query($conn, "UPDATE users SET password='$pw' WHERE email='admin@smartlorry.test'");
  ?>
- Replace YOUR_GOOGLE_MAPS_API_KEY in track.php with your actual key (or use OpenStreetMap).
- For real driver location, build a simple mobile page that POSTs to update_location.php periodically.
