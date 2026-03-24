<?php
/**
 * Shared Public Footer
 */

/* Determine Base Path */
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
          <img src="<?= isset($rootBase) ? $rootBase : '../' ?>assets/logo-white.svg" alt="" width="36" height="36" aria-hidden="true">
          <span style="font-family:'Merriweather',serif;font-size:1.15rem;font-weight:700;color:#fff;line-height:1.2;">University of Liverpool</span>
        </div>
        <p>Inspiring minds, shaping futures. Explore undergraduate and postgraduate programmes designed to launch your career.</p>
        <p style="font-size:0.7rem;color:rgba(255,255,255,0.3);margin-top:1.25rem;margin-bottom:0;">WCAG 2.1 AA Compliant | UK GDPR Registered</p>
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
          <li><a href="<?= $footerBase ?>index.php">Browse Programmes</a></li>
          <li><a href="<?= $footerBase ?>contact.php">Contact Admissions</a></li>
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
        &copy; <?= $year ?> University of Liverpool. All rights reserved.
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
  const navToggle = document.getElementById('navToggle');
  const navbar    = document.getElementById('site-navbar');
  if (navToggle && navbar) {
    navToggle.addEventListener('click', () => {
      const isOpen = navbar.classList.toggle('is-open');
      navToggle.setAttribute('aria-expanded', String(isOpen));
    });
    navbar.querySelectorAll('nav a').forEach(a => {
      a.addEventListener('click', () => {
        navbar.classList.remove('is-open');
        navToggle.setAttribute('aria-expanded', 'false');
      });
    });
  }

  const loginToggleBtn = document.getElementById('loginToggleBtn');
  const loginDropdownMenu = document.getElementById('loginDropdownMenu');
  
  if (loginToggleBtn && loginDropdownMenu) {
    loginToggleBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      const isExpanded = loginToggleBtn.getAttribute('aria-expanded') === 'true';
      loginToggleBtn.setAttribute('aria-expanded', !isExpanded);
      loginDropdownMenu.classList.toggle('is-open');
    });

    document.addEventListener('click', (e) => {
      if (!loginToggleBtn.contains(e.target) && !loginDropdownMenu.contains(e.target)) {
        loginToggleBtn.setAttribute('aria-expanded', 'false');
        loginDropdownMenu.classList.remove('is-open');
      }
    });
  }
  
  // Scroll animations for programme cards
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };
  
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      }
    });
  }, observerOptions);
  
  document.querySelectorAll('.programme-card').forEach(card => {
    observer.observe(card);
  });
  
  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const href = this.getAttribute('href');
      if (href !== '#' && href.length > 1) {
        e.preventDefault();
        const target = document.querySelector(href);
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      }
    });
  });
</script>

</body>
</html>
