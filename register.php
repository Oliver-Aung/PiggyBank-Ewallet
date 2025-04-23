<?php
// Start session at the VERY TOP
session_start();

// Verify we're not already logged in
if (isset($_SESSION['user_id'])) {
    // Clear any existing session if we're hitting register page
    session_destroy();
}

// Set default values
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require_once __DIR__ . '/includes/functions.php';
    
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);
    $confirm_password = sanitizeInput($_POST['confirm_password']);
    $full_name = sanitizeInput($_POST['full_name']);
    
    // Validate passwords match
    if ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        $database = new Database();
        $db = $database->getConnection();
        
        // Check if user exists
        $checkQuery = "SELECT * FROM users WHERE username = ? OR email = ?";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->execute([$username, $email]);
        
        if ($checkStmt->rowCount() > 0) {
            $error = 'Username or email already exists';
        } else {
            // Create new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $insertQuery = "INSERT INTO users (username, email, password, full_name, created_at) 
                           VALUES (?, ?, ?, ?, NOW())";
            $insertStmt = $db->prepare($insertQuery);
            
            if ($insertStmt->execute([$username, $email, $hashed_password, $full_name])) {
                $success = 'Registration successful! You can now login.';
                
                // DON'T login automatically - let user click login
                // Clear any session variables just in case
                session_unset();
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}

$page_title = "Piggy-Bank eWallet - Register";
include __DIR__ . '/includes/header.php';
?>
<br><br><br><br>
<div class="auth-container">
    <div class="auth-card">
        <h2>Create Your Account</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <div class="text-center">
                <a href="login.php" class="btn btn-primary">Login Now</a>
            </div>
        <?php else: ?>
            <form action="register.php" method="POST" autocomplete="off">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" id="full_name" name="full_name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required minlength="6">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Register</button>
                </div>
                
                <div class="auth-footer">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>