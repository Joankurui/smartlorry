<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  $check = $conn->prepare("SELECT id FROM users WHERE email=?");
  $check->bind_param("s", $email);
  $check->execute();
  $check->store_result();

  if ($check->num_rows > 0) {
    $error = "Email already exists!";
  } else {
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);
    if ($stmt->execute()) {
      $_SESSION['user'] = ['id' => $stmt->insert_id, 'name' => $name, 'email' => $email];
      header("Location: dashboard.php");
      exit;
    } else {
      $error = "Registration failed. Try again.";
    }
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Register | SmartLorry</title>
  <style>
    body {
      background: linear-gradient(120deg, #2d89ef, #1b5fab);
      height: 100vh; display: flex; justify-content: center; align-items: center;
      font-family: 'Poppins', sans-serif;
    }
    .register-box {
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
  <div class="register-box">
    <h2>Create Account</h2>
    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
      <input type="text" name="name" placeholder="Full Name" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Login</a></p>
  </div>
</body>
</html>
