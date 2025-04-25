<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user = getUserById($_SESSION['user_id']);
$wallet = getWalletByUserId($_SESSION['user_id']);
$transactions = getTransactionsByWalletId($wallet['wallet_id'], 50); // Fetch up to 50 transactions

$page_title = "Transaction History";
include __DIR__ . '/includes/header.php';
?>

<div class="dashboard">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    
    <div class="dashboard-content">
        <br><br><br><br>
        <h2>Transaction History</h2>
        <br><br>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <?php if (!empty($transactions)): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $transaction): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($transaction['created_at']); ?></td>
                                        <td><?php echo htmlspecialchars($transaction['description']); ?></td>
                                        <td><?php echo ucfirst(htmlspecialchars($transaction['transaction_type'])); ?></td>
                                        <td>
                                            <?php 
                                                $amount = formatCurrency($transaction['amount'], $wallet['currency']);
                                                echo $transaction['transaction_type'] === 'withdrawal' || $transaction['transaction_type'] === 'send' 
                                                    ? '-' . $amount 
                                                    : '+' . $amount;
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($transaction['status']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No transactions found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
