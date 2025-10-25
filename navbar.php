<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<nav style="background:#fff;box-shadow:0 2px 6px rgba(0,0,0,.07);padding:12px 4%;display:flex;justify-content:space-between;align-items:center;">
  <div style="font-weight:700;color:#1b5fab"><a href="index.php" style="text-decoration:none;color:inherit;">Smart<span style="color:#2d89ef">Lorry</span></a></div>
  <div>
    <a href="index.php" style="margin-right:18px;color:#333;text-decoration:none;">Home</a>
    <a href="available_lorries.php" style="margin-right:18px;color:#333;text-decoration:none;">Lorries</a>
    <a href="track.php" style="margin-right:18px;color:#333;text-decoration:none;">Track</a>
    <?php if(isset($_SESSION['user'])): ?>
      <a href="dashboard.php" style="margin-right:18px;color:#333;text-decoration:none;">Dashboard</a>
      <a href="logout.php" style="background:#ff5252;color:#fff;padding:6px 12px;border-radius:6px;text-decoration:none;">Logout</a>
    <?php else: ?>
      <a href="login.php" style="background:#2d89ef;color:#fff;padding:6px 12px;border-radius:6px;text-decoration:none;">Login / Register</a>
    <?php endif; ?>
  </div>
</nav>
