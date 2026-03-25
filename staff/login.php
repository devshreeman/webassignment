<?php
include('../config/db.php');

$pageTitle         = 'Staff Login';
$activePage        = 'staff-login';

// redirect if already logged in
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['staff'])) {
    header("Location: dashboard.php");
    exit;
}

// handle login form submission
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $errors[] = 'Please enter both email and password.';
    } else {
        // look up staff member by email
        $stmt = $pdo->prepare("SELECT * FROM staff WHERE Email = ?");
        $stmt->execute([$email]);
        $staff = $stmt->fetch(PDO::FETCH_ASSOC);

        // verify staff credentials and password
        if ($staff && !empty($staff['password']) && password_verify($password, $staff['password'])) {
            $_SESSION['staff'] = [
                'StaffID' => $staff['StaffID'],
                'Name'    => $staff['Name'],
                'Email'   => $staff['Email']
            ];
            header("Location: dashboard.php");
            exit;
        } else {
            $errors[] = 'Invalid email address or password. (Note: If you are new, an admin must set your password first).';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff Login | University of Liverpool</title>
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
        <span>Staff Portal</span>
      </div>
    </a>

    <h1 class="auth-card__title">Staff Sign In</h1>
    <p class="auth-card__subtitle">Access your modules and teaching schedule.</p>

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
          placeholder="s.johnson@university.ac.uk"
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
        Sign In to Staff Portal
      </button>
    </form>

    <p class="auth-card__footer" style="margin-top:var(--space-6);">
      <a href="../public/index.php" style="color:var(--color-text-muted);font-size:var(--text-xs);">← Return to Student Site</a>
    </p>

  </div>
</div>

</body>
</html>
