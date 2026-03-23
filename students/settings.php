<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['student'])) {
    header('Location: login.php');
    exit;
}

$studentId = $_SESSION['student']['StudentID'];
$message = '';
$msgType = '';

try {
    $stmt = $pdo->prepare("SELECT FullName, Email, Phone FROM students WHERE StudentID = ?");
    $stmt->execute([$studentId]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $student = null;
    $message = 'Failed to load profile information.';
    $msgType = 'error';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['_action'] ?? '';
    
    if ($action === 'update_profile') {
        $fullName = trim($_POST['fullName'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        
        if (empty($fullName) || empty($email)) {
            $message = 'Name and email are required.';
            $msgType = 'error';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = 'Please enter a valid email address.';
            $msgType = 'error';
        } else {
            try {
                $stmt = $pdo->prepare("SELECT StudentID FROM students WHERE Email = ? AND StudentID != ?");
                $stmt->execute([$email, $studentId]);
                
                if ($stmt->rowCount() > 0) {
                    $message = 'This email is already in use by another account.';
                    $msgType = 'error';
                } else {
                    $stmt = $pdo->prepare("UPDATE students SET FullName = ?, Email = ?, Phone = ? WHERE StudentID = ?");
                    $stmt->execute([$fullName, $email, $phone, $studentId]);
                    
                    $_SESSION['student']['FullName'] = $fullName;
                    $_SESSION['student']['Email'] = $email;
                    
                    $student['FullName'] = $fullName;
                    $student['Email'] = $email;
                    $student['Phone'] = $phone;
                    
                    $message = 'Profile updated successfully.';
                    $msgType = 'success';
                }
            } catch (PDOException $e) {
                $message = 'Failed to update profile. Please try again.';
                $msgType = 'error';
            }
        }
    } elseif ($action === 'change_password') {
        $currentPassword = $_POST['currentPassword'] ?? '';
        $newPassword = $_POST['newPassword'] ?? '';
        $confirmPassword = $_POST['confirmPassword'] ?? '';
        
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $message = 'All password fields are required.';
            $msgType = 'error';
        } elseif (strlen($newPassword) < 8) {
            $message = 'New password must be at least 8 characters long.';
            $msgType = 'error';
        } elseif ($newPassword !== $confirmPassword) {
            $message = 'New passwords do not match.';
            $msgType = 'error';
        } else {
            try {
                $stmt = $pdo->prepare("SELECT Password FROM students WHERE StudentID = ?");
                $stmt->execute([$studentId]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($row && password_verify($currentPassword, $row['Password'])) {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE students SET Password = ? WHERE StudentID = ?");
                    $stmt->execute([$hashedPassword, $studentId]);
                    
                    $message = 'Password changed successfully.';
                    $msgType = 'success';
                } else {
                    $message = 'Current password is incorrect.';
                    $msgType = 'error';
                }
            } catch (PDOException $e) {
                $message = 'Failed to change password. Please try again.';
                $msgType = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Settings - University of Liverpool</title>
    <link rel="stylesheet" href="../css/university.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    
    <section style="background: var(--color-surface); border-bottom: 1px solid var(--color-border); padding: var(--space-8) 0;">
      <div class="container">
        <div class="admin-topbar" style="margin-bottom: 0;">
          <div>
            <h1 class="admin-page-title" style="margin-bottom: var(--space-1);">Profile Settings</h1>
            <p class="admin-page-subtitle">Manage your profile information and password.</p>
          </div>
        </div>
      </div>
    </section>
    
    <section class="section">
      <div class="container">
        <?php if ($message): ?>
          <div class="alert alert-<?php echo htmlspecialchars($msgType); ?>" style="margin-bottom: var(--space-6);">
            <?php echo htmlspecialchars($message); ?>
          </div>
        <?php endif; ?>
        
        <div class="grid grid-2" style="align-items:start; gap:var(--space-8);">
          
          <div>
            <div class="section-header section-header--left" style="margin-bottom:var(--space-6);">
              <h2 class="section-header__title" style="font-size:var(--text-2xl);">Profile Information</h2>
              <div class="section-divider"></div>
            </div>
            
            <form method="POST" action="settings.php" class="form-card">
              <input type="hidden" name="_action" value="update_profile">
              
              <div class="form-group">
                <label for="fullName" class="form-label">Full Name *</label>
                <input type="text" id="fullName" name="fullName" class="form-input"
                       value="<?php echo htmlspecialchars($student['FullName'] ?? ''); ?>" 
                       required minlength="2" maxlength="100">
              </div>
              
              <div class="form-group">
                <label for="email" class="form-label">Email Address *</label>
                <input type="email" id="email" name="email" class="form-input"
                       value="<?php echo htmlspecialchars($student['Email'] ?? ''); ?>" 
                       required maxlength="150">
              </div>
              
              <div class="form-group">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="tel" id="phone" name="phone" class="form-input"
                       value="<?php echo htmlspecialchars($student['Phone'] ?? ''); ?>" 
                       maxlength="20">
              </div>
              
              <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
          </div>
          
          <div>
            <div class="section-header section-header--left" style="margin-bottom:var(--space-6);">
              <h2 class="section-header__title" style="font-size:var(--text-2xl);">Change Password</h2>
              <div class="section-divider"></div>
            </div>
            
            <form method="POST" action="settings.php" class="form-card">
              <input type="hidden" name="_action" value="change_password">
              
              <div class="form-group">
                <label for="currentPassword" class="form-label">Current Password *</label>
                <input type="password" id="currentPassword" name="currentPassword" class="form-input" required>
              </div>
              
              <div class="form-group">
                <label for="newPassword" class="form-label">New Password *</label>
                <input type="password" id="newPassword" name="newPassword" class="form-input"
                       required minlength="8">
                <small class="text-muted">Minimum 8 characters</small>
              </div>
              
              <div class="form-group">
                <label for="confirmPassword" class="form-label">Confirm New Password *</label>
                <input type="password" id="confirmPassword" name="confirmPassword" class="form-input"
                       required minlength="8">
              </div>
              
              <button type="submit" class="btn btn-primary">Change Password</button>
            </form>
          </div>
          
        </div>
        
        <div style="margin-top: var(--space-8);">
          <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
        </div>
      </div>
    </section>
    
    <?php include('../includes/footer.php'); ?>
</body>
</html>
