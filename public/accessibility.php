<?php
$pageTitle  = 'Accessibility Statement';
$activePage = '';
$cssBase    = '../';
$rootBase   = '../';
include('../includes/header.php');
?>

<section style="background:linear-gradient(135deg,var(--color-primary-dark) 0%,var(--color-primary) 100%);padding:var(--space-12) 0;color:#fff;text-align:center;">
  <div class="container">
    <span style="display:inline-block;background:rgba(197,169,106,0.2);border:1px solid rgba(197,169,106,0.4);color:#dfc48a;font-size:0.7rem;font-weight:700;letter-spacing:0.12em;text-transform:uppercase;padding:0.25rem 0.75rem;border-radius:9999px;margin-bottom:1rem;">Legal &amp; Compliance</span>
    <h1 style="font-family:'Merriweather',serif;font-size:clamp(1.75rem,4vw,2.5rem);color:#fff;margin-bottom:1rem;">Accessibility Statement</h1>
    <p style="color:rgba(255,255,255,0.7);max-width:560px;margin:0 auto;">This statement applies to the University of Liverpool Student Course Hub website.</p>
  </div>
</section>

<section class="section">
  <div class="container--narrow">

    <div class="section-header--left" style="margin-bottom:var(--space-8);">
      <p style="font-size:var(--text-sm);color:var(--color-text-muted);">Last reviewed: March 2026</p>
    </div>

    <div style="background:var(--color-surface);border:1px solid var(--color-border);border-radius:var(--radius-lg);padding:var(--space-8);">

      <h2 style="font-size:var(--text-2xl);margin-bottom:var(--space-4);">Our Commitment</h2>
      <p>The University of Liverpool is committed to making its website accessible in accordance with the Public Sector Bodies (Websites and Mobile Applications) Accessibility Regulations 2018. This website aims to comply with the Web Content Accessibility Guidelines (WCAG) 2.1 at Level AA.</p>

      <h2 style="font-size:var(--text-xl);margin-top:var(--space-8);margin-bottom:var(--space-4);">Technical Information</h2>
      <p>This website uses the following accessibility features:</p>
      <ul style="margin-bottom:var(--space-4);">
        <li>Keyboard-only navigation is fully supported across all pages.</li>
        <li>A "Skip to main content" link is available at the top of every page.</li>
        <li>All interactive elements have visible focus indicators.</li>
        <li>All images have descriptive alternative text, or are marked as decorative.</li>
        <li>Colour contrast ratios meet the WCAG 2.1 AA minimum of 4.5:1 for body text.</li>
        <li>Form fields have associated labels and clear error messaging.</li>
        <li>ARIA roles and attributes are used where appropriate.</li>
      </ul>

      <h2 style="font-size:var(--text-xl);margin-top:var(--space-8);margin-bottom:var(--space-4);">Known Limitations</h2>
      <p>We are aware of the following areas for improvement and are working to address them:</p>
      <ul style="margin-bottom:var(--space-4);">
        <li>Some older PDF documents may not be fully accessible — we are working to remediate these.</li>
        <li>Third-party embedded content (e.g., maps) may not meet full accessibility standards.</li>
      </ul>

      <h2 style="font-size:var(--text-xl);margin-top:var(--space-8);margin-bottom:var(--space-4);">Feedback &amp; Contact</h2>
      <p>If you experience any accessibility issues or require information in an alternative format, please contact us:</p>
      <ul>
        <li>Email: <a href="mailto:accessibility@liverpool.ac.uk">accessibility@liverpool.ac.uk</a></li>
        <li>Phone: +44 (0)151 794 2000</li>
        <li>You may also use our <a href="contact.php">general contact form</a>.</li>
      </ul>

      <h2 style="font-size:var(--text-xl);margin-top:var(--space-8);margin-bottom:var(--space-4);">Enforcement Procedure</h2>
      <p>If you are not satisfied with our response, contact the <a href="https://www.equalityadvisoryservice.com/" target="_blank" rel="noopener noreferrer">Equality Advisory and Support Service (EASS)</a>. If you are in Northern Ireland and are not happy with our response, contact the <a href="https://www.equalityni.org/" target="_blank" rel="noopener noreferrer">Equality Commission for Northern Ireland</a>.</p>

    </div>
  </div>
</section>

<?php include('../includes/footer.php'); ?>
