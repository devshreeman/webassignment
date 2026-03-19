<?php
/**
 * Unified Admin Header — handles session_start safely.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle         = $pageTitle         ?? 'Admin Panel';
$activeSidebarItem = $activeSidebarItem ?? '';

// Access control
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$adminName = htmlspecialchars($_SESSION['admin']['name'] ?? 'Administrator');

function adminIcon(string $name): string {
    static $icons = [
      'dashboard'  => '<svg class="admin-sidebar__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>',
      'programmes' => '<svg class="admin-sidebar__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>',
      'modules'    => '<svg class="admin-sidebar__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>',
      'staff'      => '<svg class="admin-sidebar__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
      'interests'  => '<svg class="admin-sidebar__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
      'logout'     => '<svg class="admin-sidebar__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>',
      'settings'   => '<svg class="admin-sidebar__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>',
    ];
    return $icons[$name] ?? '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?> | Admin — Student Course Hub</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="../css/university.css">
</head>
<body>

<!-- Admin Top Navbar -->
<header class="navbar" id="site-navbar" style="position:sticky;top:0;z-index:200;">
  <div class="navbar__inner">
    <a href="../admin/dashboard.php" class="navbar__brand" aria-label="Student Course Hub Administration">
      <div class="navbar__site-name">
        <span style="font-size: 1.15rem;">Student Course Hub</span>
        <span>Administration</span>
      </div>
    </a>
    <div class="navbar__actions" style="margin-left:auto;gap:1rem;">
      <span style="font-size:0.8rem;color:rgba(255,255,255,0.55);">
        Signed in as <a href="../admin/admin_settings.php" style="color:#C5A96A;font-weight:600;text-decoration:none;"><?= $adminName ?></a>
      </span>
      <a href="../public/index.php" class="btn btn-ghost btn-sm" target="_blank">View Site ↗</a>
      <a href="../admin/logout.php" class="btn btn-danger btn-sm">Sign Out</a>
    </div>
  </div>
</header>

<!-- Admin Layout Wrapper -->
<div class="admin-layout">

  <!-- Sidebar -->
  <aside class="admin-sidebar" aria-label="Administration navigation">

    <p class="admin-sidebar__heading">Overview</p>
    <nav aria-label="Main admin menu">
      <ul class="admin-sidebar__nav" role="list">
        <li>
          <a href="../admin/dashboard.php" class="<?= $activeSidebarItem === 'dashboard' ? 'active' : '' ?>">
            <?= adminIcon('dashboard') ?> Dashboard
          </a>
        </li>
      </ul>
    </nav>

    <div class="admin-sidebar__divider"></div>
    <p class="admin-sidebar__heading">Academic Content</p>
    <nav aria-label="Content management">
      <ul class="admin-sidebar__nav" role="list">
        <li>
          <a href="../admin/manage_programmes.php" class="<?= $activeSidebarItem === 'programmes' ? 'active' : '' ?>">
            <?= adminIcon('programmes') ?> Programmes
          </a>
        </li>
        <li>
          <a href="../admin/manage_modules.php" class="<?= $activeSidebarItem === 'modules' ? 'active' : '' ?>">
            <?= adminIcon('modules') ?> Modules
          </a>
        </li>
        <li>
          <a href="../admin/manage_staff.php" class="<?= $activeSidebarItem === 'staff' ? 'active' : '' ?>">
            <?= adminIcon('staff') ?> Staff
          </a>
        </li>
      </ul>
    </nav>

    <div class="admin-sidebar__divider"></div>
    <p class="admin-sidebar__heading">Prospective Students</p>
    <nav aria-label="Student management">
      <ul class="admin-sidebar__nav" role="list">
        <li>
          <a href="../admin/manage_interests.php" class="<?= $activeSidebarItem === 'interests' ? 'active' : '' ?>">
            <?= adminIcon('interests') ?> Interest Registrations
          </a>
        </li>
        <li>
          <a href="../admin/contact_messages.php" class="<?= $activeSidebarItem === 'contact' ? 'active' : '' ?>">
            <?= adminIcon('interests') ?> Contact Messages
          </a>
        </li>
      </ul>
    </nav>

    <div class="admin-sidebar__divider"></div>
    <p class="admin-sidebar__heading">Account</p>
    <nav aria-label="Account settings">
      <ul class="admin-sidebar__nav" role="list">
        <li>
          <a href="../admin/admin_settings.php" class="<?= $activeSidebarItem === 'settings' ? 'active' : '' ?>">
            <?= adminIcon('settings') ?> Settings
          </a>
        </li>
        <li>
          <a href="../admin/logout.php">
            <?= adminIcon('logout') ?> Sign Out
          </a>
        </li>
      </ul>
    </nav>

  </aside>

  <!-- Main Content -->
  <main class="admin-main" id="main-content">
