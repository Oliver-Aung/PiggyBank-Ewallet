<?php
require_once __DIR__ . '/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

// Fetch the latest user data
$user = getUserById($_SESSION['user_id']);
$profile_picture = !empty($user['profile_picture']) ? $user['profile_picture'] : 'assets/images/default-avatar.jpg'; // Default profile picture
?>

<div class="sidebar">
    <br><br><br><br>
    <div class="profile text-center">
        <!-- Profile Picture -->
        <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="profile-img rounded-circle">
        
        <!-- User Information -->
        <h4 class="mt-2"><?php echo htmlspecialchars($user['full_name']); ?></h4>
        <p class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></p>
    </div>
    
    <!-- Sidebar Menu -->
    <ul class="sidebar-menu mt-4">
        <li><a href="dashboard.php" <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'class="active"' : ''; ?>>
            <i class="fas fa-home"></i> Dashboard
        </a></li>
        <li><a href="deposit.php" <?php echo basename($_SERVER['PHP_SELF']) == 'deposit.php' ? 'class="active"' : ''; ?>>
            <i class="fas fa-plus-circle"></i> Deposit
        </a></li>
        <li><a href="withdraw.php" <?php echo basename($_SERVER['PHP_SELF']) == 'withdraw.php' ? 'class="active"' : ''; ?>>
            <i class="fas fa-minus-circle"></i> Withdraw
        </a></li>
        <li><a href="send-money.php" <?php echo basename($_SERVER['PHP_SELF']) == 'send-money.php' ? 'class="active"' : ''; ?>>
            <i class="fas fa-paper-plane"></i> Send Money
        </a></li>
        <li><a href="request-money.php" <?php echo basename($_SERVER['PHP_SELF']) == 'request-money.php' ? 'class="active"' : ''; ?>>
            <i class="fas fa-hand-holding-usd"></i> Request Money
        </a></li>
        <li><a href="transactions.php" <?php echo basename($_SERVER['PHP_SELF']) == 'transactions.php' ? 'class="active"' : ''; ?>>
            <i class="fas fa-history"></i> Transactions
        </a></li>
        <li><a href="settings.php" <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'class="active"' : ''; ?>>
            <i class="fas fa-cog"></i> Settings
        </a></li>
    </ul>
</div>