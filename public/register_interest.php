<?php
include('../config/db.php');

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$progId = (int)$_GET['id'];
$progStmt = $pdo->prepare("SELECT ProgrammeName FROM programmes WHERE ProgrammeID = ? AND IsPublished = 1");
$progStmt->execute([$progId]);
$prog = $progStmt->fetch(PDO::FETCH_ASSOC);
if (!$prog) {
    header("Location: index.php");
    exit;
}

session_start();
$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['StudentName'] ?? '');
    $email = trim($_POST['Email'] ?? '');

    // Validate
    if (empty($name))              $errors[] = 'Please enter your full name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';

    if (empty($errors)) {
        // Check for duplicate
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM interestedstudents WHERE ProgrammeID = ? AND Email = ?");
        $checkStmt->execute([$progId, $email]);
        if ($checkStmt->fetchColumn() > 0) {
            $errors[] = 'This email is already registered for this programme.';
        } else {
            $ins = $pdo->prepare("INSERT INTO interestedstudents (ProgrammeID, StudentName, Email) VALUES (?, ?, ?)");
            $ins->execute([$progId, $name, $email]);
            $_SESSION['flash_success'] = 'Thank you! Your interest has been registered. We will be in touch soon.';
            header("Location: programme.php?id=$progId");
            exit;
        }
    }
}

$pageTitle  = 'Register Interest — ' . htmlspecialchars($prog['ProgrammeName']);
$activePage = 'home';
$cssBase    = '../';
$rootBase   = '../';
include('../includes/header.php');
?>

<div style="background: var(--color-surface); border-bottom: 1px solid var(--color-border);">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <span class="breadcrumb__item"><a href="index.php">Programmes</a></span>
      <span class="breadcrumb__separator" aria-hidden="true">›</span>
      <span class="breadcrumb__item"><a href="programme.php?id=<?= $progId ?>"><?= htmlspecialchars($prog['ProgrammeName']) ?></a></span>
      <span class="breadcrumb__separator" aria-hidden="true">›</span>
      <span class="breadcrumb__item breadcrumb__item--current" aria-current="page">Register Interest</span>
    </nav>
  </div>
</div>

<section class="section">
  <div class="container--narrow">

    <div class="section-header">
      <span class="section-header__eyebrow">Prospective Students</span>
      <h1 class="section-header__title">Register Your Interest</h1>
      <p class="section-header__subtitle">
        in <strong><?= htmlspecialchars($prog['ProgrammeName']) ?></strong>
      </p>
      <div class="section-divider"></div>
    </div>

    <?php if ($errors): ?>
      <div class="alert alert--error" role="alert">
        <div>
          <strong>Please correct the following:</strong>
          <ul style="margin:0.5rem 0 0 1rem;">
            <?php foreach ($errors as $e): ?>
              <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    <?php endif; ?>

    <div class="form-card">
      <div style="margin-bottom:var(--space-6);">
        <p style="color:var(--color-text-muted);font-size:var(--text-sm);">
          Fill in your details below and we'll keep you informed about <strong><?= htmlspecialchars($prog['ProgrammeName']) ?></strong> — including open days, application windows, and programme updates. Your information is stored securely and in accordance with our Privacy Policy.
        </p>
      </div>

      <form method="POST" action="" novalidate>
        <div class="form-group">
          <label class="form-label" for="StudentName">
            Full Name <span class="form-required" aria-hidden="true">*</span>
          </label>
          <input
            class="form-input"
            type="text"
            id="StudentName"
            name="StudentName"
            value="<?= htmlspecialchars($_POST['StudentName'] ?? '') ?>"
            placeholder="e.g. Jane Smith"
            required
            aria-required="true"
            autocomplete="name"
          >
        </div>

        <div class="form-group">
          <label class="form-label" for="Email">
            Email Address <span class="form-required" aria-hidden="true">*</span>
          </label>
          <input
            class="form-input"
            type="email"
            id="Email"
            name="Email"
            value="<?= htmlspecialchars($_POST['Email'] ?? '') ?>"
            placeholder="you@example.com"
            required
            aria-required="true"
            autocomplete="email"
          >
          <small class="form-hint">We'll only use this to send you programme-related updates. No spam, ever.</small>
        </div>

        <!-- Honeypot anti-spam -->
        <div style="display:none;" aria-hidden="true">
          <label for="_gotcha">Leave this empty</label>
          <input type="text" id="_gotcha" name="_gotcha" tabindex="-1">
        </div>

        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:var(--space-6);flex-wrap:wrap;gap:var(--space-4);">
          <a href="programme.php?id=<?= $progId ?>" class="btn btn-secondary">← Back to Programme</a>
          <button type="submit" class="btn btn-primary btn-lg">Register My Interest</button>
        </div>
      </form>
    </div>

  </div>
</section>

<?php include('../includes/footer.php'); ?>
