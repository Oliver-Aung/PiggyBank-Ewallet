<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitizeInput($_POST['email']);
    
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() == 1) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $reset_token = bin2hex(random_bytes(16)); // Generate a secure token
        $reset_expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expires in 1 hour
        
        // Save the reset token and expiry in the database
        $updateQuery = "UPDATE users SET reset_token = ?, reset_expiry = ? WHERE email = ?";
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->execute([$reset_token, $reset_expiry, $email]);
        
        // Send reset email
        $reset_link = "http://localhost/piggybank-ewallet/reset-password.php?token=$reset_token";
        $subject = "Password Reset Request";
        $message = "Hello, \n\nClick the link below to reset your password:\n$reset_link\n\nThis link will expire in 1 hour.";
        $headers = "From: no-reply@piggybank.com";
        
        if (mail($email, $subject, $message, $headers)) {
            $success = "A password reset link has been sent to your email.";
        } else {
            $error = "Failed to send the reset email. Please try again.";
        }
    } else {
        $error = "No account found with that email address.";
    }
}

$page_title = "Forgot Password";
include __DIR__ . '/includes/header.php';
?>
<br><br><br><br>
<div class="container">
    <div class="auth-card">
        <h2>Forgot Password</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form action="forgot-password.php" method="POST">
            <div class="form-group">
                <label for="email" class="form-label">Enter your email address</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>