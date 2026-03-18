<?php
/**
 * Shared Public Navbar + Head
 * Usage: include('../includes/header.php');
 * Vars: $pageTitle, $activePage, $cssBase, $rootBase
 */
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
  <meta name="description" content="<?= htmlspecialchars($pageTitle) ?> — Explore undergraduate and postgraduate programmes at the University of Excellence.">
  <title><?= htmlspecialchars($pageTitle) ?> | University of Excellence</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="<?= $cssBase ?>css/university.css">
</head>
<body>

<a class="skip-link" href="#main-content">Skip to main content</a>

<header class="navbar" id="site-navbar">
  <div class="navbar__inner">
    <a href="<?= $_pubBase ?>index.php" class="navbar__brand" aria-label="University of Excellence — Home">
      <div class="navbar__logo-mark" aria-hidden="true">UE</div>
      <div class="navbar__site-name">
        <span>University of Excellence</span>
        <span>Student Course Hub</span>
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
      <a href="<?= $rootBase ?>admin/login.php" class="btn btn-ghost btn-sm">
        Admin Login
      </a>
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
