<?php
include('../config/db.php');
$pageTitle         = 'Manage Staff';
$activeSidebarItem = 'staff';
include('../includes/admin_header.php');

$msg     = '';
$msgType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['Name']);
    $title = trim($_POST['Title']);
    $email = trim($_POST['Email']);
    $bio   = trim($_POST['Bio']);
    $photo = trim($_POST['Photo']);

    if ($name && $title && $email) {
        $stmt = $pdo->prepare("INSERT INTO staff (Name, Title, Email, Bio, Photo) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $title, $email, $bio, $photo]);
        $msg = 'Staff member added successfully.';
    } else {
        $msg     = 'Name, title and email are required.';
        $msgType = 'error';
    }
}

if (isset($_GET['delete'])) {
    $sid = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM staff WHERE StaffID = ?")->execute([$sid]);
    $msg = 'Staff member removed.';
}

$staffList = $pdo->query("
    SELECT s.*, COUNT(m.ModuleID) AS ModuleCount
    FROM staff s
    LEFT JOIN modules m ON m.ModuleLeaderID = s.StaffID
    GROUP BY s.StaffID
    ORDER BY s.Name
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="admin-topbar">
  <div>
    <h1 class="admin-page-title">Manage Staff</h1>
    <p class="admin-page-subtitle">Add and remove academic staff members and module leaders.</p>
  </div>
</div>

<?php if ($msg): ?>
  <div class="alert alert--<?= $msgType ?>" role="alert"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<!-- Add Staff -->
<div class="admin-section-card">
  <div class="admin-section-card__header">
    <h2 class="admin-section-card__title">Add New Staff Member</h2>
  </div>
  <form method="POST" action="">
    <div class="form-row">
      <div class="form-group">
        <label class="form-label" for="Name">Full Name <span class="form-required">*</span></label>
        <input class="form-input" type="text" id="Name" name="Name" required placeholder="e.g. Sarah Johnson">
      </div>
      <div class="form-group">
        <label class="form-label" for="Title">Title / Role <span class="form-required">*</span></label>
        <input class="form-input" type="text" id="Title" name="Title" required placeholder="e.g. Dr, Prof, Mr, Ms">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label" for="StaffEmail">Email <span class="form-required">*</span></label>
        <input class="form-input" type="email" id="StaffEmail" name="Email" required placeholder="s.johnson@university.ac.uk">
      </div>
      <div class="form-group">
        <label class="form-label" for="Photo">Photo URL</label>
        <input class="form-input" type="url" id="Photo" name="Photo" placeholder="https://…">
      </div>
    </div>
    <div class="form-group">
      <label class="form-label" for="Bio">Biography</label>
      <textarea class="form-textarea" id="Bio" name="Bio" rows="3" placeholder="Brief biography and research areas…"></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Add Staff Member</button>
  </form>
</div>

<!-- Staff Table -->
<div class="admin-section-card">
  <div class="admin-section-card__header">
    <h2 class="admin-section-card__title">All Staff (<?= count($staffList) ?>)</h2>
  </div>
  <div class="data-table-wrapper">
    <table class="data-table" aria-label="Staff list">
      <thead>
        <tr>
          <th scope="col">Name</th>
          <th scope="col">Title</th>
          <th scope="col">Email</th>
          <th scope="col">Modules Leading</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($staffList): ?>
          <?php foreach ($staffList as $s): ?>
            <tr>
              <td>
                <div style="display:flex;align-items:center;gap:var(--space-3);">
                  <div style="width:36px;height:36px;border-radius:50%;overflow:hidden;background:var(--color-primary);flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;color:var(--color-accent);">
                    <?php if (!empty($s['Photo'])): ?>
                      <img src="<?= htmlspecialchars($s['Photo']) ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
                    <?php else: ?>
                      <?= strtoupper(substr($s['Name'], 0, 2)) ?>
                    <?php endif; ?>
                  </div>
                  <strong><?= htmlspecialchars($s['Name']) ?></strong>
                </div>
              </td>
              <td><?= htmlspecialchars($s['Title']) ?></td>
              <td><a href="mailto:<?= htmlspecialchars($s['Email']) ?>"><?= htmlspecialchars($s['Email']) ?></a></td>
              <td><?= $s['ModuleCount'] ?></td>
              <td>
                <div class="td-actions">
                  <a href="edit_staff.php?id=<?= $s['StaffID'] ?>" class="btn btn-primary btn-sm">Edit</a>
                  <a href="manage_staff.php?delete=<?= $s['StaffID'] ?>"
                     class="btn btn-danger btn-sm"
                     data-confirm="Remove <?= htmlspecialchars(addslashes($s['Name'])) ?> from staff?">
                    Remove
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="5" class="text-center text-muted" style="padding:var(--space-8);">No staff added yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include('../includes/admin_footer.php'); ?>
