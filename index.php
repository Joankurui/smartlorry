<?php
include('config.php');
include('navbar.php');

// Fetch available lorries to display on the landing page
$lorries = mysqli_query($conn, "SELECT * FROM trucks WHERE status='available' LIMIT 6");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SmartLorry | Move Smarter</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: #f9f9f9;
    }

    /* HERO SECTION */
    .hero {
      background: linear-gradient(to right, rgba(45,137,239,0.8), rgba(27,95,171,0.8)),
                  url('assets/images/truck-bg.jpg') center/cover no-repeat;
      color: white;
      text-align: center;
      padding: 100px 20px;
    }
    .hero h1 {
      font-size: 2.8em;
      margin-bottom: 15px;
      font-weight: 700;
    }
    .hero p {
      font-size: 1.2em;
      margin-bottom: 30px;
    }
    .hero .cta-buttons a {
      background: white;
      color: #2d89ef;
      padding: 12px 25px;
      border-radius: 30px;
      margin: 0 10px;
      text-decoration: none;
      font-weight: 600;
      transition: 0.3s;
    }
    .hero .cta-buttons a:hover {
      background: #1b5fab;
      color: white;
    }

    /* AVAILABLE LORRIES */
    .section {
      padding: 60px 10%;
      text-align: center;
    }
    .section h2 {
      color: #2d89ef;
      margin-bottom: 30px;
    }
    .lorry-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 25px;
    }
    .lorry-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      overflow: hidden;
      transition: transform 0.3s ease;
    }
    .lorry-card:hover {
      transform: translateY(-5px);
    }
    .lorry-card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
    }
    .lorry-card h3 {
      color: #1b5fab;
      margin: 15px 0 5px;
    }
    .lorry-card p {
      margin: 5px 0;
      color: #555;
    }
    .lorry-card a {
      display: inline-block;
      margin: 10px 0 15px;
      background: #2d89ef;
      color: white;
      padding: 8px 15px;
      border-radius: 6px;
      text-decoration: none;
      transition: 0.3s;
    }
    .lorry-card a:hover {
      background: #1b5fab;
    }

    /* MAP PREVIEW */
    #map {
      width: 100%;
      height: 400px;
      border-radius: 12px;
      margin-top: 30px;
    }

    /* FOOTER */
    footer {
      background: #1b5fab;
      color: white;
      text-align: center;
      padding: 20px 0;
      margin-top: 60px;
    }
    footer a {
      color: #ffefc5;
      text-decoration: none;
    }
  </style>
</head>
<body>

<!-- HERO SECTION -->
<section class="hero">
  <h1>SmartLorry — Move Smarter, Faster, and Easier</h1>
  <p>Book trusted lorries and track them live from pickup to delivery — all in one platform.</p>
  <div class="cta-buttons">
    <a href="register.php">Get Started</a>
    <a href="available_lorries.php">View Lorries</a>
    <a href="track.php">Track Shipment</a>
  </div>
</section>

<!-- AVAILABLE LORRIES SECTION -->
<section class="section">
  <h2>Available Lorries</h2>
  <div class="lorry-grid">
    <?php while ($lorry = mysqli_fetch_assoc($lorries)): ?>
      <div class="lorry-card">
        <img src="assets/images/truck-placeholder.jpg" alt="Truck">
        <h3><?= htmlspecialchars($lorry['model']) ?></h3>
        <p><b>Plate:</b> <?= htmlspecialchars($lorry['plate_number']) ?></p>
        <p><b>Capacity:</b> <?= htmlspecialchars($lorry['capacity']) ?></p>
        <p><b>Cost/km:</b> KSh <?= htmlspecialchars($lorry['cost_per_km']) ?></p>
        <a href="login.php">Book Now</a>
      </div>
    <?php endwhile; ?>
  </div>
</section>

<!-- LIVE MAP SECTION -->
<section class="section">
  <h2>Live Fleet Map</h2>
  <p>Track all SmartLorry drivers in real time.</p>
  <div id="map"></div>
</section>

<!-- FOOTER -->
<footer>
  &copy; <?= date('Y') ?> SmartLorry. All Rights Reserved.  
  <br>
  <a href="contact.php">Contact Us</a> | <a href="about.php">About</a>
</footer>

<!-- GOOGLE MAPS SCRIPT -->
<script>
  function initMap() {
    const center = { lat: -1.286389, lng: 36.817223 }; // Nairobi default center
    const map = new google.maps.Map(document.getElementById("map"), {
      zoom: 7,
      center: center,
    });

    // Example truck markers (to be replaced with live DB data)
    const trucks = [
      { lat: -1.286389, lng: 36.817223, title: "Truck A" },
      { lat: -0.1022, lng: 34.7617, title: "Truck B" },
      { lat: -3.3869, lng: 37.5300, title: "Truck C" },
    ];

    trucks.forEach(t => {
      new google.maps.Marker({
        position: { lat: t.lat, lng: t.lng },
        map: map,
        title: t.title,
      });
    });
  }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap"
async defer></script>

</body>
</html>
