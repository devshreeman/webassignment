<?php
// admin footer - closes the admin layout
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
    
    // Close sidebar when clicking a link on mobile
    adminSidebar.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        if (window.innerWidth <= 640) {
          adminSidebar.classList.remove('is-open');
          adminSidebarToggle.setAttribute('aria-expanded', 'false');
        }
      });
    });
  }
  
  // Table scroll indicator
  document.querySelectorAll('.data-table-wrapper').forEach(wrapper => {
    const checkScroll = () => {
      if (wrapper.scrollLeft > 10) {
        wrapper.classList.add('scrolled');
      } else {
        wrapper.classList.remove('scrolled');
      }
    };
    
    wrapper.addEventListener('scroll', checkScroll);
    checkScroll();
  });
</script>
</body>
</html>
