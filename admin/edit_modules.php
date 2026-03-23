<?php
include('../config/db.php');
$pageTitle         = 'Edit Module';
$activeSidebarItem = 'modules';
include('../includes/admin_header.php');

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header("Location: manage_modules.php"); exit; }

$msg = '';
$msgType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['ModuleName'] ?? '');
    $desc     = trim($_POST['Description'] ?? '');
    $leaderId = !empty($_POST['ModuleLeaderID']) ? (int)$_POST['ModuleLeaderID'] : null;
    
    if ($name) {
        try {
            $pdo->prepare("UPDATE modules SET ModuleName=?, Description=?, ModuleLeaderID=? WHERE ModuleID=?")
                ->execute([$name, $desc, $leaderId, $id]);
            $msg = 'Module updated successfully.';
        } catch (PDOException $e) {
            $msg = 'Failed to update module. Please try again.';
            $msgType = 'error';
        }
    } else {
        $msg = 'Module name is required.';
        $msgType = 'error';
    }
}

$mod  = $pdo->prepare("
    SELECT m.*, pm.Year
    FROM modules m
    LEFT JOIN programmemodules pm ON pm.ModuleID = m.ModuleID
    WHERE m.ModuleID = ?
    LIMIT 1
");
$mod->execute([$id]);
$mod   = $mod->fetch(PDO::FETCH_ASSOC);
$staff = $pdo->query("SELECT StaffID, Name FROM staff ORDER BY Name")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="admin-topbar">
  <div>
    <nav class="breadcrumb" style="margin-bottom:0.5rem;">
      <span class="breadcrumb__item"><a href="manage_modules.php">Modules</a></span>
      <span class="breadcrumb__separator">›</span>
      <span class="breadcrumb__item breadcrumb__item--current">Edit</span>
    </nav>
    <h1 class="admin-page-title">Edit Module</h1>
  </div>
  <a href="manage_modules.php" class="btn btn-secondary">← Back to Modules</a>
</div>

<?php if ($msg): ?>
  <div class="alert alert--<?= $msgType ?>" role="alert"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="admin-section-card">
  <form method="POST">
    <div class="form-row">
      <div class="form-group">
        <label class="form-label" for="ModuleName">Module Name <span class="form-required">*</span></label>
        <input class="form-input" type="text" id="ModuleName" name="ModuleName" required value="<?= htmlspecialchars($mod['ModuleName']) ?>">
      </div>
      <div class="form-group">
        <label class="form-label" for="ModuleLeaderID">Module Leader</label>
        <select class="form-select" id="ModuleLeaderID" name="ModuleLeaderID">
          <option value="">None</option>
          <?php foreach ($staff as $s): ?>
            <option value="<?= $s['StaffID'] ?>" <?= $mod['ModuleLeaderID'] == $s['StaffID'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($s['Name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label class="form-label" for="Description">Description</label>
      <textarea class="form-textarea" id="Description" name="Description" rows="4"><?= htmlspecialchars($mod['Description'] ?? '') ?></textarea>
    </div>
    <div style="display:flex;gap:var(--space-4);">
      <button type="submit" class="btn btn-primary">Save Changes</button>
      <a href="manage_modules.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>

<?php include('../includes/admin_footer.php'); ?>
