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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Sign Up | University of Liverpool</title>
  <link rel="icon" type="image/svg+xml" href="../assets/favicon.svg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="../css/university.css">
</head>
<body>

<div class="auth-page" role="main">
  <div class="auth-card">

    <a href="../public/index.php" class="auth-card__logo">
      <img src="../assets/logo.svg" alt="" width="48" height="48" aria-hidden="true">
      <div class="auth-card__logo-text">
        <span>University of Liverpool</span>
        <span>Administration</span>
      </div>
    </a>

    <h1 class="auth-card__title">Create Admin Account</h1>
    <p class="auth-card__subtitle">Set up the administrator account for the system.</p>

    <?php if (!empty($error)): ?>
      <div class="alert alert--error" role="alert">
        <p style="margin:0;"><?= htmlspecialchars($error) ?></p>
      </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
      <div class="alert alert--success" role="alert">
        <p style="margin:0;"><?= htmlspecialchars($success) ?></p>
      </div>
    <?php endif; ?>

    <form method="POST" action="" novalidate>
      <div class="form-group">
        <label class="form-label" for="email">Email Address</label>
        <input
          class="form-input"
          type="email"
          id="email"
          name="email"
          placeholder="admin@liverpool.ac.uk"
          required
          aria-required="true"
          autocomplete="email"
        >
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input
          class="form-input"
          type="password"
          id="password"
          name="password"
          required
          aria-required="true"
          autocomplete="new-password"
          minlength="8"
        >
        <small class="form-hint">Minimum 8 characters</small>
      </div>

      <div class="form-group">
        <label class="form-label" for="confirm">Confirm Password</label>
        <input
          class="form-input"
          type="password"
          id="confirm"
          name="confirm"
          required
          aria-required="true"
          autocomplete="new-password"
          minlength="8"
        >
      </div>

      <button type="submit" class="btn btn-primary" style="width:100%;">Create Admin Account</button>
    </form>

    <div class="auth-card__footer">
      Already have an account? <a href="login.php">Sign in here</a>
    </div>

  </div>
</div>

</body>
</html>
