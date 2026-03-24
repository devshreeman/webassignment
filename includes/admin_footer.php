<?php
/**
 * Shared Admin Footer — closes the admin layout
 */
?>
  </main><!-- /.admin-main -->
</div><!-- /.admin-layout -->

<script>
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', e => {
      if (!confirm(el.dataset.confirm)) e.preventDefault();
    });
  });

  document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
      alert.style.transition = 'opacity 0.5s';
      alert.style.opacity = '0';
      setTimeout(() => alert.remove(), 500);
    }, 5000);
  });
  
  // Mobile admin sidebar toggle
  const adminSidebarToggle = document.getElementById('adminSidebarToggle');
  const adminSidebar = document.querySelector('.admin-sidebar');
  
  if (adminSidebarToggle && adminSidebar) {
    // Show toggle button on mobile
    function checkMobile() {
      if (window.innerWidth <= 640) {
        adminSidebarToggle.style.display = 'flex';
        document.querySelector('.admin-user-desktop')?.style.setProperty('display', 'none', 'important');
        document.getElementById('viewSiteBtn')?.style.setProperty('display', 'none', 'important');
      } else {
        adminSidebarToggle.style.display = 'none';
        adminSidebar.classList.remove('is-open');
        document.querySelector('.admin-user-desktop')?.style.setProperty('display', 'inline', 'important');
        document.getElementById('viewSiteBtn')?.style.setProperty('display', 'inline-flex', 'important');
      }
    }
    
    checkMobile();
    window.addEventListener('resize', checkMobile);
    
    adminSidebarToggle.addEventListener('click', () => {
      const isOpen = adminSidebar.classList.toggle('is-open');
      adminSidebarToggle.setAttribute('aria-expanded', String(isOpen));
    });
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', (e) => {
      if (window.innerWidth <= 640 && 
          !adminSidebar.contains(e.target) && 
          !adminSidebarToggle.contains(e.target) &&
          adminSidebar.classList.contains('is-open')) {
        adminSidebar.classList.remove('is-open');
        adminSidebarToggle.setAttribute('aria-expanded', 'false');
      }
    });
  }
</script>
</body>
</html>
