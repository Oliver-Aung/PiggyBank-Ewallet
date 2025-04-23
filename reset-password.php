<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

$error = '';
$success = '';

if (isset($_GET['token'])) {
    $token = sanitizeInput($_GET['token']);
    
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT * FROM users WHERE reset_token = ? AND reset_expiry > NOW()";
    $stmt = $db->prepare($query);
    $stmt->execute([$token]);
    
    if ($stmt->rowCount() == 1) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $password = sanitizeInput($_POST['password']);
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Update the password and clear the reset token
            $updateQuery = "UPDATE users SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE user_id = ?";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->execute([$hashed_password, $user['user_id']]);
            
            $success = "Your password has been reset successfully. You can now <a href='login.php'>login</a>.";
        }
    } else {
        $error = "Invalid or expired reset token.";
    }
} else {
    $error = "No reset token provided.";
}

$page_title = "Reset Password";
include __DIR__ . '/includes/header.php';
?>
<br><br><br>
<div class="container">
    <div class="auth-card">
        <h2>Reset Password</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <?php if (empty($success)): ?>
            <form action="reset-password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST">
                <div class="form-group">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>