<?php
session_start();
include('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm']);

    if ($password !== $confirm) {
        $error = "Passwords do not match!";
    } else {
        /* Hash the password */
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        /* Check if admin already exists */
        $stmt = $pdo->query("SELECT COUNT(*) FROM admin");
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $error = "An admin already exists! Only one admin account is allowed.";
        } else {
            /* Insert new admin */
            $stmt = $pdo->prepare("INSERT INTO admin (email, password) VALUES (?, ?)");
            $stmt->execute([$email, $hashed]);
            $success = "Admin account created successfully! You can now log in.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Sign Up</title>
  <link rel="stylesheet" href="../css/style3.css">
</head>
<body>
  <div class="container login-box">
    <h2>Admin Sign Up</h2>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>

    <form method="POST">
      <label>Email:</label>
      <input type="email" name="email" required>

      <label>Password:</label>
      <input type="password" name="password" required>

      <label>Confirm Password:</label>
      <input type="password" name="confirm" required>

      <input type="submit" value="Sign Up" class="btn">
    </form>

    <p class="signup-text">
      Already have an account? 
      <a href="login.php">Login</a>
    </p>
  </div>
</body>
</html>
