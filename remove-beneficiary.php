<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

if (isset($_GET['id'])) {
    $beneficiary_user_id = intval($_GET['id']);
    
    $database = new Database();
    $db = $database->getConnection();
    
    try {
        $query = "DELETE FROM beneficiaries WHERE user_id = ? AND beneficiary_user_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$_SESSION['user_id'], $beneficiary_user_id]);
        
        if ($stmt->rowCount() > 0) {
            $_SESSION['success'] = "Beneficiary removed successfully.";
        } else {
            $_SESSION['error'] = "Failed to remove the beneficiary or beneficiary not found.";
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "An error occurred: " . $e->getMessage();
    }
}

redirect('dashboard.php');
?>