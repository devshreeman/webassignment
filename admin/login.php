<?php
include('../config/db.php');

$pageTitle         = 'Admin Login';
$activeSidebarItem = '';

/* Redirect if Already Logged In */
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['admin'])) {
    header("Location: dashboard.php");
    exit;
}

/* Handle Login Form Submission */
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $errors[] = 'Please enter both email and password.';
    } else {
        /* Query Admin Table */
        $stmt = $pdo->prepare("SELECT * FROM admin WHERE email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin'] = $admin;
            header("Location: dashboard.php");
            exit;
        } else {
            $errors[] = 'Invalid email address or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login | Student Course Hub</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="../css/university.css">
</head>
<body>

<div class="auth-page" role="main">
  <div class="auth-card">

    <h1 class="auth-card__title">Administrator Sign In</h1>
    <p class="auth-card__subtitle">Restricted access. Authorised personnel only.</p>

    <?php if ($errors): ?>
      <div class="alert alert--error" role="alert">
        <?php foreach ($errors as $e): ?>
          <p style="margin:0;"><?= htmlspecialchars($e) ?></p>
        <?php endforeach; ?>
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
          value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
          placeholder="admin@university.ac.uk"
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
          placeholder="••••••••"
          required
          aria-required="true"
          autocomplete="current-password"
        >
      </div>

      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:var(--space-2);">
        Sign In to Admin Panel
      </button>
    </form>

    <p class="auth-card__footer" style="margin-top:var(--space-6);">
      <a href="../public/index.php" style="color:var(--color-text-muted);font-size:var(--text-xs);">← Return to Student Site</a>
    </p>

  </div>
</div>

</body>
</html>
