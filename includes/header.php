<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/style.css">

<?php
function base_url() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    return $protocol . $_SERVER['HTTP_HOST'] . '/piggybank-ewallet/';
}

$upload_dir = __DIR__ . '/uploads/profile_pictures/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true); // Create the directory with full permissions
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    
    <!-- Favicon -->
    <link rel="icon" href="/assets/images/favicon.ico" type="image/x-icon">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom JS -->
    <script src="/assets/js/main.js" defer></script>
</head>
<body>
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="index.php" class="logo">
                    <i class="fas fa-piggy-bank"></i> Piggy-Bank
                </a>
                
              
                
                <ul class="nav-links" id="mobile-menu">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="features.php">Features</a></li>
                    <li><a href="pricing.php">Pricing</a></li>
                    <li><a href="about.php">About</a></li>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="dashboard.php">Dashboard</a></li>
                    <?php endif; ?>
                </ul>
                
                <div class="auth-buttons">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="logout.php" class="btn btn-outline">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline">Login</a>
                        <a href="register.php" class="btn btn-primary">Sign Up</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <main class="container">
        <!-- Alerts container -->
        <div id="alerts-container"></div>