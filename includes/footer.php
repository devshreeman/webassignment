<?php
/**
 * Shared Public Footer
 * Determines base path dynamically so links work from any depth.
 */
// Determine correct relative base (public pages are in /public/)
$footerBase = isset($rootBase) ? $rootBase . 'public/' : '../public/';
$year = date('Y');
?>
</main>

<footer class="site-footer" role="contentinfo">
  <div class="container">
    <div class="site-footer__grid">

      <!-- Brand -->
      <div class="site-footer__brand">
        <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1rem;">
          <div style="width:38px;height:38px;border:2px solid #C5A96A;border-radius:4px;display:flex;align-items:center;justify-content:center;font-family:'Merriweather',serif;font-weight:900;color:#C5A96A;font-size:1rem;flex-shrink:0;">UE</div>
          <span style="font-family:'Merriweather',serif;font-size:0.95rem;font-weight:700;color:#fff;line-height:1.2;">University<br>of Excellence</span>
        </div>
        <p>Inspiring minds, shaping futures. Explore undergraduate and postgraduate programmes designed to launch your career.</p>
        <p style="font-size:0.7rem;color:rgba(255,255,255,0.3);margin-top:1.25rem;margin-bottom:0;">WCAG 2.1 AA Compliant &nbsp;·&nbsp; UK GDPR Registered</p>
      </div>

      <!-- Programmes -->
      <div>
        <h3 class="site-footer__heading">Programmes</h3>
        <ul class="site-footer__links">
          <li><a href="<?= $footerBase ?>index.php">All Programmes</a></li>
          <li><a href="<?= $footerBase ?>index.php?level=1">Undergraduate</a></li>
          <li><a href="<?= $footerBase ?>index.php?level=2">Postgraduate</a></li>
          <li><a href="<?= $footerBase ?>staff.php">Academic Staff</a></li>
        </ul>
      </div>

      <!-- Study -->
      <div>
        <h3 class="site-footer__heading">Study with Us</h3>
        <ul class="site-footer__links">
          <li><a href="<?= $footerBase ?>open-days.php">Open Days</a></li>
          <li><a href="<?= $footerBase ?>contact.php">Contact Admissions</a></li>
          <li><a href="<?= $footerBase ?>contact.php?s=fees">Fees &amp; Funding</a></li>
        </ul>
      </div>

      <!-- Legal -->
      <div>
        <h3 class="site-footer__heading">Information</h3>
        <ul class="site-footer__links">
          <li><a href="<?= $footerBase ?>accessibility.php">Accessibility Statement</a></li>
          <li><a href="<?= $footerBase ?>privacy.php">Privacy Policy</a></li>
          <li><a href="<?= $footerBase ?>contact.php">Contact Us</a></li>
        </ul>
      </div>

    </div>

    <div class="site-footer__bottom">
      <p class="site-footer__copyright">
        &copy; <?= $year ?> University of Excellence. All rights reserved.
      </p>
      <div class="site-footer__legal">
        <a href="<?= $footerBase ?>privacy.php">Privacy</a>
        <a href="<?= $footerBase ?>accessibility.php">Accessibility</a>
        <a href="<?= $footerBase ?>contact.php">Contact</a>
      </div>
    </div>
  </div>
</footer>

<script>
  // Mobile navbar toggle
  const navToggle = document.getElementById('navToggle');
  const navbar    = document.getElementById('site-navbar');
  if (navToggle && navbar) {
    navToggle.addEventListener('click', () => {
      const isOpen = navbar.classList.toggle('is-open');
      navToggle.setAttribute('aria-expanded', String(isOpen));
    });
    // Close mobile nav when a link is clicked
    navbar.querySelectorAll('a').forEach(a => {
      a.addEventListener('click', () => {
        navbar.classList.remove('is-open');
        navToggle.setAttribute('aria-expanded', 'false');
      });
    });
  }
</script>

</body>
</html>
