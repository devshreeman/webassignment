<?php
include('../config/db.php');
$pageTitle         = 'Manage Staff';
$activeSidebarItem = 'staff';
include('../includes/admin_header.php');

$msg     = '';
$msgType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['Name']);
    $email = trim($_POST['Email']);
    $bio   = trim($_POST['Bio']);
    $pass  = trim($_POST['Password'] ?? '');
    
    /* Handle Photo Upload */
    $photoPath = '';
    if (isset($_FILES['Photo']) && $_FILES['Photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['Photo']['tmp_name'];
        $fileName = $_FILES['Photo']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $newFileName = 'staff_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $fileExtension;
        $destPath = '../uploads/' . $newFileName;
        
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $photoPath = 'uploads/' . $newFileName;
        }
    }

    if ($name && $email) {
        try {
            $hash = $pass ? password_hash($pass, PASSWORD_DEFAULT) : null;
            $stmt = $pdo->prepare("INSERT INTO staff (Name, Email, Bio, Photo, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $bio, $photoPath, $hash]);
            $msg = 'Staff member added successfully.';
        } catch (PDOException $e) {
            $msg     = 'Failed to add staff member. Please try again.';
            $msgType = 'error';
        }
    } else {
        $msg     = 'Name and email are required.';
        $msgType = 'error';
    }
}

/* Handle Staff Deletion */
if (isset($_GET['delete'])) {
    $sid = (int)$_GET['delete'];
    try {
        $progCount = $pdo->prepare("SELECT COUNT(*) FROM programmes WHERE ProgrammeLeaderID = ?");
        $progCount->execute([$sid]);
        $progRefs = $progCount->fetchColumn();
        
        $modCount = $pdo->prepare("SELECT COUNT(*) FROM modules WHERE ModuleLeaderID = ?");
        $modCount->execute([$sid]);
        $modRefs = $modCount->fetchColumn();
        
        if ($progRefs > 0 || $modRefs > 0) {
            $msg = "Cannot delete staff member who is assigned as programme or module leader. Please reassign their responsibilities first.";
            $msgType = 'error';
        } else {
            $pdo->prepare("DELETE FROM staff WHERE StaffID = ?")->execute([$sid]);
            $msg = 'Staff member removed successfully.';
        }
    } catch (PDOException $e) {
        $msg = 'Failed to delete staff member. Please try again.';
        $msgType = 'error';
    }
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
  <form method="POST" action="" enctype="multipart/form-data">
    <div class="form-row">
      <div class="form-group">
        <label class="form-label" for="Name">Full Name <span class="form-required">*</span></label>
        <input class="form-input" type="text" id="Name" name="Name" required placeholder="e.g. Sarah Johnson">
      </div>
      <div class="form-group">
        <label class="form-label" for="StaffEmail">Email <span class="form-required">*</span></label>
        <input class="form-input" type="email" id="StaffEmail" name="Email" required placeholder="s.johnson@university.ac.uk">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label" for="Photo">Staff Photo</label>
        <input class="form-input" type="file" id="Photo" name="Photo" accept="image/*">
        <p class="form-hint">Upload a professional headshot (JPG, PNG).</p>
      </div>
      <div class="form-group">
        <label class="form-label" for="Password">Set Password (Optional)</label>
        <input class="form-input" type="password" id="Password" name="Password" placeholder="••••••••">
        <p class="form-hint">If set, the staff member can use this to sign into the Staff Portal.</p>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group" style="flex:1;">
        <label class="form-label" for="Bio">Biography</label>
        <textarea class="form-textarea" id="Bio" name="Bio" rows="3" placeholder="Brief biography and research areas…"></textarea>
      </div>
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
                      <img src="<?= '../' . htmlspecialchars($s['Photo']) ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
                    <?php else: ?>
                      <?= strtoupper(substr($s['Name'], 0, 2)) ?>
                    <?php endif; ?>
                  </div>
                  <strong><?= htmlspecialchars($s['Name']) ?></strong>
                </div>
              </td>
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
