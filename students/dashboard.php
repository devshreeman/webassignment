<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['student'])) {
    header('Location: login.php');
    exit;
}

$studentId = $_SESSION['student']['StudentID'];
$studentName = $_SESSION['student']['FullName'];

$message = '';
$msgType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['_action'] ?? '') === 'remove_interest') {
    $interestId = intval($_POST['interestId'] ?? 0);
    
    try {
        $stmt = $pdo->prepare("DELETE FROM interestedstudents WHERE InterestID = ? AND StudentID = ?");
        $stmt->execute([$interestId, $studentId]);
        
        if ($stmt->rowCount() > 0) {
            $message = 'Interest removed successfully.';
            $msgType = 'success';
        } else {
            $message = 'Interest not found or already removed.';
            $msgType = 'error';
        }
    } catch (PDOException $e) {
        $message = 'Failed to remove interest. Please try again.';
        $msgType = 'error';
    }
}

try {
    $stmt = $pdo->prepare("
        SELECT i.InterestID, p.ProgrammeName, l.LevelName, i.RegisteredAt
        FROM interestedstudents i
        JOIN programmes p ON i.ProgrammeID = p.ProgrammeID
        LEFT JOIN levels l ON p.LevelID = l.LevelID
        WHERE i.StudentID = ?
        ORDER BY i.RegisteredAt DESC
    ");
    $stmt->execute([$studentId]);
    $interests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $interests = [];
    $message = 'Failed to load your interests: ' . $e->getMessage();
    $msgType = 'error';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - University of Liverpool</title>
    <link rel="stylesheet" href="../css/university.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    
    <main class="container">
        <div class="dashboard-header">
            <h1>Welcome, <?php echo htmlspecialchars($studentName); ?>!</h1>
            <p>Manage your programme interests and profile settings.</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo htmlspecialchars($msgType); ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="dashboard-actions">
            <a href="settings.php" class="btn btn-secondary">Profile Settings</a>
            <a href="../public/index.php#programmes" class="btn btn-primary">Browse Programmes</a>
        </div>
        
        <section class="dashboard-section">
            <h2>Your Programme Interests</h2>
            
            <?php if (empty($interests)): ?>
                <div class="empty-state">
                    <p>You haven't registered interest in any programmes yet.</p>
                    <a href="../public/index.php#programmes" class="btn btn-primary">Browse Programmes</a>
                </div>
            <?php else: ?>
                <div class="interests-grid">
                    <?php foreach ($interests as $interest): ?>
                        <div class="interest-card">
                            <h3><?php echo htmlspecialchars($interest['ProgrammeName']); ?></h3>
                            <p class="interest-level"><?php echo htmlspecialchars($interest['LevelName']); ?></p>
                            <p class="interest-date">
                                Registered: <?php echo date('M d, Y', strtotime($interest['RegisteredAt'])); ?>
                            </p>
                            <form method="POST" action="dashboard.php" class="interest-remove-form">
                                <input type="hidden" name="_action" value="remove_interest">
                                <input type="hidden" name="interestId" value="<?php echo $interest['InterestID']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm" 
                                        onclick="return confirm('Are you sure you want to remove this interest?');">
                                    Remove Interest
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
    
    <?php include('../includes/footer.php'); ?>
</body>
</html>
