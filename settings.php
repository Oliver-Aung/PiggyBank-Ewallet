<?php
session_start();
require_once __DIR__ . '/includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user = getUserById($_SESSION['user_id']);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = sanitizeInput($_POST['full_name']);
    $email = sanitizeInput($_POST['email']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
    $profile_picture = $user['profile_picture']; // Keep the current profile picture by default

    // Handle profile picture removal
    if (isset($_POST['remove_profile_picture']) && $_POST['remove_profile_picture'] == '1') {
        $profile_picture = null; // Reset to default
    }

    // Handle profile picture upload
    if (!empty($_FILES['profile_picture']['name']) && empty($_POST['remove_profile_picture'])) {
        $upload_dir = __DIR__ . '/uploads/profile_pictures/';
        
        // Ensure the directory exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = $_SESSION['user_id'] . '_' . time() . '_' . basename($_FILES['profile_picture']['name']);
        $target_file = $upload_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate file type
        if (!in_array($file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
            $error = 'Only JPG, JPEG, PNG, and GIF files are allowed.';
        } elseif ($_FILES['profile_picture']['size'] > 2 * 1024 * 1024) { // Limit file size to 2MB
            $error = 'File size must not exceed 2MB.';
        } else {
            // Move the uploaded file
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
                $profile_picture = 'uploads/profile_pictures/' . $file_name;
            } else {
                $error = 'Failed to upload the profile picture.';
            }
        }
    }

    if (empty($error)) {
        $database = new Database();
        $db = $database->getConnection();

        try {
            $query = "UPDATE users SET full_name = ?, email = ?, profile_picture = ?" . ($password ? ", password = ?" : "") . " WHERE user_id = ?";
            $stmt = $db->prepare($query);

            $params = [$full_name, $email, $profile_picture];
            if ($password) {
                $params[] = $password;
            }
            $params[] = $_SESSION['user_id'];

            $stmt->execute($params);
            $success = 'Profile updated successfully.';
            $user = getUserById($_SESSION['user_id']); // Refresh user data
        } catch (Exception $e) {
            $error = 'Failed to update profile: ' . $e->getMessage();
        }
    }
}

$page_title = "Settings";
include __DIR__ . '/includes/header.php';
?>
<br><br><br><br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Settings</h2>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form action="settings.php" method="POST" enctype="multipart/form-data">
                <div class="form-group mb-3">
                    <label for="full_name">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>
                <div class="form-group mb-3">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="form-group mb-3">
                    <label for="password">Password (Leave blank to keep current password)</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <div class="form-group mb-3">
                    <label for="profile_picture">Profile Picture</label>
                    <input type="file" class="form-control" id="profile_picture" name="profile_picture">
                    <?php if (!empty($user['profile_picture'])): ?>
                        <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="img-thumbnail mt-2" width="150">
                        <div class="form-check mt-2">
                            <input type="checkbox" class="form-check-input" id="remove_profile_picture" name="remove_profile_picture" value="1">
                            <label for="remove_profile_picture" class="form-check-label">Remove Profile Picture</label>
                        </div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>