<?php
include('../config/db.php');
$pageTitle         = 'Dashboard';
$activeSidebarItem = 'dashboard';
include('../includes/admin_header.php');

// get dashboard statistics
$totalProgrammes = $pdo->query("SELECT COUNT(*) FROM programmes")->fetchColumn();
$totalPublished  = $pdo->query("SELECT COUNT(*) FROM programmes WHERE IsPublished = 1")->fetchColumn();
$totalModules    = $pdo->query("SELECT COUNT(*) FROM modules")->fetchColumn();
$totalStaff      = $pdo->query("SELECT COUNT(*) FROM staff")->fetchColumn();
$totalInterests  = $pdo->query("SELECT COUNT(*) FROM interestedstudents")->fetchColumn();

// get recent interest registrations
$recentInterests = $pdo->query("
    SELECT si.StudentName, si.Email, p.ProgrammeName, si.RegisteredAt
    FROM interestedstudents si
    JOIN programmes p ON si.ProgrammeID = p.ProgrammeID
    ORDER BY si.RegisteredAt DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// get top programmes by interest count
$topProgrammes = $pdo->query("
    SELECT p.ProgrammeName, COUNT(si.InterestID) AS IntCount, p.IsPublished
    FROM programmes p
    LEFT JOIN interestedstudents si ON p.ProgrammeID = si.ProgrammeID
    GROUP BY p.ProgrammeID
    ORDER BY IntCount DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Page Header -->
<div class="admin-topbar">
  <div>
    <h1 class="admin-page-title">Dashboard</h1>
    <p class="admin-page-subtitle">Welcome back, <?= htmlspecialchars($_SESSION['admin']['name'] ?? 'Administrator') ?>. Here's an overview of the Student Course Hub.</p>
  </div>
  <a href="manage_programmes.php" class="btn btn-primary">+ Add Programme</a>
</div>

<!-- Stat Cards -->
<div class="stats-grid">
  <div class="stat-card stat-card--blue">
    <div class="stat-card__icon" aria-hidden="true">
      <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
    </div>
    <div>
      <div class="stat-card__value"><?= $totalProgrammes ?></div>
      <div class="stat-card__label">Total Programmes</div>
    </div>
  </div>
  <div class="stat-card stat-card--green">
    <div class="stat-card__icon" aria-hidden="true">
      <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    </div>
    <div>
      <div class="stat-card__value"><?= $totalPublished ?></div>
      <div class="stat-card__label">Published</div>
    </div>
  </div>
  <div class="stat-card stat-card--gold">
    <div class="stat-card__icon" aria-hidden="true">
      <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
    </div>
    <div>
      <div class="stat-card__value"><?= $totalModules ?></div>
      <div class="stat-card__label">Total Modules</div>
    </div>
  </div>
  <div class="stat-card stat-card--red">
    <div class="stat-card__icon" aria-hidden="true">
      <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
    </div>
    <div>
      <div class="stat-card__value"><?= $totalInterests ?></div>
      <div class="stat-card__label">Student Interests</div>
    </div>
  </div>
</div>

<div class="two-col-layout" style="margin-top:var(--space-2);">

  <!-- Recent Registrations -->
  <div class="admin-section-card">
    <div class="admin-section-card__header">
      <h2 class="admin-section-card__title">Recent Interest Registrations</h2>
      <a href="manage_interests.php" class="btn btn-secondary btn-sm">View All</a>
    </div>
    <?php if ($recentInterests): ?>
      <div class="data-table-wrapper">
        <table class="data-table" aria-label="Recent student interest registrations">
          <thead>
            <tr>
              <th scope="col">Student</th>
              <th scope="col">Programme</th>
              <th scope="col">Date</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentInterests as $r): ?>
              <tr>
                <td>
                  <div style="font-weight:600;"><?= htmlspecialchars($r['StudentName']) ?></div>
                  <div style="font-size:var(--text-xs);color:var(--color-text-muted);"><?= htmlspecialchars($r['Email']) ?></div>
                </td>
                <td><?= htmlspecialchars($r['ProgrammeName']) ?></td>
                <td style="color:var(--color-text-muted);white-space:nowrap;"><?= date('d M Y', strtotime($r['RegisteredAt'])) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="empty-state" style="padding:var(--space-8) 0;">
        <p class="empty-state__title">No registrations yet</p>
      </div>
    <?php endif; ?>
  </div>

  <!-- Top Programmes by Interest -->
  <div class="admin-section-card">
    <div class="admin-section-card__header">
      <h2 class="admin-section-card__title">Top Programmes</h2>
      <a href="manage_programmes.php" class="btn btn-secondary btn-sm">Manage</a>
    </div>
    <?php if ($topProgrammes): ?>
      <ul style="list-style:none;padding:0;margin:0;">
        <?php foreach ($topProgrammes as $i => $tp): ?>
          <li style="display:flex;align-items:center;justify-content:space-between;padding:var(--space-3) 0;border-bottom:1px solid var(--color-border);">
            <div style="display:flex;align-items:center;gap:var(--space-3);min-width:0;">
              <span style="width:24px;height:24px;border-radius:50%;background:<?= $i === 0 ? 'var(--color-accent)' : 'var(--color-bg)' ?>;border:1px solid var(--color-border);font-size:0.7rem;font-weight:700;color:<?= $i === 0 ? 'var(--color-primary)' : 'var(--color-text-muted)' ?>;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><?= $i + 1 ?></span>
              <span style="font-size:var(--text-sm);font-weight:500;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars($tp['ProgrammeName']) ?></span>
            </div>
            <div style="display:flex;align-items:center;gap:var(--space-3);flex-shrink:0;">
              <span style="font-weight:700;color:var(--color-primary);"><?= $tp['IntCount'] ?></span>
              <span class="status-badge <?= $tp['IsPublished'] ? 'status-badge--published' : 'status-badge--draft' ?>"><?= $tp['IsPublished'] ? 'Live' : 'Draft' ?></span>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <!-- Quick Links -->
    <div style="margin-top:var(--space-5);display:grid;grid-template-columns:1fr 1fr;gap:var(--space-3);">
      <a href="manage_staff.php" class="btn btn-secondary btn-sm" style="text-align:center;">Manage Staff</a>
      <a href="manage_interests.php" class="btn btn-secondary btn-sm" style="text-align:center;">Export List</a>
    </div>
  </div>

</div>

<?php include('../includes/admin_footer.php'); ?>
