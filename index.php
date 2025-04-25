<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

if (isLoggedIn()) {
    redirect('dashboard.php');
}

$page_title = "Piggy-Bank eWallet - Home";
include __DIR__ . '/includes/header.php';
?>

<div class="hero">
    <br><br><br><br><br>
    <div class="container">
        <div class="hero-content">
            <h1>Modern Digital Wallet for Everyone</h1>
            <p>Send, receive, and manage your money with ease using Piggy-Bank eWallet.</p>
            <div class="hero-buttons">
                <a href="register.php" class="btn btn-primary">Get Started</a>
                <a href="login.php" class="btn btn-outline">Learn More</a>
            </div>
        </div>
        <div class="hero-image">
          
        </div>
    </div>
</div>

<div class="features">
    <div class="container">
        <h2>Why Choose Piggy-Bank?</h2>
        <div class="grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3>Fast Transactions</h3>
                <p>Send and receive money instantly with just a few taps.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Secure</h3>
                <p>Bank-level security to keep your money safe.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-globe"></i>
                </div>
                <h3>Global</h3>
                <p>Send money to anyone, anywhere in the world.</p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
