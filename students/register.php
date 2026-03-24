<?php
session_start();
include('../config/db.php');

$error = '';
$success = '';
$redirectMessage = $_SESSION['redirect_message'] ?? '';
unset($_SESSION['redirect_message']);

$redirectTo = $_GET['redirect'] ?? '';
$progId = intval($_GET['prog'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['fullName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    
    if (empty($fullName) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = 'All fields except phone are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT StudentID FROM students WHERE Email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $error = 'An account with this email already exists.';
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("INSERT INTO students (FullName, Email, Phone, Password) VALUES (?, ?, ?, ?)");
                $stmt->execute([$fullName, $email, $phone, $hashedPassword]);
                
                $studentId = $pdo->lastInsertId();
                
                session_regenerate_id(true);
                $_SESSION['student'] = [
                    'StudentID' => $studentId,
                    'FullName' => $fullName,
                    'Email' => $email
                ];
                
                if ($redirectTo === 'register_interest' && $progId > 0) {
                    header("Location: ../public/register_interest.php?id=$progId");
                } else {
                    header('Location: dashboard.php');
                }
                exit;
            }
        } catch (PDOException $e) {
            $error = 'Registration failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration - University of Liverpool</title>
    <link rel="icon" type="image/svg+xml" href="../assets/favicon.svg">
    <link rel="stylesheet" href="../css/university.css">
</head>
<body>
    <?php include('../includes/header.php'); ?>
    
    <main class="container">
        <div class="auth-container">
            <a href="../public/index.php" class="auth-card__logo">
                <img src="../assets/logo.svg" alt="" width="48" height="48" aria-hidden="true">
                <div class="auth-card__logo-text">
                    <span>University of Liverpool</span>
                    <span>Student Portal</span>
                </div>
            </a>
            
            <h1>Student Registration</h1>
            <p>Create your student account to manage your programme interests.</p>
            
            <?php if ($redirectMessage): ?>
                <div class="alert alert-info"><?php echo htmlspecialchars($redirectMessage); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="register.php<?php echo $redirectTo ? "?redirect=$redirectTo&prog=$progId" : ''; ?>" class="auth-form">
                <div class="form-group">
                    <label for="fullName">Full Name *</label>
                    <input type="text" id="fullName" name="fullName" 
                           value="<?php echo htmlspecialchars($_POST['fullName'] ?? ''); ?>" 
                           required minlength="2" maxlength="100">
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                           required maxlength="150">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" 
                           value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" 
                           maxlength="20">
                </div>
                
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" 
                           required minlength="8">
                    <small>Minimum 8 characters</small>
                </div>
                
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password *</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" 
                           required minlength="8">
                </div>
                
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
            
            <p class="auth-links">
                Already have an account? <a href="login.php<?php echo $redirectTo ? "?redirect=$redirectTo&prog=$progId" : ''; ?>">Login here</a><br>
                <a href="../public/index.php">← Back to Home</a>
            </p>
        </div>
    </main>
    
    <?php include('../includes/footer.php'); ?>
</body>
</html>
