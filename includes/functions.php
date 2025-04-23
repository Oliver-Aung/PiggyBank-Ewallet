<?php
require_once __DIR__ . '/../config/database.php';



function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function isLoggedIn() {
  // More thorough check
  return isset($_SESSION['user_id']) && 
         !empty($_SESSION['user_id']) && 
         is_numeric($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function redirectIfLoggedIn() {
  if (isLoggedIn()) {
      header("Location: dashboard.php");
      exit();
  }
}

function getUserById($user_id) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT user_id, username, full_name, email, profile_picture FROM users WHERE user_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$user_id]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getWalletByUserId($user_id) {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        return false;
    }
    
    $query = "SELECT * FROM wallets WHERE user_id = ?";
    $stmt = $db->prepare($query);
    
    if (!$stmt->execute([$user_id])) {
        return false;
    }
    
    $wallet = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Ensure wallet exists, if not create one
    if (!$wallet) {
        $insertQuery = "INSERT INTO wallets (user_id, created_at) VALUES (?, NOW())";
        $insertStmt = $db->prepare($insertQuery);
        
        if ($insertStmt->execute([$user_id])) {
            $wallet_id = $db->lastInsertId();
            return [
                'wallet_id' => $wallet_id,
                'user_id' => $user_id,
                'balance' => 0.00,
                'currency' => 'USD'
            ];
        }
        return false;
    }
    
    return $wallet;
}

function getTransactionsByWalletId($wallet_id, $limit = 10) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT * FROM transactions WHERE wallet_id = ? ORDER BY created_at DESC LIMIT ?";
    $stmt = $db->prepare($query);
    $stmt->bindValue(1, $wallet_id, PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getBeneficiaries($user_id) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT u.user_id, u.username, u.full_name, b.nickname 
              FROM beneficiaries b
              JOIN users u ON b.beneficiary_user_id = u.user_id
              WHERE b.user_id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$user_id]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function formatCurrency($amount, $currency = 'USD') {
  if (class_exists('NumberFormatter')) {
      $formatter = new NumberFormatter('en_US', NumberFormatter::CURRENCY);
      return $formatter->formatCurrency($amount, $currency);
  } else {
      // Fallback if intl extension not available
      return $currency . ' ' . number_format($amount, 2);
  }
}

function generateReference() {
    return uniqid('PIGGY', true);
}

function createMoneyRequest($sender_id, $receiver_id, $amount, $description) {
    $database = new Database();
    $db = $database->getConnection();

    $query = "INSERT INTO money_requests 
              (sender_id, receiver_id, amount, currency, description, status, created_at)
              VALUES (?, ?, ?, 'USD', ?, 'pending', NOW())";
    $stmt = $db->prepare($query);
    return $stmt->execute([$sender_id, $receiver_id, $amount, $description]);
}

function getPendingRequests($user_id) {
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT r.request_id, r.sender_id, r.amount, r.description, r.created_at, 
                     u.username AS sender_username, u.full_name AS sender_full_name
              FROM money_requests r
              JOIN users u ON r.sender_id = u.user_id
              WHERE r.receiver_id = ? AND r.status = 'pending'
              ORDER BY r.created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute([$user_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateRequestStatus($request_id, $status) {
    $database = new Database();
    $db = $database->getConnection();

    $query = "UPDATE money_requests SET status = ?, updated_at = NOW() WHERE request_id = ?";
    $stmt = $db->prepare($query);
    return $stmt->execute([$status, $request_id]);
}
?>