<?php
/**
 * Admin Settings Page
 * Accredited Inspection Agency
 */

require_once __DIR__ . '/auth_check.php';

$csrf_token = generate_csrf_token();
$inquiry_counts = get_inquiry_counts($pdo);

// Get current admin user
$admin_id = $_SESSION['admin_id'];
$stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id = ?");
$stmt->execute([$admin_id]);
$admin_user = $stmt->fetch();

$success_message = '';
$error_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error_message = 'Invalid request. Please try again.';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'update_profile') {
            $name = sanitize_input($_POST['name'] ?? '');
            $username = sanitize_input($_POST['username'] ?? '');
            $email = sanitize_input($_POST['email'] ?? '');

            if (empty($username)) {
                $error_message = 'Username is required.';
            } else {
                // Check if username is taken by another user
                $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username = ? AND id != ?");
                $stmt->execute([$username, $admin_id]);
                if ($stmt->fetch()) {
                    $error_message = 'Username is already taken.';
                } else {
                    $stmt = $pdo->prepare("UPDATE admin_users SET name = ?, username = ?, email = ? WHERE id = ?");
                    if ($stmt->execute([$name, $username, $email, $admin_id])) {
                        $_SESSION['admin_username'] = $username;
                        $success_message = 'Profile updated successfully!';
                        // Refresh admin user data
                        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id = ?");
                        $stmt->execute([$admin_id]);
                        $admin_user = $stmt->fetch();
                    } else {
                        $error_message = 'Failed to update profile.';
                    }
                }
            }
        } elseif ($action === 'change_password') {
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                $error_message = 'All password fields are required.';
            } elseif ($new_password !== $confirm_password) {
                $error_message = 'New password and confirmation do not match.';
            } elseif (strlen($new_password) < 6) {
                $error_message = 'New password must be at least 6 characters.';
            } elseif (!password_verify($current_password, $admin_user['password'])) {
                $error_message = 'Current password is incorrect.';
            } else {
                $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
                if ($stmt->execute([$new_hash, $admin_id])) {
                    $success_message = 'Password changed successfully!';
                } else {
                    $error_message = 'Failed to change password.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include __DIR__ . '/admin_head.php'; ?>
    <title>Settings | Admin Panel</title>
</head>

<body class="bg-light">
    <div class="d-flex">
        <?php include __DIR__ . '/admin_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-grow-1">
            <!-- Top Bar -->
            <div class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Settings</h5>
                <div>
                    <span class="text-muted me-2">Welcome,</span>
                    <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong>
                </div>
            </div>

            <!-- Content -->
            <div class="p-4">
                <?php if ($success_message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i><?php echo $success_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i><?php echo $error_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row g-4">
                    <!-- Profile Settings -->
                    <div class="col-lg-6">
                        <div class="card h-100">
                            <div class="card-header bg-white">
                                <h6 class="mb-0"><i class="bi bi-person me-2"></i>Profile Settings</h6>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <input type="hidden" name="action" value="update_profile">

                                    <div class="mb-3">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" class="form-control" name="name"
                                            value="<?php echo htmlspecialchars($admin_user['name'] ?? ''); ?>"
                                            placeholder="Enter your full name">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Username <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="username" required
                                            value="<?php echo htmlspecialchars($admin_user['username']); ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email"
                                            value="<?php echo htmlspecialchars($admin_user['email'] ?? ''); ?>"
                                            placeholder="Enter your email">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label text-muted">Account Created</label>
                                        <p class="form-control-plaintext">
                                            <?php echo date('F d, Y', strtotime($admin_user['created_at'])); ?>
                                        </p>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-2"></i>Update Profile
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Password Settings -->
                    <div class="col-lg-6">
                        <div class="card h-100">
                            <div class="card-header bg-white">
                                <h6 class="mb-0"><i class="bi bi-lock me-2"></i>Change Password</h6>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <input type="hidden" name="action" value="change_password">

                                    <div class="mb-3">
                                        <label class="form-label">Current Password <span
                                                class="text-danger">*</span></label>
                                        <input type="password" class="form-control" name="current_password" required
                                            placeholder="Enter current password">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">New Password <span
                                                class="text-danger">*</span></label>
                                        <input type="password" class="form-control" name="new_password" required
                                            minlength="6" placeholder="Enter new password">
                                        <small class="text-muted">Minimum 6 characters</small>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Confirm New Password <span
                                                class="text-danger">*</span></label>
                                        <input type="password" class="form-control" name="confirm_password" required
                                            placeholder="Confirm new password">
                                    </div>

                                    <button type="submit" class="btn btn-warning">
                                        <i class="bi bi-key me-2"></i>Change Password
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Info -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Account Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <p class="text-muted small mb-1">User ID</p>
                                        <p class="mb-0"><strong>#<?php echo $admin_user['id']; ?></strong></p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted small mb-1">Account Type</p>
                                        <p class="mb-0"><span class="badge bg-primary">Administrator</span></p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-muted small mb-1">Last Updated</p>
                                        <p class="mb-0">
                                            <?php echo isset($admin_user['updated_at']) ? date('F d, Y h:i A', strtotime($admin_user['updated_at'])) : 'N/A'; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>