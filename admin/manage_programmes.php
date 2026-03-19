<?php
include('../config/db.php');
$pageTitle         = 'Manage Programmes';
$activeSidebarItem = 'programmes';
include('../includes/admin_header.php');

$msg     = '';
$msgType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['_action'] ?? '';

    if ($action === 'add') {
        $name     = trim($_POST['ProgrammeName']);
        $desc     = trim($_POST['Description']);
        $level    = (int)$_POST['LevelID'];
        $leader   = !empty($_POST['ProgrammeLeaderID']) ? (int)$_POST['ProgrammeLeaderID'] : null;
        $duration = (int)$_POST['Duration'];
        $pub      = isset($_POST['IsPublished']) ? 1 : 0;
        $image    = null;

        // Handle image upload
        if (isset($_FILES['Image']) && $_FILES['Image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            
            if (in_array($_FILES['Image']['type'], $allowedTypes) && $_FILES['Image']['size'] <= $maxSize) {
                $uploadDir = '../uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $extension = pathinfo($_FILES['Image']['name'], PATHINFO_EXTENSION);
                $filename = 'programme_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
                $uploadPath = $uploadDir . $filename;
                
                if (move_uploaded_file($_FILES['Image']['tmp_name'], $uploadPath)) {
                    $image = 'uploads/' . $filename;
                } else {
                    $msg = 'Failed to upload image.';
                    $msgType = 'error';
                }
            } else {
                $msg = 'Invalid image file. Please upload JPG, PNG, GIF, or WebP (max 5MB).';
                $msgType = 'error';
            }
        }

        if ($name && $level && $msgType !== 'error') {
            try {
                $stmt = $pdo->prepare("INSERT INTO programmes (ProgrammeName, Description, LevelID, ProgrammeLeaderID, Duration, Image, IsPublished) VALUES (?,?,?,?,?,?,?)");
                $stmt->execute([$name, $desc, $level, $leader, $duration, $image, $pub]);
                $newId = $pdo->lastInsertId();
                header("Location: edit_programmes.php?id=$newId&msg=created");
                exit;
            } catch (PDOException $e) {
                $msg     = 'Database error: ' . $e->getMessage();
                $msgType = 'error';
            }
        } elseif (!$name || !$level) {
            $msg     = 'Programme name and level are required.';
            $msgType = 'error';
        }
    } elseif ($action === 'toggle') {
        $pid    = (int)$_POST['ProgrammeID'];
        $newPub = (int)$_POST['IsPublished'];
        $pdo->prepare("UPDATE programmes SET IsPublished = ? WHERE ProgrammeID = ?")->execute([$newPub, $pid]);
        $msg = 'Programme visibility updated.';
    }
}

if (isset($_GET['delete'])) {
    $pid = (int)$_GET['delete'];
    try {
        // First delete related records in programmemodules
        $pdo->prepare("DELETE FROM programmemodules WHERE ProgrammeID = ?")->execute([$pid]);
        // Then delete the programme (interestedstudents will cascade automatically)
        $pdo->prepare("DELETE FROM programmes WHERE ProgrammeID = ?")->execute([$pid]);
        $msg = 'Programme deleted successfully.';
    } catch (PDOException $e) {
        $msg = 'Error deleting programme: ' . $e->getMessage();
        $msgType = 'error';
    }
}

$programmes = $pdo->query("
    SELECT p.*, l.LevelName, s.Name AS LeaderName,
           (SELECT COUNT(*) FROM programmemodules pm WHERE pm.ProgrammeID = p.ProgrammeID) AS ModuleCount
    FROM programmes p
    LEFT JOIN levels l ON p.LevelID = l.LevelID
    LEFT JOIN staff s ON p.ProgrammeLeaderID = s.StaffID
    ORDER BY l.LevelName, p.ProgrammeName
")->fetchAll(PDO::FETCH_ASSOC);

$levels = $pdo->query("SELECT * FROM levels ORDER BY LevelName")->fetchAll(PDO::FETCH_ASSOC);
$staff  = $pdo->query("SELECT StaffID, Name FROM staff ORDER BY Name")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="admin-topbar">
  <div>
    <h1 class="admin-page-title">Manage Programmes</h1>
    <p class="admin-page-subtitle">Create, edit, publish, and remove degree programmes. After adding a programme, you will be taken directly to a page where you can add modules.</p>
  </div>
</div>

<?php if ($msg): ?>
  <div class="alert alert--<?= $msgType ?>" role="alert"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<!-- Add Programme -->
<div class="admin-section-card">
  <div class="admin-section-card__header">
    <h2 class="admin-section-card__title">Add New Programme</h2>
    <span style="font-size:var(--text-xs);color:var(--color-text-muted);">After saving, you can add modules on the next page.</span>
  </div>
  <form method="POST" action="" enctype="multipart/form-data">
    <input type="hidden" name="_action" value="add">
    <div class="form-row">
      <div class="form-group">
        <label class="form-label" for="ProgrammeName">Programme Name <span class="form-required">*</span></label>
        <input class="form-input" type="text" id="ProgrammeName" name="ProgrammeName" required
               placeholder="e.g. BSc (Hons) Computer Science">
      </div>
      <div class="form-group">
        <label class="form-label" for="LevelID">Study Level <span class="form-required">*</span></label>
        <select class="form-select" id="LevelID" name="LevelID" required>
          <option value="">Select…</option>
          <?php foreach ($levels as $lvl): ?>
            <option value="<?= $lvl['LevelID'] ?>"><?= htmlspecialchars($lvl['LevelName']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label" for="Duration">Programme Duration</label>
        <select class="form-select" id="Duration" name="Duration">
          <?php for ($y = 1; $y <= 5; $y++): ?>
            <option value="<?= $y ?>" <?= $y === 3 ? 'selected' : '' ?>><?= $y ?> Year<?= $y > 1 ? 's' : '' ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label" for="ProgrammeLeaderID">Programme Leader</label>
        <select class="form-select" id="ProgrammeLeaderID" name="ProgrammeLeaderID">
          <option value="">— Not yet assigned —</option>
          <?php foreach ($staff as $s): ?>
            <option value="<?= $s['StaffID'] ?>"><?= htmlspecialchars($s['Name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="form-label" for="Description">Description</label>
      <textarea class="form-textarea" id="Description" name="Description" rows="3"
                placeholder="Briefly describe the programme aims and career outcomes…"></textarea>
    </div>
    <div class="form-group">
      <label class="form-label" for="Image">Programme Image</label>
      <input class="form-input" type="file" id="Image" name="Image" accept="image/jpeg,image/png,image/gif,image/webp">
      <small class="form-hint">Optional. JPG, PNG, GIF, or WebP. Max 5MB.</small>
    </div>
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:var(--space-4);">
      <label style="display:flex;align-items:center;gap:var(--space-3);cursor:pointer;font-size:var(--text-sm);">
        <input type="checkbox" name="IsPublished" value="1" style="width:18px;height:18px;accent-color:var(--color-primary);">
        <span>Publish immediately (visible to prospective students)</span>
      </label>
      <button type="submit" class="btn btn-primary">Create Programme &amp; Add Modules →</button>
    </div>
  </form>
</div>

<!-- Existing Programmes -->
<div class="admin-section-card">
  <div class="admin-section-card__header">
    <h2 class="admin-section-card__title">All Programmes (<?= count($programmes) ?>)</h2>
    <a href="../public/index.php" class="btn btn-secondary btn-sm" target="_blank">Preview Student Site ↗</a>
  </div>
  <div class="data-table-wrapper">
    <table class="data-table" aria-label="Programmes list">
      <thead>
        <tr>
          <th scope="col">Programme</th>
          <th scope="col">Level</th>
          <th scope="col">Duration</th>
          <th scope="col">Leader</th>
          <th scope="col">Modules</th>
          <th scope="col">Status</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($programmes): ?>
          <?php foreach ($programmes as $p): ?>
            <tr>
              <td>
                <strong><?= htmlspecialchars($p['ProgrammeName']) ?></strong>
              </td>
              <td><?= htmlspecialchars($p['LevelName'] ?? '—') ?></td>
              <td><?= !empty($p['Duration']) ? $p['Duration'] . ' yr' . ($p['Duration'] > 1 ? 's' : '') : '—' ?></td>
              <td><?= htmlspecialchars($p['LeaderName'] ?? '—') ?></td>
              <td style="text-align:center;"><?= (int)$p['ModuleCount'] ?></td>
              <td>
                <span class="status-badge <?= $p['IsPublished'] ? 'status-badge--published' : 'status-badge--draft' ?>">
                  <?= $p['IsPublished'] ? 'Published' : 'Draft' ?>
                </span>
              </td>
              <td>
                <div class="td-actions">
                  <a href="edit_programmes.php?id=<?= $p['ProgrammeID'] ?>" class="btn btn-primary btn-sm">Edit &amp; Modules</a>
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="_action" value="toggle">
                    <input type="hidden" name="ProgrammeID" value="<?= $p['ProgrammeID'] ?>">
                    <input type="hidden" name="IsPublished"  value="<?= $p['IsPublished'] ? 0 : 1 ?>">
                    <button type="submit" class="btn btn-sm <?= $p['IsPublished'] ? 'btn-secondary' : 'btn-success' ?>">
                      <?= $p['IsPublished'] ? 'Unpublish' : 'Publish' ?>
                    </button>
                  </form>
                  <a href="manage_programmes.php?delete=<?= $p['ProgrammeID'] ?>"
                     class="btn btn-danger btn-sm"
                     data-confirm="Delete '<?= htmlspecialchars(addslashes($p['ProgrammeName'])) ?>'? This cannot be undone and will remove all associated student interest records.">
                    Delete
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" style="text-align:center;padding:var(--space-10);color:var(--color-text-muted);">
              No programmes found. Create your first programme above.
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include('../includes/admin_footer.php'); ?>
