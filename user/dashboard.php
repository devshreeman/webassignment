<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];

// Handle interest actions
if (isset($_GET['action']) && isset($_GET['programme_id'])) {
    $programme_id = (int)$_GET['programme_id'];

    if ($_GET['action'] === 'add') {
        $stmt = $pdo->prepare("
            INSERT INTO interestedstudents (ProgrammeID, StudentName, Email, RegisteredAt)
            VALUES (?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE RegisteredAt = NOW()
        ");
        $stmt->execute([$programme_id, $user['name'], $user['email']]);
    } elseif ($_GET['action'] === 'remove') {
        $stmt = $pdo->prepare("DELETE FROM interestedstudents WHERE ProgrammeID = ? AND Email = ?");
        $stmt->execute([$programme_id, $user['email']]);
    }

    header("Location: dashboard.php");
    exit;
}

$selectedLevel = isset($_GET['level']) ? (int)$_GET['level'] : 0;
$levels = $pdo->query("SELECT * FROM levels ORDER BY LevelName")->fetchAll(PDO::FETCH_ASSOC);

$interestStmt = $pdo->prepare("SELECT ProgrammeID FROM interestedstudents WHERE Email = ?");
$interestStmt->execute([$user['email']]);
$userInterests = $interestStmt->fetchAll(PDO::FETCH_COLUMN);

if ($selectedLevel > 0) {
    $stmt = $pdo->prepare("
        SELECT p.*, l.LevelName 
        FROM programmes p
        JOIN levels l ON p.LevelID = l.LevelID
        WHERE p.LevelID = ?
        ORDER BY p.ProgrammeName
    ");
    $stmt->execute([$selectedLevel]);
} else {
    $stmt = $pdo->query("
        SELECT p.*, l.LevelName 
        FROM programmes p
        JOIN levels l ON p.LevelID = l.LevelID
        ORDER BY p.ProgrammeName
    ");
}
$programmes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Image function (same as index)
function getProgrammeImage($name) {
    $name = strtolower($name);
    if (strpos($name, 'artificial intelligence') !== false) {
        return 'https://images.unsplash.com/photo-1677442136019-21780ecad995?auto=format&fit=crop&w=1000&q=80';
    } elseif (strpos($name, 'computer science') !== false) {
        return 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=800&q=80';
    } elseif (strpos($name, 'cyber') !== false) {
        return 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=800&q=80';
    } elseif (strpos($name, 'software') !== false) {
        return 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=800&q=80';
    } elseif (strpos($name, 'business') !== false) {
        return 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=800&q=80';
    } else {
        return 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=800&q=80';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    body { background: #f4f6f8; font-family: Arial, sans-serif; }
    .programme-card {
      background: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      margin-bottom: 25px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .programme-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 18px rgba(0,0,0,0.15);
    }
    .programme-card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-bottom: 3px solid #0066cc;
      transition: transform 0.3s ease;
    }
    .programme-card:hover img { transform: scale(1.05); }
    .card-body { padding: 15px; }
    .card-body h5 { color: #003366; margin-bottom: 6px; }
    .btn { display: inline-block; padding: 6px 12px; border-radius: 6px; text-decoration: none; color: #fff; }
    .btn-primary { background: #0066cc; }
    .btn-success { background: #28a745; }
    .btn-danger { background: #dc3545; }
  </style>
</head>
<body>

<header class="main-header">
  <div class="header-container">
    <div class="header-left">
      <a href="dashboard.php" class="site-title">Student Course Hub</a>
    </div>
    <div class="header-right">
      <a href="../public/index.php" class="nav-link">Home</a>
      <a href="logout.php" class="nav-link btn-logout">Logout</a>
    </div>
  </div>
</header>

<div class="container">
  <div class="dashboard-header">
    <h2>Welcome, <?= htmlspecialchars($user['name']); ?> 👋</h2>
    <p>Explore available programmes and manage your academic interests.</p>
  </div>

  <!-- FILTER -->
  <div class="filter-section">
    <form method="GET">
      <label for="level">Filter by Level:</label>
      <select name="level" id="level" onchange="this.form.submit()">
        <option value="0">All Levels</option>
        <?php foreach ($levels as $level): ?>
          <option value="<?= $level['LevelID']; ?>" <?= ($selectedLevel == $level['LevelID']) ? 'selected' : ''; ?>>
            <?= htmlspecialchars($level['LevelName']); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>
  </div>

  <!-- PROGRAMMES -->
  <?php if ($programmes): ?>
    <div class="row">
      <?php foreach ($programmes as $p): ?>
        <div class="programme-card">
          <img src="<?= getProgrammeImage($p['ProgrammeName']); ?>" alt="<?= htmlspecialchars($p['ProgrammeName']); ?>">
          <div class="card-body">
            <h5><?= htmlspecialchars($p['ProgrammeName']); ?></h5>
            <p><strong>Level:</strong> <?= htmlspecialchars($p['LevelName']); ?></p>
            <p><?= nl2br(htmlspecialchars(substr($p['Description'], 0, 150))); ?>...</p>
            <a href="../public/programme.php?id=<?= $p['ProgrammeID']; ?>" class="btn btn-primary">View Details</a>
            <?php if (in_array($p['ProgrammeID'], $userInterests)): ?>
              <a href="?action=remove&programme_id=<?= $p['ProgrammeID']; ?>" class="btn btn-danger">Remove Interest</a>
            <?php else: ?>
              <a href="?action=add&programme_id=<?= $p['ProgrammeID']; ?>" class="btn btn-success">Register Interest</a>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p>No programmes available for this level.</p>
  <?php endif; ?>
</div>

<footer class="text-center mt-5 p-3 bg-primary text-white">
  &copy; <?= date('Y'); ?> Student Course Hub | All Rights Reserved
</footer>

</body>
</html>
