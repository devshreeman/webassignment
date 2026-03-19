<?php
include('../config/db.php');
$pageTitle         = 'Contact Messages';
$activeSidebarItem = 'contact';
include('../includes/admin_header.php');

$msg     = '';
$msgType = 'success';

/* Handle Mark as Read */
if (isset($_GET['mark_read'])) {
    $id = (int)$_GET['mark_read'];
    $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?")->execute([$id]);
    $msg = 'Message marked as read.';
}

/* Handle Message Deletion */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM contact_messages WHERE id = ?")->execute([$id]);
    $msg = 'Message deleted.';
}

/* Fetch All Contact Messages */
try {
    $messages = $pdo->query("
        SELECT * FROM contact_messages 
        ORDER BY is_read ASC, created_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    $unreadCount = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
} catch (PDOException $e) {
    /* Table doesn't exist yet */
    $messages = [];
    $unreadCount = 0;
}
?>

<div class="admin-topbar">
  <div>
    <h1 class="admin-page-title">Contact Messages</h1>
    <p class="admin-page-subtitle">
      View and manage enquiries submitted through the contact form.
      <?php if ($unreadCount > 0): ?>
        <span style="display:inline-block;background:var(--color-danger);color:#fff;padding:0.15rem 0.5rem;border-radius:9999px;font-size:0.75rem;font-weight:700;margin-left:0.5rem;">
          <?= $unreadCount ?> unread
        </span>
      <?php endif; ?>
    </p>
  </div>
</div>

<?php if ($msg): ?>
  <div class="alert alert--<?= $msgType ?>" role="alert"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="admin-section-card">
  <div class="admin-section-card__header">
    <h2 class="admin-section-card__title">All Messages (<?= count($messages) ?>)</h2>
  </div>
  
  <?php if ($messages): ?>
    <div style="display:flex;flex-direction:column;gap:var(--space-4);">
      <?php foreach ($messages as $m): ?>
        <div style="border:1px solid var(--color-border);border-radius:var(--radius-md);padding:var(--space-5);background:<?= $m['is_read'] ? 'var(--color-bg)' : 'var(--color-surface)' ?>;">
          <div style="display:flex;justify-content:space-between;align-items:start;margin-bottom:var(--space-3);">
            <div style="flex:1;">
              <div style="display:flex;align-items:center;gap:var(--space-3);margin-bottom:var(--space-2);">
                <h3 style="font-size:var(--text-lg);font-weight:600;margin:0;">
                  <?= htmlspecialchars($m['name']) ?>
                </h3>
                <?php if (!$m['is_read']): ?>
                  <span style="display:inline-block;background:var(--color-primary);color:#fff;padding:0.15rem 0.5rem;border-radius:9999px;font-size:0.65rem;font-weight:700;letter-spacing:0.05em;text-transform:uppercase;">
                    New
                  </span>
                <?php endif; ?>
              </div>
              <div style="display:flex;gap:var(--space-4);font-size:var(--text-sm);color:var(--color-text-muted);">
                <span>
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:0.25rem;">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                    <polyline points="22,6 12,13 2,6"/>
                  </svg>
                  <?= htmlspecialchars($m['email']) ?>
                </span>
                <span>
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:0.25rem;">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                  </svg>
                  <?= date('d M Y, H:i', strtotime($m['created_at'])) ?>
                </span>
              </div>
            </div>
            <div class="td-actions" style="flex-shrink:0;">
              <?php if (!$m['is_read']): ?>
                <a href="contact_messages.php?mark_read=<?= $m['id'] ?>" class="btn btn-primary btn-sm">Mark as Read</a>
              <?php endif; ?>
              <a href="contact_messages.php?delete=<?= $m['id'] ?>" 
                 class="btn btn-danger btn-sm"
                 data-confirm="Delete this message? This cannot be undone.">
                Delete
              </a>
            </div>
          </div>
          
          <div style="margin-bottom:var(--space-3);">
            <span style="display:inline-block;background:var(--color-primary);color:var(--color-accent);padding:0.25rem 0.75rem;border-radius:var(--radius-sm);font-size:var(--text-xs);font-weight:600;">
              <?= htmlspecialchars($m['subject']) ?>
            </span>
          </div>
          
          <div style="background:var(--color-surface);border-left:3px solid var(--color-primary);padding:var(--space-4);border-radius:var(--radius-sm);font-size:var(--text-sm);line-height:1.7;color:var(--color-text);">
            <?= nl2br(htmlspecialchars($m['message'])) ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="empty-state" style="padding:var(--space-10);">
      <div class="empty-state__icon" aria-hidden="true">📬</div>
      <h3 class="empty-state__title">No messages yet</h3>
      <p>Contact form submissions will appear here.</p>
    </div>
  <?php endif; ?>
</div>

<?php include('../includes/admin_footer.php'); ?>
