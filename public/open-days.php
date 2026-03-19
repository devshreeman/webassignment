<?php
$pageTitle  = 'Open Days';
$activePage = 'opendays';
$cssBase    = '../';
$rootBase   = '../';
include('../includes/header.php');

$events = [
    ['date'=>'12 April 2026',    'weekday'=>'Sunday',    'time'=>'10:00 – 16:00', 'type'=>'Undergraduate Open Day',    'desc'=>'Explore all undergraduate programmes across all departments. Campus tours, lab demonstrations, and Q&amp;A sessions with academic staff.'],
    ['date'=>'14 June 2026',     'weekday'=>'Sunday',    'time'=>'10:00 – 15:00', 'type'=>'Postgraduate Open Evening', 'desc'=>'An evening dedicated to postgraduate study options, including taught and research programmes. Meet course leaders and current students.'],
    ['date'=>'11 October 2026',  'weekday'=>'Sunday',    'time'=>'10:00 – 16:00', 'type'=>'Undergraduate Open Day',    'desc'=>'Second major open day of the academic year — ideal for UCAS applicants targeting 2027 entry.'],
    ['date'=>'22 November 2026', 'weekday'=>'Sunday',    'time'=>'11:00 – 15:00', 'type'=>'Applicant Visit Day',       'desc'=>'Exclusive event for students who have received an offer from the Student Course Hub. Includes department-specific sessions and campus tours.'],
];
?>

<section style="background:linear-gradient(135deg,var(--color-primary-dark) 0%,var(--color-primary) 100%);padding:var(--space-12) 0;color:#fff;text-align:center;">
  <div class="container">
    <span style="display:inline-block;background:rgba(197,169,106,0.2);border:1px solid rgba(197,169,106,0.4);color:#dfc48a;font-size:0.7rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;padding:0.25rem 0.75rem;border-radius:9999px;margin-bottom:1rem;">Events &amp; Visits</span>
    <h1 style="font-family:'Merriweather',serif;font-size:clamp(1.75rem,4vw,2.5rem);color:#fff;margin-bottom:1rem;">Open Days</h1>
    <p style="color:rgba(255,255,255,0.7);max-width:520px;margin:0 auto;font-size:1.05rem;">Visit our campus, meet our academic staff, and explore life at the Student Course Hub.</p>
  </div>
</section>

<section class="section">
  <div class="container--narrow">

    <div class="alert alert--info" role="note" style="display:flex;align-items:flex-start;gap:var(--space-3);">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;margin-top:1px;color:#0284C7;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      <div><strong>Upcoming Open Days:</strong> Registration for 2026 open days is now open. All events are free to attend.</div>
    </div>

    <div style="display:grid;gap:var(--space-5);margin-bottom:var(--space-12);">
      <?php foreach ($events as $e): $parts = explode(' ', $e['date']); ?>
        <article style="background:var(--color-surface);border:1px solid var(--color-border);border-radius:var(--radius-lg);padding:var(--space-6);display:grid;grid-template-columns:auto 1fr auto;gap:var(--space-6);align-items:center;">
          <div style="text-align:center;background:var(--color-primary);border-radius:var(--radius-md);padding:var(--space-4) var(--space-5);color:#fff;min-width:80px;">
            <div style="font-family:'Merriweather',serif;font-size:1.6rem;font-weight:700;line-height:1;"><?= $parts[0] ?></div>
            <div style="font-size:0.72rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--color-accent-light);"><?= $parts[1] . ' ' . $parts[2] ?></div>
          </div>
          <div>
            <div style="font-size:0.68rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;color:var(--color-accent-dark);margin-bottom:0.35rem;"><?= htmlspecialchars($e['type']) ?></div>
            <h2 style="font-family:'Inter',sans-serif;font-size:1.05rem;font-weight:600;color:var(--color-primary);margin-bottom:0.35rem;"><?= htmlspecialchars($e['weekday']) ?>, <?= htmlspecialchars($e['date']) ?></h2>
            <div style="display:flex;align-items:center;gap:0.4rem;font-size:0.8rem;color:var(--color-text-muted);margin-bottom:0.5rem;">
              <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              <?= htmlspecialchars($e['time']) ?>
            </div>
            <p style="font-size:0.875rem;color:var(--color-text);margin:0;"><?= $e['desc'] ?></p>
          </div>
          <div style="flex-shrink:0;">
            <a href="contact.php" class="btn btn-primary btn-sm">Register</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

    <div style="background:var(--color-primary);border-radius:var(--radius-lg);padding:var(--space-8);color:#fff;text-align:center;">
      <h2 style="color:#fff;font-family:'Merriweather',serif;margin-bottom:var(--space-3);">Can't make an Open Day?</h2>
      <p style="color:rgba(255,255,255,0.75);margin-bottom:var(--space-5);">We offer individual campus visits and virtual tours year-round. Contact our admissions team to arrange your visit.</p>
      <a href="contact.php" class="btn btn-primary">Contact Admissions</a>
    </div>

  </div>
</section>

<?php include('../includes/footer.php'); ?>
