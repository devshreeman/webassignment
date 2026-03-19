<?php
/**
 * Admin Account Settings — update own profile, change password, manage admin accounts.
 */
if (session_status() === PHP_SESSION_NONE) session_start();
include('../config/db.php');

$pageTitle         = 'Account Settings';
$activeSidebarItem = 'settings';
include('../includes/admin_header.php');

$adminId = (int)$_SESSION['admin']['AdminID'];
$msg     = '';
$msgType = 'success';

/* ── Update Profile ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_action'] ?? '') === 'update_profile') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);

    if (empty($name) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg     = 'Please provide a valid name and email address.';
        $msgType = 'error';
    } else {
        /* Check email uniqueness */
        $check = $pdo->prepare("SELECT AdminID FROM admin WHERE email = ? AND AdminID != ?");
        $check->execute([$email, $adminId]);
        if ($check->rowCount() > 0) {
            $msg     = 'That email address is already used by another admin account.';
            $msgType = 'error';
        } else {
            $pdo->prepare("UPDATE admin SET name = ?, email = ? WHERE AdminID = ?")
                ->execute([$name, $email, $adminId]);
            $_SESSION['admin']['name']  = $name;
            $_SESSION['admin']['email'] = $email;
            $msg = 'Profile updated successfully.';
        }
    }
}

/* ── Change Password ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_action'] ?? '') === 'change_password') {
    $current  = $_POST['current_password'] ?? '';
    $new      = $_POST['new_password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    $row = $pdo->prepare("SELECT password FROM admin WHERE AdminID = ?");
    $row->execute([$adminId]);
    $stored = $row->fetchColumn();

    if (!password_verify($current, $stored)) {
        $msg     = 'Your current password is incorrect.';
        $msgType = 'error';
    } elseif (strlen($new) < 8) {
        $msg     = 'New password must be at least 8 characters.';
        $msgType = 'error';
    } elseif ($new !== $confirm) {
        $msg     = 'New passwords do not match.';
        $msgType = 'error';
    } else {
        $pdo->prepare("UPDATE admin SET password = ? WHERE AdminID = ?")
            ->execute([password_hash($new, PASSWORD_DEFAULT), $adminId]);
        $msg = 'Password changed successfully.';
    }
}

/* ── Add Admin ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_action'] ?? '') === 'add_admin') {
    $newName  = trim($_POST['new_name']);
    $newEmail = trim($_POST['new_email']);
    $newPass  = $_POST['new_password'];

    if (empty($newName) || !filter_var($newEmail, FILTER_VALIDATE_EMAIL) || strlen($newPass) < 8) {
        $msg     = 'Please provide a valid name, email, and password (min. 8 characters) for the new administrator.';
        $msgType = 'error';
    } else {
        $check = $pdo->prepare("SELECT AdminID FROM admin WHERE email = ?");
        $check->execute([$newEmail]);
        if ($check->rowCount() > 0) {
            $msg     = 'An administrator with that email already exists.';
            $msgType = 'error';
        } else {
            $pdo->prepare("INSERT INTO admin (name, email, password) VALUES (?, ?, ?)")
                ->execute([$newName, $newEmail, password_hash($newPass, PASSWORD_DEFAULT)]);
            $msg = "Administrator account for '{$newName}' created successfully.";
        }
    }
}

/* ── Delete Admin ── */
if (isset($_GET['delete_admin'])) {
    $delId = (int)$_GET['delete_admin'];
    if ($delId === $adminId) {
        $msg     = 'You cannot delete your own account.';
        $msgType = 'error';
    } else {
        $pdo->prepare("DELETE FROM admin WHERE AdminID = ?")->execute([$delId]);
        $msg = 'Administrator account removed.';
    }
}

/* ── Fetch data ── */
$me     = $pdo->prepare("SELECT * FROM admin WHERE AdminID = ?");
$me->execute([$adminId]);
$me     = $me->fetch(PDO::FETCH_ASSOC);

$admins = $pdo->query("SELECT AdminID, name, email, created_at FROM admin ORDER BY created_at ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="admin-topbar">
  <div>
    <h1 class="admin-page-title">Account Settings</h1>
    <p class="admin-page-subtitle">Manage your profile, change your password, and add or remove administrator accounts.</p>
  </div>
</div>

<?php if ($msg): ?>
  <div class="alert alert--<?= $msgType ?>" role="alert"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-6);align-items:start;">

  <!-- My Profile -->
  <div class="admin-section-card">
    <div class="admin-section-card__header">
      <h2 class="admin-section-card__title">My Profile</h2>
    </div>
    <div style="display:flex;align-items:center;gap:var(--space-4);margin-bottom:var(--space-6);padding-bottom:var(--space-6);border-bottom:1px solid var(--color-border);">
      <div style="width:56px;height:56px;border-radius:50%;background:var(--color-primary);display:flex;align-items:center;justify-content:center;font-family:'Merriweather',serif;font-size:1.25rem;font-weight:700;color:var(--color-accent);flex-shrink:0;">
        <?= htmlspecialchars(strtoupper(substr($me['name'] ?? 'A', 0, 1))) ?>
      </div>
      <div>
        <div style="font-weight:700;font-size:var(--text-lg);color:var(--color-primary);"><?= htmlspecialchars($me['name'] ?: 'Administrator') ?></div>
        <div style="font-size:var(--text-sm);color:var(--color-text-muted);"><?= htmlspecialchars($me['email']) ?></div>
        <div style="font-size:var(--text-xs);color:var(--color-text-light);margin-top:2px;">Administrator · Student Course Hub</div>
      </div>
    </div>
    <form method="POST" action="">
      <input type="hidden" name="_action" value="update_profile">
      <div class="form-group">
        <label class="form-label" for="name">Display Name <span class="form-required">*</span></label>
        <input class="form-input" type="text" id="name" name="name" required
               value="<?= htmlspecialchars($me['name'] ?? '') ?>" placeholder="Your name">
      </div>
      <div class="form-group">
        <label class="form-label" for="email">Email Address <span class="form-required">*</span></label>
        <input class="form-input" type="email" id="email" name="email" required
               value="<?= htmlspecialchars($me['email']) ?>" placeholder="admin@excellence.ac.uk">
      </div>
      <button type="submit" class="btn btn-primary">Save Profile</button>
    </form>
  </div>

  <!-- Change Password -->
  <div class="admin-section-card">
    <div class="admin-section-card__header">
      <h2 class="admin-section-card__title">Change Password</h2>
    </div>
    <form method="POST" action="" autocomplete="off">
      <input type="hidden" name="_action" value="change_password">
      <div class="form-group">
        <label class="form-label" for="current_password">Current Password <span class="form-required">*</span></label>
        <input class="form-input" type="password" id="current_password" name="current_password" required
               placeholder="Enter your current password" autocomplete="current-password">
      </div>
      <div class="form-group">
        <label class="form-label" for="new_password">New Password <span class="form-required">*</span></label>
        <input class="form-input" type="password" id="new_password" name="new_password" required
               placeholder="Minimum 8 characters" autocomplete="new-password">
        <small class="form-hint">Must be at least 8 characters long.</small>
      </div>
      <div class="form-group">
        <label class="form-label" for="confirm_password">Confirm New Password <span class="form-required">*</span></label>
        <input class="form-input" type="password" id="confirm_password" name="confirm_password" required
               placeholder="Repeat new password" autocomplete="new-password">
      </div>
      <button type="submit" class="btn btn-primary">Update Password</button>
    </form>
  </div>

</div>

<!-- Add New Administrator -->
<div class="admin-section-card" style="margin-top:var(--space-6);">
  <div class="admin-section-card__header">
    <h2 class="admin-section-card__title">Add Administrator Account</h2>
  </div>
  <form method="POST" action="" autocomplete="off">
    <input type="hidden" name="_action" value="add_admin">
    <div class="form-row">
      <div class="form-group">
        <label class="form-label" for="new_name">Name <span class="form-required">*</span></label>
        <input class="form-input" type="text" id="new_name" name="new_name" required placeholder="e.g. Jane Smith">
      </div>
      <div class="form-group">
        <label class="form-label" for="new_email">Email <span class="form-required">*</span></label>
        <input class="form-input" type="email" id="new_email" name="new_email" required placeholder="admin@excellence.ac.uk">
      </div>
    </div>
    <div class="form-group" style="max-width:360px;">
      <label class="form-label" for="new_admin_password">Temporary Password <span class="form-required">*</span></label>
      <input class="form-input" type="password" id="new_admin_password" name="new_password" required
             placeholder="Min 8 characters" autocomplete="new-password">
      <small class="form-hint">Share this securely with the new admin. They should change it on first login.</small>
    </div>
    <button type="submit" class="btn btn-success">Create Administrator Account</button>
  </form>
</div>

<!-- Admin Accounts List -->
<div class="admin-section-card" style="margin-top:var(--space-6);">
  <div class="admin-section-card__header">
    <h2 class="admin-section-card__title">All Administrator Accounts (<?= count($admins) ?>)</h2>
  </div>
  <div class="data-table-wrapper">
    <table class="data-table" aria-label="Administrator accounts">
      <thead>
        <tr>
          <th scope="col">Name</th>
          <th scope="col">Email</th>
          <th scope="col">Account Created</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($admins as $a): ?>
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:var(--space-3);">
                <div style="width:32px;height:32px;border-radius:50%;background:var(--color-primary);display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;color:var(--color-accent);flex-shrink:0;">
                  <?= htmlspecialchars(strtoupper(substr($a['name'] ?? 'A', 0, 1))) ?>
                </div>
                <strong><?= htmlspecialchars($a['name'] ?: '(unnamed)') ?></strong>
                <?php if ($a['AdminID'] === $adminId): ?>
                  <span class="status-badge status-badge--published" style="font-size:0.65rem;">You</span>
                <?php endif; ?>
              </div>
            </td>
            <td><?= htmlspecialchars($a['email']) ?></td>
            <td style="color:var(--color-text-muted);white-space:nowrap;">
              <?= !empty($a['created_at']) ? date('d M Y', strtotime($a['created_at'])) : '—' ?>
            </td>
            <td>
              <?php if ($a['AdminID'] !== $adminId): ?>
                <a href="admin_settings.php?delete_admin=<?= $a['AdminID'] ?>"
                   class="btn btn-danger btn-sm"
                   data-confirm="Remove administrator account for <?= htmlspecialchars(addslashes($a['name'])) ?>? They will immediately lose access.">
                  Remove
                </a>
              <?php else: ?>
                <span style="font-size:var(--text-xs);color:var(--color-text-muted);">Current session</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include('../includes/admin_footer.php'); ?>
