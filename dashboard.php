<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

// Get user data
$user = getUserById($_SESSION['user_id']);
if (!$user) {
    die("Error: User not found");
}

// Get wallet data
$wallet = getWalletByUserId($_SESSION['user_id']);
if (!$wallet) {
    die("Error: Wallet not found");
}

// Get recent transactions
$transactions = getTransactionsByWalletId($wallet['wallet_id'], 5); // Limit to 5 recent transactions

// Fetch beneficiaries
$beneficiaries = getBeneficiaries($_SESSION['user_id']);

// Fetch pending requests
$pendingRequests = getPendingRequests($_SESSION['user_id']);

$page_title = "Piggy-Bank eWallet - Dashboard";
include __DIR__ . '/includes/header.php';
?>

<div class="dashboard">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    
    <div class="dashboard-content">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_SESSION['success']); ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($_SESSION['error']); ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <h2>Welcome, <?php echo htmlspecialchars($user['full_name']); ?>!</h2>
        <br>
        <br>
        <!-- Wallet Balance -->
        <div class="balance-card">
            
            <br>
            <h3>Wallet Balance</h3>
            <p><?php echo formatCurrency($wallet['balance'], $wallet['currency']); ?></p>
        </div>
        
        <!-- Quick Actions -->
        <div class="grid">
            <div class="card">
                <a href="deposit.php" class="btn btn-primary">Deposit Money</a>
            </div>
            <div class="card">
                <a href="withdraw.php" class="btn btn-primary">Withdraw Money</a>
            </div>
            <div class="card">
                <a href="send-money.php" class="btn btn-primary">Send Money</a>
            </div>
            <div class="card">
                <a href="request-money.php" class="btn btn-primary">Request Money</a>
            </div>
        </div>
        
        <!-- Recent Transactions -->
        <div class="card">
            <div class="card-header">
                <h3>Recent Transactions</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($transactions)): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $transaction): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($transaction['created_at']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['description']); ?></td>
                                    <td><?php echo formatCurrency($transaction['amount'], $wallet['currency']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['status']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No recent transactions found.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Beneficiaries -->
        <div class="card">
            <div class="card-header">
                <h3>Your Beneficiaries</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($beneficiaries)): ?>
                    <ul class="list-group">
                        <?php foreach ($beneficiaries as $beneficiary): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?php echo htmlspecialchars($beneficiary['full_name']); ?></strong>
                                    <span class="text-muted">(@<?php echo htmlspecialchars($beneficiary['username']); ?>)</span>
                                    <?php if (!empty($beneficiary['nickname'])): ?>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($beneficiary['nickname']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <a href="remove-beneficiary.php?id=<?php echo $beneficiary['user_id']; ?>" class="btn btn-danger btn-sm">Remove</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No beneficiaries added yet. <a href="add-beneficiary.php">Add a beneficiary</a>.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Pending Requests -->
        <div class="card">
            <div class="card-header">
                <h3>Pending Requests</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($pendingRequests)): ?>
                    <ul>
                        <?php foreach ($pendingRequests as $request): ?>
                            <li>
                                <strong><?php echo htmlspecialchars($request['sender_full_name']); ?></strong> 
                                (@<?php echo htmlspecialchars($request['sender_username']); ?>) 
                                requested <?php echo formatCurrency($request['amount'], 'USD'); ?> 
                                for "<?php echo htmlspecialchars($request['description']); ?>" 
                                on <?php echo htmlspecialchars($request['created_at']); ?>.
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No pending requests.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

