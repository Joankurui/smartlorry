<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $query = $conn->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
  $query->bind_param("s", $email);
  $query->execute();
  $result = $query->get_result();
  $user = $result->fetch_assoc();

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user'] = $user;
    header("Location: dashboard.php");
    exit;
  } else {
    $error = "Invalid email or password";
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Login | SmartLorry</title>
  <style>
    body {
      background: linear-gradient(120deg, #2d89ef, #1b5fab);
      height: 100vh; display: flex; justify-content: center; align-items: center;
      font-family: 'Poppins', sans-serif;
    }
    .login-box {
      background: white;
      padding: 40px;
      border-radius: 12px;
      width: 350px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.2);
      text-align: center;
    }
    h2 { color: #2d89ef; }
    input {
      width: 100%; padding: 10px; margin: 10px 0;
      border: 1px solid #ccc; border-radius: 6px;
    }
    button {
      background: #2d89ef; color: white; border: none;
      padding: 10px 20px; border-radius: 6px; cursor: pointer;
      width: 100%; font-size: 1em;
    }
    button:hover { background: #1b5fab; }
    a { color: #2d89ef; text-decoration: none; }
  </style>
</head>
<body>
  <div class="login-box">
    <h2>Welcome Back</h2>
    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register</a></p>
  </div>
</body>
</html>
