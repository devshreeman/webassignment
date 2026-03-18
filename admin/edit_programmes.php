<?php
/**
 * Unified Programme Editor — details + modules on one page.
 * URL: edit_programmes.php?id=N
 */
include('../config/db.php');
$pageTitle         = 'Edit Programme';
$activeSidebarItem = 'programmes';
include('../includes/admin_header.php');

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header("Location: manage_programmes.php"); exit; }

$msg     = '';
$msgType = 'success';

/* ── Handle Programme Update ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_action'] ?? '') === 'update_programme') {
    $name     = trim($_POST['ProgrammeName']);
    $desc     = trim($_POST['Description']);
    $level    = (int)$_POST['LevelID'];
    $leader   = !empty($_POST['ProgrammeLeaderID']) ? (int)$_POST['ProgrammeLeaderID'] : null;
    $duration = (int)$_POST['Duration'];
    $pub      = isset($_POST['IsPublished']) ? 1 : 0;

    if ($name && $level) {
        $pdo->prepare("UPDATE programmes SET ProgrammeName=?, Description=?, LevelID=?, ProgrammeLeaderID=?, Duration=?, IsPublished=? WHERE ProgrammeID=?")
            ->execute([$name, $desc, $level, $leader, $duration, $pub, $id]);
        $msg = 'Programme details saved successfully.';
    } else {
        $msg     = 'Programme name and level are required.';
        $msgType = 'error';
    }
}

/* ── Handle Add Module to this Programme ── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_action'] ?? '') === 'add_module') {
    $modName  = trim($_POST['ModuleName'] ?? '');
    $modDesc  = trim($_POST['ModuleDesc'] ?? '');
    $year     = !empty($_POST['Year']) ? (int)$_POST['Year'] : null;
    $leaderId = !empty($_POST['ModuleLeaderID']) ? (int)$_POST['ModuleLeaderID'] : null;
    $pub      = isset($_POST['ModulePublished']) ? 1 : 0;

    if ($modName) {
        try {
            $pdo->beginTransaction();
            $pdo->prepare("INSERT INTO modules (ModuleName, Description, ModuleLeaderID, StaffID, ProgrammeID, IsPublished) VALUES (?,?,?,?,?,?)")
                ->execute([$modName, $modDesc, $leaderId, $leaderId ?? 1, $id, $pub]);
            $newMid = $pdo->lastInsertId();
            $pdo->prepare("INSERT INTO programmemodules (ProgrammeID, ModuleID, Year) VALUES (?,?,?)")
                ->execute([$id, $newMid, $year]);
            $pdo->commit();
            $msg = 'Module added to programme.';
        } catch (PDOException $e) {
            $pdo->rollBack();
            $msg     = 'Could not add module. Please try again.';
            $msgType = 'error';
        }
    } else {
        $msg     = 'Module name is required.';
        $msgType = 'error';
    }
}

/* ── Handle Remove Module from Programme ── */
if (isset($_GET['remove_module'])) {
    $mid = (int)$_GET['remove_module'];
    $pdo->prepare("DELETE FROM programmemodules WHERE ProgrammeID=? AND ModuleID=?")->execute([$id, $mid]);
    $pdo->prepare("DELETE FROM modules WHERE ModuleID=?")->execute([$mid]);
    header("Location: edit_programmes.php?id=$id&msg=module_removed");
    exit;
}

if (isset($_GET['msg'])) {
    $msgs = [
        'module_removed' => 'Module removed from programme.',
        'created'        => 'Programme created successfully. You can now add modules below.',
    ];
    $msg     = $msgs[$_GET['msg']] ?? '';
    $msgType = 'success';
}

/* ── Fetch Programme Data ── */
$progStmt = $pdo->prepare("SELECT p.*, l.LevelName FROM programmes p LEFT JOIN levels l ON p.LevelID = l.LevelID WHERE p.ProgrammeID = ?");
$progStmt->execute([$id]);
$prog = $progStmt->fetch(PDO::FETCH_ASSOC);
if (!$prog) { header("Location: manage_programmes.php"); exit; }

/* ── Fetch Modules (grouped by year) ── */
$modStmt = $pdo->prepare("
    SELECT m.ModuleID, m.ModuleName, m.Description, m.IsPublished, pm.Year,
           s.Name AS LeaderName, s.Title AS LeaderTitle
    FROM programmemodules pm
    JOIN modules m ON pm.ModuleID = m.ModuleID
    LEFT JOIN staff s ON m.ModuleLeaderID = s.StaffID
    WHERE pm.ProgrammeID = ?
    ORDER BY pm.Year ASC, m.ModuleName ASC
");
$modStmt->execute([$id]);
$modules = $modStmt->fetchAll(PDO::FETCH_ASSOC);

$modulesByYear = [];
foreach ($modules as $m) {
    $y = $m['Year'] ?? 'Unassigned';
    $modulesByYear[$y][] = $m;
}
ksort($modulesByYear);

/* ── Reference Data ── */
$levels = $pdo->query("SELECT * FROM levels ORDER BY LevelName")->fetchAll(PDO::FETCH_ASSOC);
$staff  = $pdo->query("SELECT StaffID, Title, Name FROM staff ORDER BY Name")->fetchAll(PDO::FETCH_ASSOC);
$duration = (int)($prog['Duration'] ?? 3);
?>

<!-- Page Header -->
<div class="admin-topbar">
  <div>
    <nav class="breadcrumb" aria-label="Breadcrumb" style="margin-bottom:0.4rem;">
      <span class="breadcrumb__item"><a href="manage_programmes.php">Programmes</a></span>
      <span class="breadcrumb__separator" aria-hidden="true">›</span>
      <span class="breadcrumb__item breadcrumb__item--current"><?= htmlspecialchars($prog['ProgrammeName']) ?></span>
    </nav>
    <h1 class="admin-page-title"><?= htmlspecialchars($prog['ProgrammeName']) ?></h1>
    <p class="admin-page-subtitle">
      <?= htmlspecialchars($prog['LevelName'] ?? '') ?>
      &nbsp;·&nbsp; <?= $duration ?> Year<?= $duration !== 1 ? 's' : '' ?>
      &nbsp;·&nbsp;
      <span class="status-badge <?= $prog['IsPublished'] ? 'status-badge--published' : 'status-badge--draft' ?>">
        <?= $prog['IsPublished'] ? 'Published' : 'Draft' ?>
      </span>
    </p>
  </div>
  <div style="display:flex;gap:var(--space-3);">
    <?php if ($prog['IsPublished']): ?>
      <a href="../public/programme.php?id=<?= $id ?>" class="btn btn-secondary btn-sm" target="_blank">Preview ↗</a>
    <?php endif; ?>
    <a href="manage_programmes.php" class="btn btn-secondary">← All Programmes</a>
  </div>
</div>

<?php if ($msg): ?>
  <div class="alert alert--<?= $msgType ?>" role="alert"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-6);align-items:start;">

  <!-- ═══ LEFT: Programme Details ═══ -->
  <div class="admin-section-card">
    <div class="admin-section-card__header">
      <h2 class="admin-section-card__title">Programme Details</h2>
    </div>
    <form method="POST" action="">
      <input type="hidden" name="_action" value="update_programme">

      <div class="form-group">
        <label class="form-label" for="ProgrammeName">Programme Name <span class="form-required">*</span></label>
        <input class="form-input" type="text" id="ProgrammeName" name="ProgrammeName" required
               value="<?= htmlspecialchars($prog['ProgrammeName']) ?>">
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="LevelID">Study Level <span class="form-required">*</span></label>
          <select class="form-select" id="LevelID" name="LevelID" required>
            <?php foreach ($levels as $lvl): ?>
              <option value="<?= $lvl['LevelID'] ?>" <?= $prog['LevelID'] == $lvl['LevelID'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($lvl['LevelName']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label" for="Duration">Programme Duration</label>
          <select class="form-select" id="Duration" name="Duration">
            <?php for ($y = 1; $y <= 5; $y++): ?>
              <option value="<?= $y ?>" <?= $duration === $y ? 'selected' : '' ?>>
                <?= $y ?> Year<?= $y > 1 ? 's' : '' ?>
              </option>
            <?php endfor; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="ProgrammeLeaderID">Programme Leader</label>
        <select class="form-select" id="ProgrammeLeaderID" name="ProgrammeLeaderID">
          <option value="">— Not assigned —</option>
          <?php foreach ($staff as $s): ?>
            <option value="<?= $s['StaffID'] ?>" <?= $prog['ProgrammeLeaderID'] == $s['StaffID'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($s['Title'] . ' ' . $s['Name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label" for="Description">Description</label>
        <textarea class="form-textarea" id="Description" name="Description" rows="5"><?= htmlspecialchars($prog['Description'] ?? '') ?></textarea>
      </div>

      <div style="display:flex;align-items:center;justify-content:space-between;margin-top:var(--space-2);flex-wrap:wrap;gap:var(--space-4);">
        <label style="display:flex;align-items:center;gap:var(--space-3);cursor:pointer;font-size:var(--text-sm);">
          <input type="checkbox" name="IsPublished" id="IsPublished" value="1"
                 <?= $prog['IsPublished'] ? 'checked' : '' ?>
                 style="width:18px;height:18px;accent-color:var(--color-primary);">
          <span>Published (visible to students)</span>
        </label>
        <button type="submit" class="btn btn-primary">Save Details</button>
      </div>
    </form>
  </div>

  <!-- ═══ RIGHT: Add Module ═══ -->
  <div class="admin-section-card">
    <div class="admin-section-card__header">
      <h2 class="admin-section-card__title">Add Module to Programme</h2>
    </div>
    <form method="POST" action="">
      <input type="hidden" name="_action" value="add_module">

      <div class="form-group">
        <label class="form-label" for="ModuleName">Module Name <span class="form-required">*</span></label>
        <input class="form-input" type="text" id="ModuleName" name="ModuleName" required
               placeholder="e.g. Introduction to Networking">
      </div>

      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="Year">Year of Study</label>
          <select class="form-select" id="Year" name="Year">
            <option value="">— Not specified —</option>
            <?php for ($y = 1; $y <= $duration; $y++): ?>
              <option value="<?= $y ?>">Year <?= $y ?></option>
            <?php endfor; ?>
          </select>
          <small class="form-hint">Years shown match the programme duration (<?= $duration ?> year<?= $duration !== 1 ? 's' : '' ?>).</small>
        </div>
        <div class="form-group">
          <label class="form-label" for="ModuleLeaderID">Module Leader</label>
          <select class="form-select" id="ModuleLeaderID" name="ModuleLeaderID">
            <option value="">— Not assigned —</option>
            <?php foreach ($staff as $s): ?>
              <option value="<?= $s['StaffID'] ?>"><?= htmlspecialchars($s['Title'] . ' ' . $s['Name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="ModuleDesc">Description</label>
        <textarea class="form-textarea" id="ModuleDesc" name="ModuleDesc" rows="3"
                  placeholder="Briefly describe what this module covers…"></textarea>
      </div>

      <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:var(--space-4);">
        <label style="display:flex;align-items:center;gap:var(--space-3);cursor:pointer;font-size:var(--text-sm);">
          <input type="checkbox" name="ModulePublished" id="ModulePublished" value="1" checked
                 style="width:18px;height:18px;accent-color:var(--color-primary);">
          <span>Publish immediately</span>
        </label>
        <button type="submit" class="btn btn-success">+ Add Module</button>
      </div>
    </form>
  </div>
</div>

<!-- ═══ Full-Width: Module List ═══ -->
<div class="admin-section-card" style="margin-top:var(--space-6);">
  <div class="admin-section-card__header">
    <h2 class="admin-section-card__title">
      Modules (<?= count($modules) ?>)
      <?php if ($duration): ?>
        <span style="font-size:var(--text-sm);font-weight:400;color:var(--color-text-muted);">— <?= $duration ?>-year programme</span>
      <?php endif; ?>
    </h2>
    <a href="manage_modules.php" class="btn btn-secondary btn-sm">Manage All Modules</a>
  </div>

  <?php if ($modulesByYear): ?>
    <?php foreach ($modulesByYear as $year => $yearMods): ?>
      <div style="margin-bottom:var(--space-6);">
        <h3 style="font-size:var(--text-xs);font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:var(--color-text-muted);margin-bottom:var(--space-3);display:flex;align-items:center;gap:var(--space-3);">
          <span style="display:inline-block;width:3px;height:1em;background:var(--color-accent);border-radius:2px;"></span>
          <?= is_numeric($year) ? 'Year ' . $year : htmlspecialchars($year) ?>
          <span style="font-weight:400;">(<?= count($yearMods) ?> module<?= count($yearMods) !== 1 ? 's' : '' ?>)</span>
        </h3>
        <div class="data-table-wrapper">
          <table class="data-table" aria-label="Year <?= is_numeric($year) ? $year : htmlspecialchars($year) ?> modules">
            <thead>
              <tr>
                <th scope="col">Module Name</th>
                <th scope="col">Leader</th>
                <th scope="col">Status</th>
                <th scope="col">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($yearMods as $m): ?>
                <tr>
                  <td>
                    <strong><?= htmlspecialchars($m['ModuleName']) ?></strong>
                    <?php if (!empty($m['Description'])): ?>
                      <div style="font-size:var(--text-xs);color:var(--color-text-muted);margin-top:2px;">
                        <?= htmlspecialchars(mb_substr($m['Description'], 0, 80)) ?><?= strlen($m['Description']) > 80 ? '…' : '' ?>
                      </div>
                    <?php endif; ?>
                  </td>
                  <td><?= !empty($m['LeaderName']) ? htmlspecialchars($m['LeaderTitle'] . ' ' . $m['LeaderName']) : '<span style="color:var(--color-text-muted)">—</span>' ?></td>
                  <td>
                    <span class="status-badge <?= $m['IsPublished'] ? 'status-badge--published' : 'status-badge--draft' ?>">
                      <?= $m['IsPublished'] ? 'Published' : 'Draft' ?>
                    </span>
                  </td>
                  <td>
                    <div class="td-actions">
                      <a href="edit_modules.php?id=<?= $m['ModuleID'] ?>" class="btn btn-primary btn-sm">Edit</a>
                      <a href="edit_programmes.php?id=<?= $id ?>&remove_module=<?= $m['ModuleID'] ?>"
                         class="btn btn-danger btn-sm"
                         data-confirm="Remove '<?= htmlspecialchars(addslashes($m['ModuleName'])) ?>' from this programme? The module will also be deleted.">
                        Remove
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="empty-state" style="padding:var(--space-10) 0;">
      <div class="empty-state__icon" aria-hidden="true">📖</div>
      <h3 class="empty-state__title">No modules yet</h3>
      <p>Use the form above to add the first module to this programme.</p>
    </div>
  <?php endif; ?>
</div>

<?php include('../includes/admin_footer.php'); ?>
