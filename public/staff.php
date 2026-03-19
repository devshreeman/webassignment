<?php
include('../config/db.php');

$staff = $pdo->query("
    SELECT StaffID, Name, Email, Bio, Photo
    FROM staff
    ORDER BY Name
")->fetchAll(PDO::FETCH_ASSOC);

$pageTitle  = 'Our Academic Staff';
$activePage = 'staff';
$cssBase    = '../';
$rootBase   = '../';

function staffInitials($name) {
    $parts = explode(' ', trim($name));
    $i = strtoupper(substr($parts[0], 0, 1));
    if (isset($parts[1])) $i .= strtoupper(substr($parts[1], 0, 1));
    return $i;
}

include('../includes/header.php');
?>

<section style="background: linear-gradient(135deg, var(--color-primary-dark) 0%, var(--color-primary) 100%); padding:var(--space-12) 0; color:#fff; text-align:center;">
  <div class="container">
    <span style="display:inline-block;background:rgba(197,169,106,0.2);border:1px solid rgba(197,169,106,0.4);color:#dfc48a;font-size:0.7rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;padding:0.25rem 0.75rem;border-radius:9999px;margin-bottom:1rem;">
      Faculty &amp; Academics
    </span>
    <h1 style="font-family:'Merriweather',serif;font-size:clamp(1.75rem,4vw,2.5rem);color:#fff;margin-bottom:1rem;">Meet Our Academic Staff</h1>
    <p style="color:rgba(255,255,255,0.7);max-width:520px;margin:0 auto;font-size:1.05rem;">
      Our dedicated faculty bring world-class expertise and a passion for teaching to every module they lead.
    </p>
  </div>
</section>

<section class="section">
  <div class="container">

    <?php if ($staff): ?>
      <div class="grid grid-3" role="list" aria-label="Academic staff">
        <?php foreach ($staff as $s): ?>
          <article class="staff-card" role="listitem">
            <div class="staff-card__photo-wrapper">
              <?php if (!empty($s['Photo'])): ?>
                <img class="staff-card__photo"
                     src="<?= '../' . htmlspecialchars($s['Photo']) ?>"
                     alt="Photo of <?= htmlspecialchars($s['Name']) ?>"
                     loading="lazy">
              <?php else: ?>
                <div class="staff-card__initials" aria-hidden="true">
                  <?= staffInitials($s['Name']) ?>
                </div>
              <?php endif; ?>
            </div>

            <h3 class="staff-card__name"><?= htmlspecialchars($s['Name']) ?></h3>

            <?php if (!empty($s['Bio'])): ?>
              <p class="staff-card__bio">
                <?= htmlspecialchars(mb_substr($s['Bio'], 0, 160)) ?><?= strlen($s['Bio']) > 160 ? '…' : '' ?>
              </p>
            <?php endif; ?>

            <?php if (!empty($s['Email'])): ?>
              <a class="staff-card__email" href="mailto:<?= htmlspecialchars($s['Email']) ?>">
                <?= htmlspecialchars($s['Email']) ?>
              </a>
            <?php endif; ?>
          </article>
        <?php endforeach; ?>
      </div>

    <?php else: ?>
      <div class="empty-state">
        <div class="empty-state__icon" aria-hidden="true">👩‍🏫</div>
        <h3 class="empty-state__title">Staff information coming soon</h3>
        <p>Our faculty profiles will be published shortly.</p>
      </div>
    <?php endif; ?>

  </div>
</section>

<?php include('../includes/footer.php'); ?>
