<?php
include('../config/db.php');
$pageTitle         = 'Student Interest Registrations';
$activeSidebarItem = 'interests';
include('../includes/admin_header.php');

$msg = '';

// Delete single interest
if (isset($_GET['delete'])) {
    $iid = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM interestedstudents WHERE InterestID = ?")->execute([$iid]);
    $msg = 'Registration removed.';
}

// Filter by programme
$filterProg = isset($_GET['prog']) ? (int)$_GET['prog'] : 0;

$params = [];
$where  = '';
if ($filterProg > 0) {
    $where  = 'WHERE si.ProgrammeID = ?';
    $params[] = $filterProg;
}

$interests = $pdo->prepare("
    SELECT si.InterestID, si.StudentName, si.Email, si.RegisteredAt,
           p.ProgrammeName, p.ProgrammeID
    FROM interestedstudents si
    JOIN programmes p ON si.ProgrammeID = p.ProgrammeID
    $where
    ORDER BY si.RegisteredAt DESC
");
$interests->execute($params);
$interests = $interests->fetchAll(PDO::FETCH_ASSOC);

$programmes = $pdo->query("SELECT ProgrammeID, ProgrammeName FROM programmes ORDER BY ProgrammeName")->fetchAll(PDO::FETCH_ASSOC);

// CSV Export
if (isset($_GET['export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="mailing_list_' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Name', 'Email', 'Programme', 'Registered At']);
    foreach ($interests as $i) {
        fputcsv($out, [$i['StudentName'], $i['Email'], $i['ProgrammeName'], $i['RegisteredAt']]);
    }
    fclose($out);
    exit;
}
?>

<div class="admin-topbar">
  <div>
    <h1 class="admin-page-title">Student Interest Registrations</h1>
    <p class="admin-page-subtitle">View and export the mailing list of prospective students.</p>
  </div>
  <div style="display:flex;gap:var(--space-3);">
    <a href="manage_interests.php<?= $filterProg ? "?prog=$filterProg&" : '?' ?>export=1"
       class="btn btn-success">↓ Export CSV</a>
  </div>
</div>

<?php if ($msg): ?>
  <div class="alert alert--success" role="alert"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<!-- Filter -->
<div class="admin-section-card" style="padding:var(--space-4) var(--space-6);">
  <form method="GET" action="" style="display:flex;align-items:flex-end;gap:var(--space-4);flex-wrap:wrap;">
    <div class="filter-bar__group">
      <label class="filter-bar__label" for="prog">Filter by Programme</label>
      <select class="filter-bar__select" id="prog" name="prog" style="min-width:240px;">
        <option value="0" <?= $filterProg === 0 ? 'selected' : '' ?>>All Programmes</option>
        <?php foreach ($programmes as $p): ?>
          <option value="<?= $p['ProgrammeID'] ?>" <?= $filterProg === (int)$p['ProgrammeID'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($p['ProgrammeName']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="btn btn-primary">Filter</button>
    <?php if ($filterProg): ?>
      <a href="manage_interests.php" class="btn btn-secondary btn-sm">Clear</a>
    <?php endif; ?>
  </form>
</div>

<!-- Interests Table -->
<div class="admin-section-card">
  <div class="admin-section-card__header">
    <h2 class="admin-section-card__title">
      Registrations (<?= count($interests) ?>)
      <?php if ($filterProg): ?>
        <span style="font-size:var(--text-sm);font-weight:400;color:var(--color-text-muted);">
          — <?= htmlspecialchars(array_column($programmes, 'ProgrammeName', 'ProgrammeID')[$filterProg] ?? '') ?>
        </span>
      <?php endif; ?>
    </h2>
  </div>
  <div class="data-table-wrapper">
    <table class="data-table" aria-label="Student interest registrations">
      <thead>
        <tr>
          <th scope="col">Name</th>
          <th scope="col">Email</th>
          <th scope="col">Programme</th>
          <th scope="col">Registered</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($interests): ?>
          <?php foreach ($interests as $i): ?>
            <tr>
              <td><strong><?= htmlspecialchars($i['StudentName']) ?></strong></td>
              <td><a href="mailto:<?= htmlspecialchars($i['Email']) ?>"><?= htmlspecialchars($i['Email']) ?></a></td>
              <td><?= htmlspecialchars($i['ProgrammeName']) ?></td>
              <td style="white-space:nowrap;color:var(--color-text-muted);"><?= date('d M Y, H:i', strtotime($i['RegisteredAt'])) ?></td>
              <td>
                <a href="manage_interests.php?delete=<?= $i['InterestID'] ?><?= $filterProg ? "&prog=$filterProg" : '' ?>"
                   class="btn btn-danger btn-sm"
                   data-confirm="Remove registration for <?= htmlspecialchars(addslashes($i['StudentName'])) ?>?">
                  Remove
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="5" class="text-center text-muted" style="padding:var(--space-8);">No registrations found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include('../includes/admin_footer.php'); ?>
