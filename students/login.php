<?php
session_start();
include('../config/db.php');

if (isset($_SESSION['student'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$redirectMessage = $_SESSION['redirect_message'] ?? '';
unset($_SESSION['redirect_message']);

$redirectTo = $_GET['redirect'] ?? '';
$progId = intval($_GET['prog'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT StudentID, FullName, Email, Password FROM students WHERE Email = ?");
            $stmt->execute([$email]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($student && password_verify($password, $student['Password'])) {
                session_regenerate_id(true);
                $_SESSION['student'] = [
                    'StudentID' => $student['StudentID'],
                    'FullName' => $student['FullName'],
                    'Email' => $student['Email']
                ];
                
                if ($redirectTo === 'register_interest' && $progId > 0) {
                    header("Location: ../public/register_interest.php?id=$progId");
                } else {
                    header('Location: dashboard.php');
                }
                exit;
            } else {
                $error = 'Invalid email or password.';
            }
        } catch (PDOException $e) {
            $error = 'Login failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - University of Liverpool</title>
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
            
            <h1>Student Login</h1>
            <p>Access your student dashboard to manage your programme interests.</p>
            
            <?php if ($redirectMessage): ?>
                <div class="alert alert-info"><?php echo htmlspecialchars($redirectMessage); ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="login.php<?php echo $redirectTo ? "?redirect=$redirectTo&prog=$progId" : ''; ?>" class="auth-form">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                           required maxlength="150">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            
            <p class="auth-links">
                Don't have an account? <a href="register.php<?php echo $redirectTo ? "?redirect=$redirectTo&prog=$progId" : ''; ?>">Register here</a><br>
                <a href="../public/index.php">← Back to Home</a>
            </p>
        </div>
    </main>
    
    <?php include('../includes/footer.php'); ?>
</body>
</html>
