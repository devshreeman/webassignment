<?php
// connect to database
include('../config/db.php');

// make sure session is started and staff is logged in
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['staff'])) {
    header("Location: login.php");
    exit;
}

// grab staff ID and setup messages
$staffId = $_SESSION['staff']['StaffID'];
$message = '';
$msgType = '';

// load current staff info
try {
    $stmt = $pdo->prepare("SELECT Name, Email, Phone, Photo FROM staff WHERE StaffID = ?");
    $stmt->execute([$staffId]);
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $staff = null;
    $message = 'Failed to load profile information.';
    $msgType = 'error';
}

// handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['_action'] ?? '';
    
    // update profile info
    if ($action === 'update_profile') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        
        // validate required fields
        if (empty($name) || empty($email)) {
            $message = 'Name and email are required.';
            $msgType = 'error';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = 'Please enter a valid email address.';
            $msgType = 'error';
        } else {
            try {
                // check if email is already taken by another staff member
                $stmt = $pdo->prepare("SELECT StaffID FROM staff WHERE Email = ? AND StaffID != ?");
                $stmt->execute([$email, $staffId]);
                
                if ($stmt->rowCount() > 0) {
                    $message = 'This email is already in use by another staff member.';
                    $msgType = 'error';
                } else {
                    // update staff profile
                    $stmt = $pdo->prepare("UPDATE staff SET Name = ?, Email = ?, Phone = ? WHERE StaffID = ?");
                    $stmt->execute([$name, $email, $phone, $staffId]);
                    
                    // update session with new info
                    $_SESSION['staff']['Name'] = $name;
                    $_SESSION['staff']['Email'] = $email;
                    
                    // update local variable too
                    $staff['Name'] = $name;
                    $staff['Email'] = $email;
                    $staff['Phone'] = $phone;
                    
                    $message = 'Profile updated successfully.';
                    $msgType = 'success';
                }
            } catch (PDOException $e) {
                $message = 'Failed to update profile. Please try again.';
                $msgType = 'error';
            }
        }
    } elseif ($action === 'upload_photo') {
        // handle photo upload
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['photo'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 5 * 1024 * 1024;
            
            // check file type for security
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            // validate file type and size
            if (!in_array($mimeType, $allowedTypes)) {
                $message = 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.';
                $msgType = 'error';
            } elseif ($file['size'] > $maxSize) {
                $message = 'File size exceeds 5MB limit.';
                $msgType = 'error';
            } else {
                // generate unique filename
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = 'staff_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
                $uploadPath = '../uploads/staff/' . $filename;
                
                // make sure upload directory exists
                if (!is_dir('../uploads/staff')) {
                    mkdir('../uploads/staff', 0755, true);
                }
                
                // move uploaded file
                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    // delete old photo if it exists
                    if (!empty($staff['Photo']) && file_exists('../uploads/staff/' . $staff['Photo'])) {
                        unlink('../uploads/staff/' . $staff['Photo']);
                    }
                    
                    // update database with new photo
                    $stmt = $pdo->prepare("UPDATE staff SET Photo = ? WHERE StaffID = ?");
                    $stmt->execute([$filename, $staffId]);
                    
                    $staff['Photo'] = $filename;
                    
                    $message = 'Photo uploaded successfully.';
                    $msgType = 'success';
                } else {
                    $message = 'Failed to upload photo. Please try again.';
                    $msgType = 'error';
                }
            }
        } else {
            $message = 'Please select a photo to upload.';
            $msgType = 'error';
        }
    } elseif ($action === 'remove_photo') {
        // handle photo removal
        if (!empty($staff['Photo'])) {
            $photoPath = '../uploads/staff/' . $staff['Photo'];
            if (file_exists($photoPath)) {
                unlink($photoPath);
            }
            
            // clear photo from database
            $stmt = $pdo->prepare("UPDATE staff SET Photo = NULL WHERE StaffID = ?");
            $stmt->execute([$staffId]);
            
            $staff['Photo'] = null;
            
            $message = 'Photo removed successfully.';
            $msgType = 'success';
        }
    } elseif ($action === 'change_password') {
        // handle password change
        $currentPassword = $_POST['currentPassword'] ?? '';
        $newPassword = $_POST['newPassword'] ?? '';
        $confirmPassword = $_POST['confirmPassword'] ?? '';
        
        // validate password fields
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
                // verify current password is correct
                $stmt = $pdo->prepare("SELECT password FROM staff WHERE StaffID = ?");
                $stmt->execute([$staffId]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($row && password_verify($currentPassword, $row['password'])) {
                    // hash and save new password
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE staff SET password = ? WHERE StaffID = ?");
                    $stmt->execute([$hashedPassword, $staffId]);
                    
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

$pageTitle = 'Staff Settings';
$activePage = 'staff-settings';
$cssBase = '../';
$rootBase = '../';

include('../includes/header.php');
?>

<section style="background: var(--color-surface); border-bottom: 1px solid var(--color-border); padding: var(--space-8) 0;">
  <div class="container">
    <div class="admin-topbar" style="margin-bottom: 0;">
      <div>
        <h1 class="admin-page-title" style="margin-bottom: var(--space-1);">Profile Settings</h1>
        <p class="admin-page-subtitle">Manage your profile information, photo, and password.</p>
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
            <label for="name" class="form-label">Name *</label>
            <input type="text" id="name" name="name" class="form-input"
                   value="<?php echo htmlspecialchars($staff['Name'] ?? ''); ?>" 
                   required minlength="2" maxlength="100">
          </div>
          
          <div class="form-group">
            <label for="email" class="form-label">Email Address *</label>
            <input type="email" id="email" name="email" class="form-input"
                   value="<?php echo htmlspecialchars($staff['Email'] ?? ''); ?>" 
                   required maxlength="150">
          </div>
          
          <div class="form-group">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="tel" id="phone" name="phone" class="form-input"
                   value="<?php echo htmlspecialchars($staff['Phone'] ?? ''); ?>" 
                   maxlength="20">
          </div>
          
          <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
        
        <div class="section-header section-header--left" style="margin-top:var(--space-8); margin-bottom:var(--space-6);">
          <h2 class="section-header__title" style="font-size:var(--text-2xl);">Profile Photo</h2>
          <div class="section-divider"></div>
        </div>
        
        <div class="form-card">
          <?php if (!empty($staff['Photo'])): ?>
            <div style="margin-bottom: var(--space-4);">
              <img src="../uploads/staff/<?php echo htmlspecialchars($staff['Photo']); ?>" 
                   alt="Profile Photo" 
                   style="max-width: 200px; border-radius: var(--radius-md); border: 1px solid var(--color-border);">
            </div>
            <form method="POST" action="settings.php" style="display:inline;">
              <input type="hidden" name="_action" value="remove_photo">
              <button type="submit" class="btn btn-danger" 
                      onclick="return confirm('Are you sure you want to remove your photo?');">
                Remove Photo
              </button>
            </form>
          <?php else: ?>
            <p class="text-muted" style="margin-bottom: var(--space-4);">No photo uploaded.</p>
          <?php endif; ?>
          
          <form method="POST" action="settings.php" enctype="multipart/form-data" style="margin-top: var(--space-4);">
            <input type="hidden" name="_action" value="upload_photo">
            <div class="form-group">
              <label for="photo" class="form-label">Upload New Photo</label>
              <input type="file" id="photo" name="photo" class="form-input" 
                     accept="image/jpeg,image/png,image/gif,image/webp" required>
              <small class="text-muted">Max 5MB. Formats: JPG, PNG, GIF, WebP</small>
            </div>
            <button type="submit" class="btn btn-primary">Upload Photo</button>
          </form>
        </div>
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
