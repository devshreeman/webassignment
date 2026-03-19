<?php
/**
 * Shared Public Header
 */

/* Initialize Session */
if (session_status() === PHP_SESSION_NONE) session_start();

/* Set Default Variables */
$pageTitle   = $pageTitle   ?? 'Student Course Hub';
$activePage  = $activePage  ?? '';
$cssBase     = $cssBase     ?? '../';
$rootBase    = $rootBase    ?? '../';
$_pubBase    = $rootBase . 'public/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= htmlspecialchars($pageTitle) ?> — Explore undergraduate and postgraduate programmes at the Student Course Hub.">
  <title><?= htmlspecialchars($pageTitle) ?> | Student Course Hub</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="<?= $cssBase ?>css/university.css">
</head>
<body>

<a class="skip-link" href="#main-content">Skip to main content</a>

<header class="navbar" id="site-navbar">
  <div class="navbar__inner">
    <a href="<?= $_pubBase ?>index.php" class="navbar__brand" aria-label="Student Course Hub — Home">
      <div class="navbar__site-name">
        <span style="font-size: 1.25rem;">Student Course Hub</span>
      </div>
    </a>

    <nav aria-label="Primary navigation">
      <ul class="navbar__nav" role="list">
        <li>
          <a href="<?= $_pubBase ?>index.php"
             <?= $activePage === 'home'     ? 'class="active" aria-current="page"' : '' ?>>
            Programmes
          </a>
        </li>
        <li>
          <a href="<?= $_pubBase ?>staff.php"
             <?= $activePage === 'staff'    ? 'class="active" aria-current="page"' : '' ?>>
            Our Staff
          </a>
        </li>
        <li>
          <a href="<?= $_pubBase ?>open-days.php"
             <?= $activePage === 'opendays' ? 'class="active" aria-current="page"' : '' ?>>
            Open Days
          </a>
        </li>
        <li>
          <a href="<?= $_pubBase ?>contact.php"
             <?= $activePage === 'contact'  ? 'class="active" aria-current="page"' : '' ?>>
            Contact
          </a>
        </li>
      </ul>
    </nav>

    <div class="navbar__actions">
      <?php if (isset($_SESSION['admin'])): ?>
        <div class="login-dropdown" id="loginDropdown">
          <button class="login-dropdown__toggle" id="loginToggleBtn" aria-haspopup="true" aria-expanded="false" type="button">
            Admin ▾
          </button>
          <div class="login-dropdown__menu" id="loginDropdownMenu" role="menu">
            <a href="<?= $rootBase ?>admin/dashboard.php" role="menuitem">Dashboard</a>
            <a href="<?= $rootBase ?>admin/logout.php" role="menuitem">Sign Out</a>
          </div>
        </div>
      <?php elseif (isset($_SESSION['staff'])): ?>
        <a href="<?= $rootBase ?>staff/logout.php" class="btn btn-primary btn-sm">Sign Out</a>
      <?php else: ?>
        <div class="login-dropdown" id="loginDropdown">
          <button class="login-dropdown__toggle" id="loginToggleBtn" aria-haspopup="true" aria-expanded="false" type="button">
            Login
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
              <polyline points="6 9 12 15 18 9"/>
            </svg>
          </button>
          <div class="login-dropdown__menu" id="loginDropdownMenu" role="menu">
            <a href="<?= $rootBase ?>admin/login.php" role="menuitem">Admin Login</a>
            <a href="<?= $rootBase ?>staff/login.php" role="menuitem">Staff Login</a>
          </div>
        </div>
      <?php endif; ?>
    </div>

    <button class="navbar__toggle" id="navToggle"
            aria-expanded="false"
            aria-label="Toggle navigation"
            aria-controls="site-navbar">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<main id="main-content">
