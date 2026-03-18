<?php
include('../config/db.php');
$pageTitle         = 'Manage Modules';
$activeSidebarItem = 'modules';
include('../includes/admin_header.php');

$msg     = '';
$msgType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['_action'] ?? '';

    if ($action === 'add') {
        $name     = trim($_POST['ModuleName']);
        $desc     = trim($_POST['Description']);
        $progId   = (int)$_POST['ProgrammeID'];
        $year     = (int)$_POST['Year'];
        $leaderId = !empty($_POST['ModuleLeaderID']) ? (int)$_POST['ModuleLeaderID'] : null;
        $staffId  = !empty($_POST['StaffID'])        ? (int)$_POST['StaffID']        : 1;
        $pub      = isset($_POST['IsPublished']) ? 1 : 0;

        if ($name && $progId) {
            $pdo->beginTransaction();
            $stmtM = $pdo->prepare("INSERT INTO modules (ModuleName, Description, ModuleLeaderID, StaffID, ProgrammeID, IsPublished) VALUES (?,?,?,?,?,?)");
            $stmtM->execute([$name, $desc, $leaderId, $staffId, $progId, $pub]);
            $newModuleId = $pdo->lastInsertId();

            $stmtPM = $pdo->prepare("INSERT INTO programmemodules (ProgrammeID, ModuleID, Year) VALUES (?,?,?)");
            $stmtPM->execute([$progId, $newModuleId, $year ?: null]);
            $pdo->commit();
            $msg = 'Module added and linked to programme.';
        } else {
            $msg     = 'Please fill in all required fields.';
            $msgType = 'error';
        }
    } elseif ($action === 'toggle') {
        $mid    = (int)$_POST['ModuleID'];
        $newPub = (int)$_POST['IsPublished'];
        $pdo->prepare("UPDATE modules SET IsPublished = ? WHERE ModuleID = ?")->execute([$newPub, $mid]);
        $msg = 'Module visibility updated.';
    }
}

if (isset($_GET['delete'])) {
    $mid = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM modules WHERE ModuleID = ?")->execute([$mid]);
    $msg = 'Module deleted.';
}

$modules = $pdo->query("
    SELECT m.*, s.Name AS LeaderName, p.ProgrammeName, pm.Year
    FROM modules m
    LEFT JOIN staff s ON m.ModuleLeaderID = s.StaffID
    LEFT JOIN programmes p ON m.ProgrammeID = p.ProgrammeID
    LEFT JOIN programmemodules pm ON pm.ModuleID = m.ModuleID AND pm.ProgrammeID = m.ProgrammeID
    GROUP BY m.ModuleID
    ORDER BY p.ProgrammeName, pm.Year, m.ModuleName
")->fetchAll(PDO::FETCH_ASSOC);

$programmes = $pdo->query("SELECT ProgrammeID, ProgrammeName FROM programmes ORDER BY ProgrammeName")->fetchAll(PDO::FETCH_ASSOC);
$staff      = $pdo->query("SELECT StaffID, Title, Name FROM staff ORDER BY Name")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="admin-topbar">
  <div>
    <h1 class="admin-page-title">Manage Modules</h1>
    <p class="admin-page-subtitle">Add, edit, and link modules to degree programmes.</p>
  </div>
</div>

<?php if ($msg): ?>
  <div class="alert alert--<?= $msgType ?>" role="alert"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<!-- Add Module -->
<div class="admin-section-card">
  <div class="admin-section-card__header">
    <h2 class="admin-section-card__title">Add New Module</h2>
  </div>
  <form method="POST" action="">
    <input type="hidden" name="_action" value="add">
    <div class="form-row">
      <div class="form-group">
        <label class="form-label" for="ModuleName">Module Name <span class="form-required">*</span></label>
        <input class="form-input" type="text" id="ModuleName" name="ModuleName" required placeholder="e.g. Algorithms and Data Structures">
      </div>
      <div class="form-group">
        <label class="form-label" for="ProgrammeID">Programme <span class="form-required">*</span></label>
        <select class="form-select" id="ProgrammeID" name="ProgrammeID" required>
          <option value="">Select programme…</option>
          <?php foreach ($programmes as $p): ?>
            <option value="<?= $p['ProgrammeID'] ?>"><?= htmlspecialchars($p['ProgrammeName']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label class="form-label" for="ModuleLeaderID">Module Leader</label>
        <select class="form-select" id="ModuleLeaderID" name="ModuleLeaderID">
          <option value="">None assigned</option>
          <?php foreach ($staff as $s): ?>
            <option value="<?= $s['StaffID'] ?>"><?= htmlspecialchars($s['Title'] . ' ' . $s['Name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label" for="Year">Year of Study</label>
        <select class="form-select" id="Year" name="Year">
          <option value="">Not specified</option>
          <option value="1">Year 1</option>
          <option value="2">Year 2</option>
          <option value="3">Year 3</option>
          <option value="4">Year 4</option>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="form-label" for="ModuleDescription">Description</label>
      <textarea class="form-textarea" id="ModuleDescription" name="Description" rows="3" placeholder="Describe what this module covers…"></textarea>
    </div>
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:var(--space-4);">
      <label style="display:flex;align-items:center;gap:var(--space-3);cursor:pointer;font-size:var(--text-sm);">
        <input type="checkbox" name="IsPublished" value="1" style="width:18px;height:18px;accent-color:var(--color-primary);">
        <span>Publish immediately</span>
      </label>
      <button type="submit" class="btn btn-primary">Add Module</button>
    </div>
  </form>
</div>

<!-- Modules Table -->
<div class="admin-section-card">
  <div class="admin-section-card__header">
    <h2 class="admin-section-card__title">All Modules (<?= count($modules) ?>)</h2>
  </div>
  <div class="data-table-wrapper">
    <table class="data-table" aria-label="Modules list">
      <thead>
        <tr>
          <th scope="col">Module Name</th>
          <th scope="col">Programme</th>
          <th scope="col">Year</th>
          <th scope="col">Leader</th>
          <th scope="col">Status</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($modules): ?>
          <?php foreach ($modules as $m): ?>
            <tr>
              <td><strong><?= htmlspecialchars($m['ModuleName']) ?></strong></td>
              <td><?= htmlspecialchars($m['ProgrammeName'] ?? '—') ?></td>
              <td><?= !empty($m['Year']) ? 'Year ' . htmlspecialchars($m['Year']) : '—' ?></td>
              <td><?= htmlspecialchars($m['LeaderName'] ?? '—') ?></td>
              <td>
                <span class="status-badge <?= $m['IsPublished'] ? 'status-badge--published' : 'status-badge--draft' ?>">
                  <?= $m['IsPublished'] ? 'Published' : 'Draft' ?>
                </span>
              </td>
              <td>
                <div class="td-actions">
                  <a href="edit_modules.php?id=<?= $m['ModuleID'] ?>" class="btn btn-primary btn-sm">Edit</a>
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="_action" value="toggle">
                    <input type="hidden" name="ModuleID" value="<?= $m['ModuleID'] ?>">
                    <input type="hidden" name="IsPublished" value="<?= $m['IsPublished'] ? 0 : 1 ?>">
                    <button type="submit" class="btn btn-sm <?= $m['IsPublished'] ? 'btn-secondary' : 'btn-success' ?>">
                      <?= $m['IsPublished'] ? 'Unpublish' : 'Publish' ?>
                    </button>
                  </form>
                  <a href="manage_modules.php?delete=<?= $m['ModuleID'] ?>"
                     class="btn btn-danger btn-sm"
                     data-confirm="Delete '<?= htmlspecialchars(addslashes($m['ModuleName'])) ?>'?">
                    Delete
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="6" class="text-center text-muted" style="padding:var(--space-8);">No modules yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include('../includes/admin_footer.php'); ?>
