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
    $recipient = sanitizeInput($_POST['requester']);
    $amount = floatval($_POST['amount']);
    $description = sanitizeInput($_POST['description']);
    
    if ($amount <= 0) {
        $error = 'Amount must be greater than zero.';
    } else {
        $database = new Database();
        $db = $database->getConnection();

        try {
            // Validate recipient
            $recipientQuery = "SELECT user_id FROM users WHERE username = ? OR email = ?";
            $recipientStmt = $db->prepare($recipientQuery);
            $recipientStmt->execute([$recipient, $recipient]);

            if ($recipientStmt->rowCount() == 0) {
                $error = 'Recipient not found.';
            } else {
                $recipientData = $recipientStmt->fetch(PDO::FETCH_ASSOC);
                $recipientId = $recipientData['user_id'];

                // Create money request
                $requestQuery = "INSERT INTO money_requests 
                                (sender_id, receiver_id, amount, currency, description, status, created_at)
                                VALUES (?, ?, ?, 'USD', ?, 'pending', NOW())";
                $requestStmt = $db->prepare($requestQuery);
                $requestStmt->execute([
                    $_SESSION['user_id'], // sender_id
                    $recipientId,         // receiver_id
                    $amount,              // amount
                    $description          // description
                ]);

                $success = "Money request sent successfully to " . htmlspecialchars($recipient) . ".";
            }
        } catch (Exception $e) {
            $error = "Failed to send request: " . $e->getMessage();
        }
    }
}

$page_title = "Request Money";
include __DIR__ . '/includes/header.php';
?>

<div class="dashboard">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    
    <div class="dashboard-content">
    <br><br><br><br>
        <h2>Request Money</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <form action="request-money.php" method="POST">
                    <div class="form-group">
                        <label for="requester">Recipient (Username or Email)</label>
                        <input type="text" class="form-control" id="requester" name="requester" required>
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
                    
                    <button type="submit" class="btn btn-primary">Send Request</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>