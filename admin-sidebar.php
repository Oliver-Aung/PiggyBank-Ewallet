<?php
require_once __DIR__ . '/functions.php';

if (!isAdmin()) {
    redirect('login.php');
}
?>

<div class="sidebar">
    <div class="profile text-center">
        <h4>Admin Panel</h4>
    </div>

    <ul class="sidebar-menu mt-4">
        <li><a href="admin-dashboard.php" <?php echo basename($_SERVER['PHP_SELF']) == 'admin-dashboard.php' ? 'class="active"' : ''; ?>>
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a></li>
        <li><a href="manage-users.php" <?php echo basename($_SERVER['PHP_SELF']) == 'manage-users.php' ? 'class="active"' : ''; ?>>
            <i class="fas fa-users"></i> Manage Users
        </a></li>
        <li><a href="manage-transactions.php" <?php echo basename($_SERVER['PHP_SELF']) == 'manage-transactions.php' ? 'class="active"' : ''; ?>>
            <i class="fas fa-exchange-alt"></i> Manage Transactions
        </a></li>
        <li><a href="settings.php" <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'class="active"' : ''; ?>>
            <i class="fas fa-cog"></i> Settings
        </a></li>
    </ul>
</div>