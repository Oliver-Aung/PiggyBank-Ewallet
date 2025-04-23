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
    $recipient = sanitizeInput($_POST['recipient']);
    $amount = floatval($_POST['amount']);
    $description = sanitizeInput($_POST['description']);

    if ($amount <= 0) {
        $error = 'Amount must be greater than zero.';
    } elseif ($amount > $wallet['balance']) {
        $error = 'Insufficient balance.';
    } else {
        $database = new Database();
        $db = $database->getConnection();

        try {
            $db->beginTransaction();

            // Validate recipient
            $recipientQuery = "SELECT user_id FROM users WHERE username = ? OR email = ?";
            $recipientStmt = $db->prepare($recipientQuery);
            $recipientStmt->execute([$recipient, $recipient]);

            if ($recipientStmt->rowCount() == 0) {
                $error = 'Recipient not found.';
            } else {
                $recipientData = $recipientStmt->fetch(PDO::FETCH_ASSOC);
                $recipientId = $recipientData['user_id'];

                // Deduct amount from sender's wallet
                $deductQuery = "UPDATE wallets SET balance = balance - ? WHERE wallet_id = ?";
                $deductStmt = $db->prepare($deductQuery);
                $deductStmt->execute([$amount, $wallet['wallet_id']]);

                // Add amount to recipient's wallet
                $addQuery = "UPDATE wallets SET balance = balance + ? WHERE user_id = ?";
                $addStmt = $db->prepare($addQuery);
                $addStmt->execute([$amount, $recipientId]);

                // Record transaction for sender
                $senderTransactionQuery = "INSERT INTO transactions 
                                           (wallet_id, amount, transaction_type, description, reference, status, created_at, completed_at)
                                           VALUES (?, ?, 'send', ?, ?, 'completed', NOW(), NOW())";
                $senderTransactionStmt = $db->prepare($senderTransactionQuery);
                $reference = generateReference();
                $senderTransactionStmt->execute([
                    $wallet['wallet_id'],
                    -$amount,
                    $description,
                    $reference
                ]);

                // Record transaction for recipient
                $recipientTransactionQuery = "INSERT INTO transactions 
                                              (wallet_id, amount, transaction_type, description, reference, status, created_at, completed_at)
                                              VALUES (?, ?, 'receive', ?, ?, 'completed', NOW(), NOW())";
                $recipientTransactionStmt = $db->prepare($recipientTransactionQuery);
                $recipientTransactionStmt->execute([
                    $recipientId,
                    $amount,
                    $description,
                    $reference
                ]);

                $db->commit();
                $success = "Successfully sent " . formatCurrency($amount, $wallet['currency']) . " to " . htmlspecialchars($recipient) . ".";
                $wallet = getWalletByUserId($_SESSION['user_id']); // Refresh balance
            }
        } catch (Exception $e) {
            $db->rollBack();
            $error = "Failed to send money: " . $e->getMessage();
        }
    }
}

$page_title = "Send Money";
include __DIR__ . '/includes/header.php';
?>

<div class="dashboard">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    
    <div class="dashboard-content">
    <br><br><br><br>
        <h2>Send Money</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <form action="send-money.php" method="POST">
                    <div class="form-group">
                        <label for="recipient">Recipient (Username or Email)</label>
                        <input type="text" class="form-control" id="recipient" name="recipient" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0.01" class="form-control" id="amount" name="amount" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Send Money</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
