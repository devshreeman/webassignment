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
        $leaderId = !empty($_POST['ModuleLeaderID']) ? (int)$_POST['ModuleLeaderID'] : null;

        if ($name) {
            try {
                $stmtM = $pdo->prepare("INSERT INTO modules (ModuleName, Description, ModuleLeaderID) VALUES (?,?,?)");
                $stmtM->execute([$name, $desc, $leaderId]);
                $msg = 'Module created successfully.';
            } catch (PDOException $e) {
                $msg = 'Failed to create module: ' . $e->getMessage();
                $msgType = 'error';
            }
        } else {
            $msg     = 'Module name is required.';
            $msgType = 'error';
        }
    }
}

/* Handle Module Deletion */
if (isset($_GET['delete'])) {
    $mid = (int)$_GET['delete'];
    try {
        $pdo->prepare("DELETE FROM programmemodules WHERE ModuleID = ?")->execute([$mid]);
        $pdo->prepare("DELETE FROM modules WHERE ModuleID = ?")->execute([$mid]);
        $msg = 'Module deleted successfully.';
    } catch (PDOException $e) {
        $msg = 'Failed to delete module. Please try again.';
        $msgType = 'error';
    }
}

$modules = $pdo->query("
    SELECT m.*, s.Name AS LeaderName, 
           GROUP_CONCAT(DISTINCT p.ProgrammeName SEPARATOR ', ') AS ProgrammeName,
           MIN(pm.Year) AS Year
    FROM modules m
    LEFT JOIN staff s ON m.ModuleLeaderID = s.StaffID
    LEFT JOIN programmemodules pm ON pm.ModuleID = m.ModuleID
    LEFT JOIN programmes p ON pm.ProgrammeID = p.ProgrammeID
    GROUP BY m.ModuleID
    ORDER BY m.ModuleName
")->fetchAll(PDO::FETCH_ASSOC);

$programmes = $pdo->query("SELECT ProgrammeID, ProgrammeName FROM programmes ORDER BY ProgrammeName")->fetchAll(PDO::FETCH_ASSOC);
$staff      = $pdo->query("SELECT StaffID, Name FROM staff ORDER BY Name")->fetchAll(PDO::FETCH_ASSOC);
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
      <div class="form-group" style="flex:1;">
        <label class="form-label" for="ModuleName">Module Name <span class="form-required">*</span></label>
        <input class="form-input" type="text" id="ModuleName" name="ModuleName" required placeholder="e.g. Algorithms and Data Structures">
      </div>
      <div class="form-group" style="flex:1;">
        <label class="form-label" for="ModuleLeaderID">Module Leader</label>
        <select class="form-select" id="ModuleLeaderID" name="ModuleLeaderID">
          <option value="">None assigned</option>
          <?php foreach ($staff as $s): ?>
            <option value="<?= $s['StaffID'] ?>"><?= htmlspecialchars($s['Name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="form-label" for="ModuleDescription">Description</label>
      <textarea class="form-textarea" id="ModuleDescription" name="Description" rows="3" placeholder="Describe what this module covers…"></textarea>
    </div>
    <div style="display:flex;justify-content:flex-end;">
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
                <div class="td-actions">
                  <a href="edit_modules.php?id=<?= $m['ModuleID'] ?>" class="btn btn-primary btn-sm">Edit</a>
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
          <tr><td colspan="5" class="text-center text-muted" style="padding:var(--space-8);">No modules yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include('../includes/admin_footer.php'); ?>
