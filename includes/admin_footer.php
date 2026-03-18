<?php
/**
 * Shared Admin Footer — closes the admin layout
 */
?>
  </main><!-- /.admin-main -->
</div><!-- /.admin-layout -->

<script>
  // Confirm dialogs for destructive actions
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', e => {
      if (!confirm(el.dataset.confirm)) e.preventDefault();
    });
  });

  // Auto-dismiss alerts after 5s
  document.querySelectorAll('.alert').forEach(alert => {
    setTimeout(() => {
      alert.style.transition = 'opacity 0.5s';
      alert.style.opacity = '0';
      setTimeout(() => alert.remove(), 500);
    }, 5000);
  });
</script>
</body>
</html>
