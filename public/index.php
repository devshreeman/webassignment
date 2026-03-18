<?php
include('../config/db.php');

$pageTitle   = 'Explore Our Programmes';
$activePage  = 'home';
$cssBase     = '../';
$rootBase    = '../';

// Filters
$selectedLevel  = isset($_GET['level'])  ? (int)$_GET['level']  : 0;
$searchKeyword  = isset($_GET['search']) ? trim($_GET['search']) : '';

// Fetch levels
$levels = $pdo->query("SELECT * FROM levels ORDER BY LevelName")->fetchAll(PDO::FETCH_ASSOC);

// Build query
$params = [];
$where  = ['p.IsPublished = 1'];

if ($selectedLevel > 0) {
    $where[]  = 'p.LevelID = ?';
    $params[] = $selectedLevel;
}

if ($searchKeyword !== '') {
    $where[]  = '(p.ProgrammeName LIKE ? OR p.Description LIKE ?)';
    $params[] = "%{$searchKeyword}%";
    $params[] = "%{$searchKeyword}%";
}

$whereSQL = 'WHERE ' . implode(' AND ', $where);

$stmt = $pdo->prepare("
    SELECT p.*, l.LevelName,
           (SELECT COUNT(*) FROM programmemodules pm WHERE pm.ProgrammeID = p.ProgrammeID) AS ModuleCount
    FROM programmes p
    JOIN levels l ON p.LevelID = l.LevelID
    {$whereSQL}
    ORDER BY l.LevelName, p.ProgrammeName
");
$stmt->execute($params);
$programmes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Stats for hero badges
$totalProgrammes = $pdo->query("SELECT COUNT(*) FROM programmes WHERE IsPublished = 1")->fetchColumn();
$totalModules    = $pdo->query("SELECT COUNT(*) FROM modules WHERE IsPublished = 1")->fetchColumn();

// Fallback images per programme name
function getProgrammeImage($prog) {
    $name  = strtolower($prog['ProgrammeName']);
    $image = $prog['Image'] ?? '';
    if (!empty($image)) return htmlspecialchars($image);
    if (str_contains($name, 'artificial intelligence') || str_contains($name, 'ai'))
        return 'https://images.unsplash.com/photo-1677442136019-21780ecad995?auto=format&fit=crop&w=800&q=80';
    if (str_contains($name, 'cyber'))
        return 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=800&q=80';
    if (str_contains($name, 'software'))
        return 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=800&q=80';
    if (str_contains($name, 'data'))
        return 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=800&q=80';
    if (str_contains($name, 'business') || str_contains($name, 'management'))
        return 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=800&q=80';
    if (str_contains($name, 'network'))
        return 'https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=800&q=80';
    return 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=800&q=80';
}

include('../includes/header.php');
?>

<!-- HERO -->
<section class="hero">
  <div class="hero__inner">
    <div class="hero__content">
      <span class="hero__kicker">University of Excellence — 2025 Open Enrolment</span>
      <h1 class="hero__title">
        Discover Your<br><em>Perfect Programme</em>
      </h1>
      <p class="hero__description">
        Explore our range of undergraduate and postgraduate programmes. Find your passion, register your interest, and take the first step towards an outstanding future.
      </p>
      <div class="hero__actions">
        <a href="#programmes-section" class="btn btn-primary btn-lg">Browse Programmes</a>
        <a href="staff.php" class="btn btn-ghost btn-lg">Meet Our Staff</a>
      </div>
    </div>
    <div class="hero__visual" aria-hidden="true">
      <div class="hero__image-wrapper">
        <img src="https://images.unsplash.com/photo-1576267423445-b2e0074d68a4?q=80&w=2940&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
             alt="University campus" loading="lazy">
      </div>
      <div class="hero__stat-badge hero__stat-badge--1">
        <svg style="width:28px;height:28px;color:#C5A96A;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        <div>
          <strong><?= $totalProgrammes ?>+</strong>
          <span>Programmes</span>
        </div>
      </div>
      <div class="hero__stat-badge hero__stat-badge--2">
        <svg style="width:28px;height:28px;color:#059669;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
        <div>
          <strong><?= $totalModules ?>+</strong>
          <span>Modules</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- MAIN PROGRAMMES SECTION -->
<section class="section" id="programmes-section">
  <div class="container">

    <!-- Section Header -->
    <div class="section-header">
      <span class="section-header__eyebrow">Academic Programmes</span>
      <h2 class="section-header__title">
        <?php if ($searchKeyword): ?>
          Search Results for "<?= htmlspecialchars($searchKeyword) ?>"
        <?php elseif ($selectedLevel > 0): ?>
          <?= htmlspecialchars(array_column($levels, 'LevelName', 'LevelID')[$selectedLevel] ?? '') ?> Programmes
        <?php else: ?>
          All Available Programmes
        <?php endif; ?>
      </h2>
      <div class="section-divider"></div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" action="" id="filterForm" role="search" aria-label="Filter programmes">
      <div class="filter-bar">
        <div class="filter-bar__group filter-bar__group--search">
          <label class="filter-bar__label" for="search">Search Programmes</label>
          <input
            class="filter-bar__input"
            type="search"
            id="search"
            name="search"
            placeholder="e.g. Cyber Security, Artificial Intelligence..."
            value="<?= htmlspecialchars($searchKeyword) ?>"
            aria-label="Search programmes by keyword"
          >
        </div>
        <div class="filter-bar__group">
          <label class="filter-bar__label" for="level">Filter by Level</label>
          <select class="filter-bar__select" id="level" name="level" aria-label="Filter by study level">
            <option value="0" <?= $selectedLevel === 0 ? 'selected' : '' ?>>All Levels</option>
            <?php foreach ($levels as $lvl): ?>
              <option value="<?= $lvl['LevelID'] ?>" <?= $selectedLevel === (int)$lvl['LevelID'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($lvl['LevelName']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div style="display:flex;align-items:flex-end;">
          <button type="submit" class="btn btn-primary">Apply Filters</button>
          <?php if ($searchKeyword || $selectedLevel): ?>
            <a href="index.php" class="btn btn-secondary btn-sm" style="margin-left:0.5rem;">Clear</a>
          <?php endif; ?>
        </div>
      </div>
    </form>

    <!-- Results count -->
    <p class="text-muted text-sm mb-6">
      Showing <strong><?= count($programmes) ?></strong> programme<?= count($programmes) !== 1 ? 's' : '' ?>
      <?= $searchKeyword ? ' matching "<strong>' . htmlspecialchars($searchKeyword) . '</strong>"' : '' ?>
    </p>

    <!-- Programme Cards Grid -->
    <?php if ($programmes): ?>
      <div class="grid grid-3" role="list" aria-label="Available programmes">
        <?php foreach ($programmes as $p): ?>
          <article class="programme-card" role="listitem">
            <div class="programme-card__image-wrapper">
              <img
                src="<?= getProgrammeImage($p) ?>"
                alt="<?= htmlspecialchars($p['ProgrammeName']) ?>"
                loading="lazy"
                width="400" height="225"
              >
              <span class="programme-card__level-badge"><?= htmlspecialchars($p['LevelName']) ?></span>
            </div>
            <div class="programme-card__body">
              <h3 class="programme-card__title">
                <a href="programme.php?id=<?= $p['ProgrammeID'] ?>" style="color:inherit;text-decoration:none;">
                  <?= htmlspecialchars($p['ProgrammeName']) ?>
                </a>
              </h3>
              <p class="programme-card__description">
                <?= htmlspecialchars(mb_substr($p['Description'] ?? '', 0, 130)) ?><?= strlen($p['Description'] ?? '') > 130 ? '…' : '' ?>
              </p>
              <div class="programme-card__footer">
                <span class="programme-card__modules-count">
                  <?= (int)$p['ModuleCount'] ?> module<?= $p['ModuleCount'] != 1 ? 's' : '' ?>
                </span>
                <a href="programme.php?id=<?= $p['ProgrammeID'] ?>"
                   class="btn btn-primary btn-sm"
                   aria-label="View details for <?= htmlspecialchars($p['ProgrammeName']) ?>">
                  View Details →
                </a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>

    <?php else: ?>
      <div class="empty-state">
        <div class="empty-state__icon" aria-hidden="true">🔍</div>
        <h3 class="empty-state__title">No programmes found</h3>
        <p>Try adjusting your search or filter criteria.</p>
        <a href="index.php" class="btn btn-primary mt-4">View All Programmes</a>
      </div>
    <?php endif; ?>

  </div>
</section>

<!-- REGISTER INTEREST CTA SECTION -->
<section class="section--sm" style="background: var(--color-surface); border-top: 1px solid var(--color-border);">
  <div class="container">
    <div class="interest-cta">
      <div style="max-width:600px;">
        <h2 class="interest-cta__title">Ready to Take the Next Step?</h2>
        <p class="interest-cta__description">
          Register your interest in any of our programmes to receive updates about open days, application deadlines, and exclusive events.
        </p>
        <a href="index.php" class="btn btn-primary btn-lg">Browse &amp; Register Interest</a>
      </div>
    </div>
  </div>
</section>

<?php include('../includes/footer.php'); ?>
