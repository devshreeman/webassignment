<?php
include('../config/db.php');
$pageTitle         = 'Manage Users';
$activeSidebarItem = 'users';
include('../includes/admin_header.php');

$msg     = '';
$msgType = 'success';

if (isset($_GET['delete'])) {
    $uid = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$uid]);
    $msg = 'User removed.';
}

$users = $pdo->query("SELECT id, name, email, role, date FROM users ORDER BY date DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="admin-topbar">
  <div>
    <h1 class="admin-page-title">Manage Users</h1>
    <p class="admin-page-subtitle">View all registered users and manage their access.</p>
  </div>
</div>

<?php if ($msg): ?>
  <div class="alert alert--<?= $msgType ?>" role="alert"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<div class="admin-section-card">
  <div class="admin-section-card__header">
    <h2 class="admin-section-card__title">All Users (<?= count($users) ?>)</h2>
  </div>
  <div class="data-table-wrapper">
    <table class="data-table" aria-label="Users list">
      <thead>
        <tr>
          <th scope="col">Name</th>
          <th scope="col">Email</th>
          <th scope="col">Role</th>
          <th scope="col">Joined</th>
          <th scope="col">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($users): ?>
          <?php foreach ($users as $u): ?>
            <tr>
              <td><strong><?= htmlspecialchars($u['name']) ?></strong></td>
              <td><?= htmlspecialchars($u['email']) ?></td>
              <td>
                <span class="status-badge <?= $u['role'] === 'admin' ? 'status-badge--published' : 'status-badge--draft' ?>">
                  <?= htmlspecialchars(ucfirst($u['role'])) ?>
                </span>
              </td>
              <td style="color:var(--color-text-muted);white-space:nowrap;">
                <?= date('d M Y', strtotime($u['date'])) ?>
              </td>
              <td>
                <a href="manage_users.php?delete=<?= $u['id'] ?>"
                   class="btn btn-danger btn-sm"
                   data-confirm="Remove user <?= htmlspecialchars(addslashes($u['name'])) ?>?">
                  Remove
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="5" class="text-center text-muted" style="padding:var(--space-8);">No users registered yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include('../includes/admin_footer.php'); ?>
