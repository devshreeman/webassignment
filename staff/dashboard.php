<?php
include('../config/db.php');

if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['staff'])) {
    header("Location: login.php");
    exit;
}

$staff = $_SESSION['staff'];

// page setup
$pageTitle  = 'Staff Dashboard';
$activePage = 'staff-dashboard';
$cssBase    = '../';
$rootBase   = '../';

// get modules led by this staff member
$stmtModules = $pdo->prepare("
    SELECT m.ModuleID, m.ModuleName, m.Description, 
           GROUP_CONCAT(DISTINCT p.ProgrammeName SEPARATOR ', ') AS ProgrammeName
    FROM modules m
    LEFT JOIN programmemodules pm ON m.ModuleID = pm.ModuleID
    LEFT JOIN programmes p ON pm.ProgrammeID = p.ProgrammeID
    WHERE m.ModuleLeaderID = ?
    GROUP BY m.ModuleID
    ORDER BY m.ModuleName
");
$stmtModules->execute([$staff['StaffID']]);
$ledModules = $stmtModules->fetchAll(PDO::FETCH_ASSOC);

// get programmes that contain this staff's modules
$stmtProgrammes = $pdo->prepare("
    SELECT DISTINCT p.ProgrammeID, p.ProgrammeName, l.LevelName,
           (SELECT COUNT(*) FROM programmemodules pm2 WHERE pm2.ProgrammeID = p.ProgrammeID) as TotalModules
    FROM programmemodules pm
    JOIN modules m ON pm.ModuleID = m.ModuleID
    JOIN programmes p ON pm.ProgrammeID = p.ProgrammeID
    JOIN levels l ON p.LevelID = l.LevelID
    WHERE m.ModuleLeaderID = ?
    ORDER BY p.ProgrammeName
");
$stmtProgrammes->execute([$staff['StaffID']]);
$programmes = $stmtProgrammes->fetchAll(PDO::FETCH_ASSOC);

include('../includes/header.php');
?>

<section style="background: var(--color-surface); border-bottom: 1px solid var(--color-border); padding: var(--space-8) 0;">
  <div class="container">
    <div class="admin-topbar" style="margin-bottom: 0;">
      <div>
        <h1 class="admin-page-title" style="margin-bottom: var(--space-1);">Welcome, <?= htmlspecialchars($staff['Name']) ?></h1>
        <p class="admin-page-subtitle">Staff Portal — View your teaching responsibilities and related programmes.</p>
      </div>
      <div style="display:flex; gap:var(--space-3); flex-wrap:wrap;">
        <a href="interested_students.php" class="btn btn-primary">Interested Students</a>
        <a href="settings.php" class="btn btn-secondary">Profile Settings</a>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid grid-2" style="align-items:start;">
      
      <!-- Modules Led Column -->
      <div>
        <div class="section-header section-header--left" style="margin-bottom:var(--space-6);">
          <h2 class="section-header__title" style="font-size:var(--text-2xl);">Modules You Lead</h2>
          <div class="section-divider"></div>
        </div>

        <?php if ($ledModules): ?>
          <div style="display:flex; flex-direction:column; gap:var(--space-4);">
            <?php foreach ($ledModules as $m): ?>
              <div class="module-card" style="margin-bottom:0;">
                <h3 class="module-card__title"><?= htmlspecialchars($m['ModuleName']) ?></h3>
                <span class="module-card__year-tag">
                  <?= htmlspecialchars($m['ProgrammeName'] ?? 'Unassigned Programme') ?>
                </span>
                <p class="module-card__description" style="margin-bottom:0;">
                  <?= htmlspecialchars(mb_substr($m['Description'] ?? 'No description provided.', 0, 120)) ?>...
                </p>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="empty-state" style="padding:var(--space-8); border:1px solid var(--color-border); border-radius:var(--radius-lg);">
            <div class="empty-state__icon" aria-hidden="true">📚</div>
            <h3 class="empty-state__title">No modules assigned</h3>
            <p>You are not currently listed as the module leader for any modules.</p>
          </div>
        <?php endif; ?>
      </div>

      <!-- Programmes Column -->
      <div>
        <div class="section-header section-header--left" style="margin-bottom:var(--space-6);">
          <h2 class="section-header__title" style="font-size:var(--text-2xl);">Related Programmes</h2>
          <div class="section-divider"></div>
        </div>
        <p class="text-muted mb-6">Programmes that include the modules you teach.</p>

        <?php if ($programmes): ?>
          <div class="data-table-wrapper" style="box-shadow:none;">
            <table class="data-table" aria-label="Related Programmes">
              <thead>
                <tr>
                  <th scope="col">Programme</th>
                  <th scope="col">Level</th>
                  <th scope="col">Total Modules</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($programmes as $p): ?>
                  <tr>
                    <td>
                      <a href="../public/programme.php?id=<?= $p['ProgrammeID'] ?>" style="font-weight:500;">
                        <?= htmlspecialchars($p['ProgrammeName']) ?>
                      </a>
                    </td>
                    <td>
                      <span class="status-badge" style="background:var(--color-bg); border:1px solid var(--color-border); color:var(--color-text-muted);">
                        <?= htmlspecialchars($p['LevelName']) ?>
                      </span>
                    </td>
                    <td><?= $p['TotalModules'] ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="empty-state" style="padding:var(--space-8); border:1px solid var(--color-border); border-radius:var(--radius-lg);">
            <div class="empty-state__icon" aria-hidden="true">🎓</div>
            <h3 class="empty-state__title">No related programmes</h3>
            <p>Your modules are not currently part of any active programmes.</p>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </div>
</section>

<?php include('../includes/footer.php'); ?>
