<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $beneficiary_username = sanitizeInput($_POST['beneficiary_username']);
    $nickname = sanitizeInput($_POST['nickname']);
    
    $database = new Database();
    $db = $database->getConnection();
    
    // Check if the beneficiary exists
    $query = "SELECT user_id FROM users WHERE username = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$beneficiary_username]);
    
    if ($stmt->rowCount() == 1) {
        $beneficiary = $stmt->fetch(PDO::FETCH_ASSOC);
        $beneficiary_user_id = $beneficiary['user_id'];
        
        // Check if the beneficiary is already added
        $checkQuery = "SELECT * FROM beneficiaries WHERE user_id = ? AND beneficiary_user_id = ?";
        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->execute([$_SESSION['user_id'], $beneficiary_user_id]);
        
        if ($checkStmt->rowCount() == 0) {
            // Add the beneficiary
            $insertQuery = "INSERT INTO beneficiaries (user_id, beneficiary_user_id, nickname) VALUES (?, ?, ?)";
            $insertStmt = $db->prepare($insertQuery);
            $insertStmt->execute([$_SESSION['user_id'], $beneficiary_user_id, $nickname]);
            
            $success = "Beneficiary added successfully.";
        } else {
            $error = "This user is already your beneficiary.";
        }
    } else {
        $error = "User not found.";
    }
}

$page_title = "Add Beneficiary";
include __DIR__ . '/includes/header.php';
?>
<br><br><br><br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Add Beneficiary</h2>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form action="add-beneficiary.php" method="POST">
                <div class="form-group mb-3">
                    <label for="beneficiary_username">Beneficiary Username</label>
                    <input type="text" class="form-control" id="beneficiary_username" name="beneficiary_username" required>
                </div>
                <div class="form-group mb-3">
                    <label for="nickname">Nickname (Optional)</label>
                    <input type="text" class="form-control" id="nickname" name="nickname">
                </div>
                <button type="submit" class="btn btn-primary">Add Beneficiary</button>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>