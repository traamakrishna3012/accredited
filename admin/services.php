<?php
/**
 * Admin Services Management
 * Accredited Inspection Agency
 */

require_once __DIR__ . '/auth_check.php';

$csrf_token = generate_csrf_token();
$inquiry_counts = get_inquiry_counts($pdo);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('error', 'Invalid request.');
        header('Location: services.php');
        exit;
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'create' || $action === 'update') {
        $title = sanitize_input($_POST['title'] ?? '');
        $description = sanitize_input($_POST['description'] ?? '');
        $icon = sanitize_input($_POST['icon'] ?? 'bi-gear');
        $is_active = isset($_POST['is_active']) ? 1 : 0;

        if (empty($title)) {
            set_flash_message('error', 'Service title is required.');
        } else {
            if ($action === 'create') {
                $stmt = $pdo->prepare("INSERT INTO services (title, description, icon, is_active) VALUES (?, ?, ?, ?)");
                $stmt->execute([$title, $description, $icon, $is_active]);
                set_flash_message('success', 'Service created successfully.');
            } else {
                $id = (int) $_POST['id'];
                $stmt = $pdo->prepare("UPDATE services SET title = ?, description = ?, icon = ?, is_active = ? WHERE id = ?");
                $stmt->execute([$title, $description, $icon, $is_active, $id]);
                set_flash_message('success', 'Service updated successfully.');
            }
        }
    } elseif ($action === 'delete') {
        $id = (int) $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$id]);
        set_flash_message('success', 'Service deleted successfully.');
    }

    header('Location: services.php');
    exit;
}

// Get all services
$services = get_services($pdo, false);

// Edit mode
$edit_service = null;
if (isset($_GET['edit'])) {
    $edit_id = (int) $_GET['edit'];
    $edit_service = get_service($pdo, $edit_id);
}

// Available icons
$icons = [
    'bi-eyedropper' => 'Eyedropper (Sampling)',
    'bi-search' => 'Search (Inspection)',
    'bi-clipboard-check' => 'Clipboard Check (Testing)',
    'bi-gear' => 'Gear',
    'bi-shield-check' => 'Shield Check',
    'bi-graph-up' => 'Graph Up',
    'bi-award' => 'Award',
    'bi-building' => 'Building',
    'bi-lightning' => 'Lightning',
    'bi-tools' => 'Tools',
    'bi-truck' => 'Truck',
    'bi-box-seam' => 'Box',
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Services | Admin Panel</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?php echo BASE_URL; ?>/assets/images/Agency_Logo.svg">
    <link rel="alternate icon" type="image/x-icon" href="<?php echo BASE_URL; ?>/assets/images/favicon.ico">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>/assets/images/favicon.ico">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>/assets/css/style.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="admin-sidebar" style="width: 250px;">
            <div class="text-white mb-4">
                <h5 class="mb-0 text-white">Admin Panel</h5>
                <small class="opacity-75"><?php echo SITE_NAME; ?></small>
            </div>

            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo $admin_page == 'dashboard' ? 'active' : ''; ?>" href="dashboard.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $admin_page == 'inquiries' ? 'active' : ''; ?>" href="inquiries.php">
                        <i class="bi bi-envelope"></i> Inquiries
                        <?php if ($inquiry_counts['new_count'] > 0): ?>
                            <span class="badge bg-danger ms-auto"><?php echo $inquiry_counts['new_count']; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $admin_page == 'services' ? 'active' : ''; ?>" href="services.php">
                        <i class="bi bi-grid"></i> Services
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $admin_page == 'jobs' ? 'active' : ''; ?>" href="jobs.php">
                        <i class="bi bi-briefcase"></i> Jobs
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $admin_page == 'settings' ? 'active' : ''; ?>" href="settings.php">
                        <i class="bi bi-gear"></i> Settings
                    </a>
                </li>
            </ul>

            <hr class="border-secondary my-4">

            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo BASE_URL; ?>/" target="_blank">
                        <i class="bi bi-globe"></i> View Website
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">
                        <i class="bi bi-box-arrow-left"></i> Logout
                    </a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1">
            <!-- Top Bar -->
            <div class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Services</h5>
                <div>
                    <span class="text-muted me-2">Welcome,</span>
                    <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php $flash = get_flash_message();
            if ($flash): ?>
                <div class="px-4 pt-4">
                    <div class="alert alert-<?php echo $flash['type'] == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show"
                        role="alert">
                        <?php echo $flash['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Content -->
            <div class="p-4">
                <div class="row g-4">
                    <!-- Service Form -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h6 class="mb-0"><?php echo $edit_service ? 'Edit Service' : 'Add New Service'; ?></h6>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <input type="hidden" name="action"
                                        value="<?php echo $edit_service ? 'update' : 'create'; ?>">
                                    <?php if ($edit_service): ?>
                                        <input type="hidden" name="id" value="<?php echo $edit_service['id']; ?>">
                                    <?php endif; ?>

                                    <div class="mb-3">
                                        <label for="title" class="form-label">Title <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title" name="title" required
                                            value="<?php echo $edit_service ? htmlspecialchars($edit_service['title']) : ''; ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description"
                                            rows="4"><?php echo $edit_service ? htmlspecialchars($edit_service['description']) : ''; ?></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="icon" class="form-label">Icon</label>
                                        <select class="form-select" id="icon" name="icon">
                                            <?php foreach ($icons as $value => $label): ?>
                                                <option value="<?php echo $value; ?>" <?php echo ($edit_service && $edit_service['icon'] == $value) ? 'selected' : ''; ?>>
                                                    <?php echo $label; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="is_active"
                                                name="is_active" <?php echo (!$edit_service || $edit_service['is_active']) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="is_active">Active</label>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <?php echo $edit_service ? 'Update' : 'Add'; ?> Service
                                        </button>
                                        <?php if ($edit_service): ?>
                                            <a href="services.php" class="btn btn-outline-secondary">Cancel</a>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Services List -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">All Services</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Icon</th>
                                                <th>Title</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($services)): ?>
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-4">No services yet.
                                                    </td>
                                                </tr>
                                            <?php else: ?>
                                                <?php foreach ($services as $service): ?>
                                                    <tr>
                                                        <td>
                                                            <span class="badge bg-primary p-2">
                                                                <i
                                                                    class="bi <?php echo htmlspecialchars($service['icon']); ?>"></i>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <strong><?php echo htmlspecialchars($service['title']); ?></strong>
                                                            <br>
                                                            <small
                                                                class="text-muted"><?php echo htmlspecialchars(substr($service['description'], 0, 60)); ?>...</small>
                                                        </td>
                                                        <td>
                                                            <?php if ($service['is_active']): ?>
                                                                <span class="badge bg-success">Active</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-secondary">Inactive</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <a href="?edit=<?php echo $service['id']; ?>"
                                                                class="btn btn-sm btn-outline-primary">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                            <form method="POST" class="d-inline"
                                                                onsubmit="return confirm('Are you sure you want to delete this service?');">
                                                                <input type="hidden" name="csrf_token"
                                                                    value="<?php echo $csrf_token; ?>">
                                                                <input type="hidden" name="action" value="delete">
                                                                <input type="hidden" name="id"
                                                                    value="<?php echo $service['id']; ?>">
                                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
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
