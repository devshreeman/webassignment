<?php
include('../config/db.php');
$pageTitle         = 'Edit Staff Member';
$activeSidebarItem = 'staff';
include('../includes/admin_header.php');

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header("Location: manage_staff.php"); exit; }

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['Name']);
    $title = trim($_POST['Title']);
    $email = trim($_POST['Email']);
    $bio   = trim($_POST['Bio']);
    $photo = trim($_POST['Photo']);

    $pdo->prepare("UPDATE staff SET Name=?, Title=?, Email=?, Bio=?, Photo=? WHERE StaffID=?")
        ->execute([$name, $title, $email, $bio, $photo, $id]);
    $msg = 'Staff member updated.';
}

$s = $pdo->prepare("SELECT * FROM staff WHERE StaffID = ?");
$s->execute([$id]);
$staffMember = $s->fetch(PDO::FETCH_ASSOC);

// Modules they lead
$led = $pdo->prepare("
    SELECT m.ModuleName, p.ProgrammeName
    FROM modules m
    LEFT JOIN programmes p ON m.ProgrammeID = p.ProgrammeID
    WHERE m.ModuleLeaderID = ?
    ORDER BY m.ModuleName
");
$led->execute([$id]);
$ledModules = $led->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="admin-topbar">
  <div>
    <nav class="breadcrumb" style="margin-bottom:0.5rem;">
      <span class="breadcrumb__item"><a href="manage_staff.php">Staff</a></span>
      <span class="breadcrumb__separator">›</span>
      <span class="breadcrumb__item breadcrumb__item--current">Edit</span>
    </nav>
    <h1 class="admin-page-title">Edit Staff Member</h1>
  </div>
  <a href="manage_staff.php" class="btn btn-secondary">← Back to Staff</a>
</div>

<?php if ($msg): ?>
  <div class="alert alert--success" role="alert"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:2fr 1fr;gap:var(--space-6);">
  <div class="admin-section-card">
    <form method="POST">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="Name">Full Name <span class="form-required">*</span></label>
          <input class="form-input" type="text" id="Name" name="Name" required value="<?= htmlspecialchars($staffMember['Name']) ?>">
        </div>
        <div class="form-group">
          <label class="form-label" for="Title">Title <span class="form-required">*</span></label>
          <input class="form-input" type="text" id="Title" name="Title" required value="<?= htmlspecialchars($staffMember['Title']) ?>">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="StaffEmail">Email <span class="form-required">*</span></label>
          <input class="form-input" type="email" id="StaffEmail" name="Email" required value="<?= htmlspecialchars($staffMember['Email']) ?>">
        </div>
        <div class="form-group">
          <label class="form-label" for="Photo">Photo URL</label>
          <input class="form-input" type="url" id="Photo" name="Photo" value="<?= htmlspecialchars($staffMember['Photo'] ?? '') ?>">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label" for="Bio">Biography</label>
        <textarea class="form-textarea" id="Bio" name="Bio" rows="5"><?= htmlspecialchars($staffMember['Bio'] ?? '') ?></textarea>
      </div>
      <div style="display:flex;gap:var(--space-4);">
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="manage_staff.php" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>

  <!-- Modules led -->
  <div class="admin-section-card">
    <h2 class="admin-section-card__title" style="margin-bottom:var(--space-4);">Modules Led</h2>
    <?php if ($ledModules): ?>
      <ul style="list-style:none;padding:0;margin:0;">
        <?php foreach ($ledModules as $lm): ?>
          <li style="padding:var(--space-3) 0;border-bottom:1px solid var(--color-border);font-size:var(--text-sm);">
            <div style="font-weight:600;"><?= htmlspecialchars($lm['ModuleName']) ?></div>
            <div style="color:var(--color-text-muted);font-size:var(--text-xs);"><?= htmlspecialchars($lm['ProgrammeName'] ?? '') ?></div>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p style="color:var(--color-text-muted);font-size:var(--text-sm);">Not currently leading any modules.</p>
    <?php endif; ?>
  </div>
</div>

<?php include('../includes/admin_footer.php'); ?>
