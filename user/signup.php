<?php
include('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        // check first
        $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE LOWER(email)=LOWER(?)");
        $check->execute([$email]);
        if ($check->fetchColumn() > 0) {
            $error = "That email is already registered. Please log in instead.";
        } else {
            // ✅ match lowercase column names with your table
            $stmt = $pdo->prepare(
                "INSERT INTO users (name, email, password) VALUES (?, ?, ?)"
            );
            $stmt->execute([$name, $email, $password]);

            $success = "Account created successfully! <a href='login.php'>Login here</a>";
        }
    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) { // duplicate-key error
            $error = "This email is already in use.";
        } else {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Sign-Up</title>
<link rel="stylesheet" href="../css/style3.css">
</head>
<body>
<div class="container login-box">
  <h2>User Sign-Up</h2>
  <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
  <?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>
  <form method="POST">
    <label>Name:</label>
    <input type="name" name="name" required>

    <label>Email:</label>
    <input type="email" name="email" required placeholder="example@gmail.com">

    <label>Password:</label>
    <input type="password" name="password" required>

    <input type="submit" value="Sign Up" class="btn">
    <p>Already have an account? <a href="login.php">Login</a></p>
  </form>
</div>
</body>
</html>
