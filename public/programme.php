<?php
include('../config/db.php');

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];

/* Fetch Programme Details */
$stmt = $pdo->prepare("
    SELECT p.*, l.LevelName,
           s.Name AS LeaderName, s.Email AS LeaderEmail, s.Photo AS LeaderPhoto
    FROM programmes p
    LEFT JOIN levels l ON p.LevelID = l.LevelID
    LEFT JOIN staff s ON p.ProgrammeLeaderID = s.StaffID
    WHERE p.ProgrammeID = ? AND p.IsPublished = 1
");
$stmt->execute([$id]);
$programme = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$programme) {
    http_response_code(404);
    header("Location: index.php");
    exit;
}

/* Fetch Programme Modules */
$mod_stmt = $pdo->prepare("
    SELECT m.ModuleID, m.ModuleName, m.Description, pm.Year,
           s.Name AS LeaderName, s.StaffID AS LeaderID
    FROM programmemodules pm
    JOIN modules m ON pm.ModuleID = m.ModuleID
    LEFT JOIN staff s ON m.ModuleLeaderID = s.StaffID
    WHERE pm.ProgrammeID = ?
    ORDER BY pm.Year ASC, m.ModuleName ASC
");
$mod_stmt->execute([$id]);
$allModules = $mod_stmt->fetchAll(PDO::FETCH_ASSOC);

/* Group Modules by Year */
$modulesByYear = [];
foreach ($allModules as $m) {
    $year = $m['Year'] ?? 'Other';
    $modulesByYear[$year][] = $m;
}
ksort($modulesByYear);

/* Check for Flash Messages */
session_start();
$successMsg = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_success']);

$pageTitle  = htmlspecialchars($programme['ProgrammeName']);
$activePage = 'home';
$cssBase    = '../';
$rootBase   = '../';

/* Helper Function: Generate Initials */
function initials($name) {
    $parts = explode(' ', $name);
    return strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
}

include('../includes/header.php');
?>

<!-- BREADCRUMB -->
<div style="background: var(--color-surface); border-bottom: 1px solid var(--color-border);">
  <div class="container">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <span class="breadcrumb__item"><a href="index.php">Programmes</a></span>
      <span class="breadcrumb__separator" aria-hidden="true">›</span>
      <span class="breadcrumb__item breadcrumb__item--current" aria-current="page">
        <?= htmlspecialchars($programme['ProgrammeName']) ?>
      </span>
    </nav>
  </div>
</div>

<!-- PROGRAMME HERO BANNER -->
<section style="background: linear-gradient(135deg, var(--color-primary-dark) 0%, var(--color-primary) 100%); padding: var(--space-12) 0; color: #fff; position: relative; overflow: hidden;">
  <?php if (!empty($programme['Image'])): ?>
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0.15; background-image: url('<?= '../' . htmlspecialchars($programme['Image']) ?>'); background-size: cover; background-position: center;"></div>
  <?php endif; ?>
  <div class="container" style="position: relative; z-index: 1;">
    <span style="display:inline-block;background:rgba(197,169,106,0.2);border:1px solid rgba(197,169,106,0.4);color:#dfc48a;font-size:0.7rem;font-weight:700;letter-spacing:0.1em;text-transform:uppercase;padding:0.25rem 0.75rem;border-radius:9999px;margin-bottom:1rem;">
      <?= htmlspecialchars($programme['LevelName']) ?>
    </span>
    <h1 style="font-family:'Merriweather',serif;font-size:clamp(1.75rem,4vw,2.75rem);color:#fff;margin-bottom:1rem;max-width:750px;">
      <?= htmlspecialchars($programme['ProgrammeName']) ?>
    </h1>
    <p style="color:rgba(255,255,255,0.75);max-width:680px;font-size:1.05rem;line-height:1.7;margin-bottom:1.5rem;">
      <?= nl2br(htmlspecialchars($programme['Description'] ?? '')) ?>
    </p>
    <div style="display:flex;gap:1rem;flex-wrap:wrap;">
      <a href="register_interest.php?id=<?= $id ?>" class="btn btn-primary btn-lg">Register My Interest</a>
      <a href="#modules" class="btn btn-ghost">View Modules ↓</a>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">

    <?php if ($successMsg): ?>
      <div class="alert alert--success" role="alert">✓ <?= htmlspecialchars($successMsg) ?></div>
    <?php endif; ?>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:var(--space-10);align-items:start;">

      <!-- LEFT: Module List -->
      <div id="modules">
        <div class="section-header--left" style="margin-bottom:var(--space-8);">
          <span class="section-header__eyebrow">Curriculum</span>
          <h2 class="section-header__title" style="font-size:var(--text-2xl);">Modules by Year</h2>
          <div class="section-divider" style="margin-left:0;"></div>
        </div>

        <?php if ($modulesByYear): ?>
          <?php foreach ($modulesByYear as $year => $yearModules): ?>
            <div style="margin-bottom: var(--space-8);">
              <h3 style="font-family:var(--font-sans);font-size:var(--text-base);font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:var(--color-text-muted);margin-bottom:var(--space-4);display:flex;align-items:center;gap:var(--space-3);">
                <span style="width:2px;height:1.2em;background:var(--color-accent);border-radius:2px;display:inline-block;"></span>
                Year <?= is_numeric($year) ? htmlspecialchars($year) : htmlspecialchars($year) ?>
              </h3>
              <?php foreach ($yearModules as $m): ?>
                <article class="module-card">
                  <h4 class="module-card__title"><?= htmlspecialchars($m['ModuleName']) ?></h4>
                  <?php if (!empty($m['Description'])): ?>
                    <p class="module-card__description"><?= htmlspecialchars(mb_substr($m['Description'], 0, 200)) ?><?= strlen($m['Description']) > 200 ? '…' : '' ?></p>
                  <?php endif; ?>
                  <?php if (!empty($m['LeaderName'])): ?>
                    <div class="module-card__leader">
                      <div class="module-card__leader-avatar" aria-hidden="true">
                        <?= initials($m['LeaderName']) ?>
                      </div>
                      <div>
                        <div class="module-card__leader-name"><?= htmlspecialchars($m['LeaderName']) ?></div>
                        <div class="module-card__leader-label">Module Leader</div>
                      </div>
                    </div>
                  <?php endif; ?>
                </article>
              <?php endforeach; ?>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="empty-state">
            <div class="empty-state__icon" aria-hidden="true">📖</div>
            <p class="empty-state__title">Module information coming soon</p>
          </div>
        <?php endif; ?>
      </div>

      <!-- RIGHT: Sticky Info Panel -->
      <div>
        <!-- Programme Leader -->
        <?php if (!empty($programme['LeaderName'])): ?>
        <div class="admin-section-card" style="margin-bottom:var(--space-5);">
          <h4 style="font-family:var(--font-sans);font-size:var(--text-base);font-weight:700;color:var(--color-text-muted);letter-spacing:0.08em;text-transform:uppercase;margin-bottom:var(--space-4);">Programme Leader</h4>
          <div style="display:flex;align-items:center;gap:var(--space-4);">
            <div style="width:56px;height:56px;border-radius:50%;overflow:hidden;background:var(--color-primary);flex-shrink:0;display:flex;align-items:center;justify-content:center;font-family:var(--font-serif);font-size:1.1rem;font-weight:700;color:var(--color-accent);">
              <?php if (!empty($programme['LeaderPhoto'])): ?>
                <img src="<?= '../' . htmlspecialchars($programme['LeaderPhoto']) ?>" alt="<?= htmlspecialchars($programme['LeaderName']) ?>" style="width:100%;height:100%;object-fit:cover;">
              <?php else: ?>
                <?= initials($programme['LeaderName']) ?>
              <?php endif; ?>
            </div>
            <div>
              <p style="font-weight:600;color:var(--color-text);margin-bottom:2px;"><?= htmlspecialchars($programme['LeaderName']) ?></p>
              <p style="font-size:var(--text-xs);color:var(--color-text-muted);margin-bottom:0;"><?= htmlspecialchars($programme['LeaderEmail'] ?? '') ?></p>
            </div>
          </div>
        </div>
        <?php endif; ?>

        <!-- Register Interest CTA -->
        <div style="background:linear-gradient(135deg,var(--color-primary) 0%,#0d3a6e 100%);border-radius:var(--radius-lg);padding:var(--space-6);color:#fff;text-align:center;">
          <div style="font-size:2rem;margin-bottom:var(--space-3);" aria-hidden="true">🎓</div>
          <h4 style="color:#fff;font-family:var(--font-serif);margin-bottom:var(--space-3);">Interested in this Programme?</h4>
          <p style="color:rgba(255,255,255,0.75);font-size:var(--text-sm);margin-bottom:var(--space-5);">Register to receive updates on application deadlines and programme news.</p>
          <a href="register_interest.php?id=<?= $id ?>" class="btn btn-primary" style="display:block;text-align:center;">Register My Interest</a>
        </div>

        <!-- Info Summary -->
        <div class="admin-section-card" style="margin-top:var(--space-5);">
          <h4 style="font-family:var(--font-sans);font-size:var(--text-base);font-weight:700;color:var(--color-text-muted);letter-spacing:0.08em;text-transform:uppercase;margin-bottom:var(--space-4);">Programme Overview</h4>
          <dl style="font-size:var(--text-sm);">
            <div style="display:flex;justify-content:space-between;padding:var(--space-3) 0;border-bottom:1px solid var(--color-border);">
              <dt style="color:var(--color-text-muted);">Level</dt>
              <dd style="font-weight:600;color:var(--color-text);"><?= htmlspecialchars($programme['LevelName']) ?></dd>
            </div>
            <div style="display:flex;justify-content:space-between;padding:var(--space-3) 0;border-bottom:1px solid var(--color-border);">
              <dt style="color:var(--color-text-muted);">Total Modules</dt>
              <dd style="font-weight:600;color:var(--color-text);"><?= count($allModules) ?></dd>
            </div>
            <div style="display:flex;justify-content:space-between;padding:var(--space-3) 0;">
              <dt style="color:var(--color-text-muted);">Duration</dt>
              <dd style="font-weight:600;color:var(--color-text);"><?= count($modulesByYear) ?> Year<?= count($modulesByYear) !== 1 ? 's' : '' ?></dd>
            </div>
          </dl>
        </div>

      </div>
    </div>

  </div>
</section>

<?php include('../includes/footer.php'); ?>
