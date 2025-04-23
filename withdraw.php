<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user = getUserById($_SESSION['user_id']);
$wallet = getWalletByUserId($_SESSION['user_id']);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = floatval($_POST['amount']);
    
    if ($amount <= 0) {
        $error = 'Amount must be greater than zero';
    } elseif ($amount > $wallet['balance']) {
        $error = 'Insufficient balance';
    } else {
        $database = new Database();
        $db = $database->getConnection();
        
        try {
            $db->beginTransaction();
            
            // Update wallet balance
            $updateQuery = "UPDATE wallets SET balance = balance - ? WHERE wallet_id = ?";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->execute([$amount, $wallet['wallet_id']]);
            
            // Record transaction
            $transactionQuery = "INSERT INTO transactions 
                                (wallet_id, amount, transaction_type, description, reference, status, created_at, completed_at)
                                VALUES (?, ?, 'withdrawal', ?, ?, 'completed', NOW(), NOW())";
            $transactionStmt = $db->prepare($transactionQuery);
            $reference = generateReference();
            $transactionStmt->execute([
                $wallet['wallet_id'],
                $amount,
                "Withdrawal from wallet",
                $reference
            ]);
            
            $db->commit();
            $success = "Successfully withdrew " . formatCurrency($amount, $wallet['currency']);
            $wallet = getWalletByUserId($_SESSION['user_id']); // Refresh balance
        } catch (Exception $e) {
            $db->rollBack();
            $error = "Withdrawal failed: " . $e->getMessage();
        }
    }
}

$page_title = "Withdraw Money";
include __DIR__ . '/includes/header.php';
?>

<div class="dashboard">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    
    <div class="dashboard-content">
        <h2>Withdraw Money</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <br><br>
        <div class="card">
            <div class="card-body">
                <form action="withdraw.php" method="POST">
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0.01" class="form-control" id="amount" name="amount" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Withdraw</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>