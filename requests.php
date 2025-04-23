<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user = getUserById($_SESSION['user_id']);
$pendingRequests = getPendingRequests($_SESSION['user_id']);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = intval($_POST['request_id']);
    $action = $_POST['action'];

    if ($action === 'accept') {
        if (updateRequestStatus($request_id, 'completed')) {
            $success = "Request accepted successfully.";
        } else {
            $error = "Failed to accept the request.";
        }
    } elseif ($action === 'reject') {
        if (updateRequestStatus($request_id, 'rejected')) {
            $success = "Request rejected successfully.";
        } else {
            $error = "Failed to reject the request.";
        }
    }
}

$page_title = "Manage Requests";
include __DIR__ . '/includes/header.php';
?>

<div class="dashboard">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    
    <div class="dashboard-content">
        <h2>Manage Requests</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <?php if (!empty($pendingRequests)): ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Sender</th>
                                <th>Amount</th>
                                <th>Description</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingRequests as $request): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($request['sender_full_name']); ?> (@<?php echo htmlspecialchars($request['sender_username']); ?>)</td>
                                    <td><?php echo formatCurrency($request['amount'], 'USD'); ?></td>
                                    <td><?php echo htmlspecialchars($request['description']); ?></td>
                                    <td><?php echo htmlspecialchars($request['created_at']); ?></td>
                                    <td>
                                        <form action="requests.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                                            <button type="submit" name="action" value="accept" class="btn btn-success btn-sm">Accept</button>
                                            <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No pending requests.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>