<?php
/**
 * Admin Inquiries Management
 * Accredited Inspection Agency
 */

require_once __DIR__ . '/auth_check.php';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status' && isset($_POST['id']) && isset($_POST['status'])) {
        $id = (int) $_POST['id'];
        $status = sanitize_input($_POST['status']);

        if (in_array($status, ['new', 'read', 'responded'])) {
            update_inquiry_status($pdo, $id, $status);
            set_flash_message('success', 'Inquiry status updated successfully.');
        }
    } elseif ($_POST['action'] === 'delete' && isset($_POST['id'])) {
        $id = (int) $_POST['id'];
        $stmt = $pdo->prepare("DELETE FROM inquiries WHERE id = ?");
        $stmt->execute([$id]);
        set_flash_message('success', 'Inquiry deleted successfully.');
    } elseif ($_POST['action'] === 'send_reply' && isset($_POST['id'])) {
        $id = (int) $_POST['id'];
        $reply_to_email = sanitize_input($_POST['reply_to']);
        $reply_subject = sanitize_input($_POST['reply_subject']);
        $reply_message = sanitize_input($_POST['reply_message']);

        if (empty($reply_to_email) || empty($reply_subject) || empty($reply_message)) {
            set_flash_message('error', 'All fields are required for sending reply.');
        } else {
            // Include send_email helper
            require_once __DIR__ . '/../api/send_email.php';

            // Format email body
            $email_body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background: #1a365d; color: #fff; padding: 20px; text-align: center;'>
                        <h2 style='margin: 0;'>" . SITE_NAME . "</h2>
                    </div>
                    <div style='padding: 30px; background: #f7f7f7;'>
                        <p>Dear Customer,</p>
                        <div style='background: #fff; padding: 20px; border-radius: 5px; margin: 20px 0;'>
                            " . nl2br(htmlspecialchars($reply_message)) . "
                        </div>
                        <p style='margin-top: 20px;'>Best regards,<br><strong>" . SITE_NAME . "</strong></p>
                    </div>
                    <div style='background: #1a365d; color: #fff; padding: 15px; text-align: center; font-size: 12px;'>
                        <p style='margin: 0;'>" . SITE_ADDRESS . "</p>
                        <p style='margin: 5px 0 0;'>Phone: " . SITE_PHONE . " | Email: " . SITE_EMAIL . "</p>
                    </div>
                </div>
            ";

            // Get customer name from inquiry
            $stmt = $pdo->prepare("SELECT name FROM inquiries WHERE id = ?");
            $stmt->execute([$id]);
            $customer = $stmt->fetch();
            $customer_name = $customer ? $customer['name'] : 'Customer';

            $result = send_email($reply_to_email, $customer_name, $reply_subject, $email_body, true);

            if ($result) {
                // Update status to responded
                update_inquiry_status($pdo, $id, 'responded');
                set_flash_message('success', 'Reply sent successfully to ' . $reply_to_email);
            } else {
                set_flash_message('error', 'Failed to send email. Please check SMTP settings.');
            }
        }
        header('Location: inquiries.php?view=' . $id);
        exit;
    }

    header('Location: inquiries.php');
    exit;
}

// Pagination logic
$limit = 15;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;
$status_filter = isset($_GET['status']) ? sanitize_input($_GET['status']) : '';

// Get paginated inquiries
$inquiries = get_inquiries($pdo, $limit, $offset, $status_filter);
$total_inquiries = get_total_inquiries($pdo, $status_filter);
$total_pages = ceil($total_inquiries / $limit);

$inquiry_counts = get_inquiry_counts($pdo);

// View single inquiry
$view_inquiry = null;
if (isset($_GET['view'])) {
    $view_id = (int) $_GET['view'];
    $stmt = $pdo->prepare("SELECT i.*, s.title as service_name FROM inquiries i LEFT JOIN services s ON i.service_id = s.id WHERE i.id = ?");
    $stmt->execute([$view_id]);
    $view_inquiry = $stmt->fetch();

    if ($view_inquiry && $view_inquiry['status'] === 'new') {
        update_inquiry_status($pdo, $view_id, 'read');
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include __DIR__ . '/admin_head.php'; ?>
    <title>Inquiries | Admin Panel</title>
</head>

<body class="bg-light">
    <div class="d-flex">
        <?php include __DIR__ . '/admin_sidebar.php'; ?>


        <!-- Main Content -->
        <div class="flex-grow-1" style="max-width: calc(100vw - 250px);">
            <!-- Top Bar -->
            <div class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Inquiries</h5>
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
                <?php if ($view_inquiry): ?>
                    <!-- View Single Inquiry -->
                    <div class="card">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Inquiry Details</h6>
                            <a href="inquiries.php" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Back to List
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small">Name</label>
                                    <p class="mb-0 fw-medium"><?php echo htmlspecialchars($view_inquiry['name']); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small">Email</label>
                                    <p class="mb-0">
                                        <a href="mailto:<?php echo htmlspecialchars($view_inquiry['email']); ?>">
                                            <?php echo htmlspecialchars($view_inquiry['email']); ?>
                                        </a>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small">Phone</label>
                                    <p class="mb-0">
                                        <?php if ($view_inquiry['phone']): ?>
                                            <a href="tel:<?php echo htmlspecialchars($view_inquiry['phone']); ?>">
                                                <?php echo htmlspecialchars($view_inquiry['phone']); ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Not provided</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small">Service</label>
                                    <p class="mb-0">
                                        <?php echo $view_inquiry['service_name'] ? htmlspecialchars($view_inquiry['service_name']) : '<span class="text-muted">General Inquiry</span>'; ?>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small">Date</label>
                                    <p class="mb-0">
                                        <?php echo date('F d, Y \a\t h:i A', strtotime($view_inquiry['created_at'])); ?>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small">Status</label>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="id" value="<?php echo $view_inquiry['id']; ?>">
                                        <select name="status" class="form-select form-select-sm d-inline-block w-auto"
                                            onchange="this.form.submit()">
                                            <option value="new" <?php echo $view_inquiry['status'] == 'new' ? 'selected' : ''; ?>>New</option>
                                            <option value="read" <?php echo $view_inquiry['status'] == 'read' ? 'selected' : ''; ?>>Read</option>
                                            <option value="responded" <?php echo $view_inquiry['status'] == 'responded' ? 'selected' : ''; ?>>Responded</option>
                                        </select>
                                    </form>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-muted small">Message</label>
                                    <div class="bg-light rounded p-3">
                                        <?php echo nl2br(htmlspecialchars($view_inquiry['message'])); ?>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Reply Form -->
                            <div class="card bg-light border-0 mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="bi bi-reply me-2"></i>Send Reply</h6>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="send_reply">
                                        <input type="hidden" name="id" value="<?php echo $view_inquiry['id']; ?>">
                                        <input type="hidden" name="reply_to"
                                            value="<?php echo htmlspecialchars($view_inquiry['email']); ?>">

                                        <div class="mb-3">
                                            <label class="form-label">To</label>
                                            <input type="text" class="form-control"
                                                value="<?php echo htmlspecialchars($view_inquiry['email']); ?>" disabled>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Subject <span class="text-danger">*</span></label>
                                            <input type="text" name="reply_subject" class="form-control" required
                                                value="Re: Your Inquiry - <?php echo SITE_NAME; ?>">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Message <span class="text-danger">*</span></label>
                                            <textarea name="reply_message" class="form-control" rows="6" required
                                                placeholder="Type your reply message here...">Dear <?php echo htmlspecialchars($view_inquiry['name']); ?>,

            Thank you for your inquiry regarding <?php echo $view_inquiry['service_name'] ? htmlspecialchars($view_inquiry['service_name']) : 'our services'; ?>.

            </textarea>
                                        </div>

                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-send me-2"></i>Send Email
                                            </button>
                                            <a href="mailto:<?php echo htmlspecialchars($view_inquiry['email']); ?>?subject=Re: Your Inquiry - <?php echo SITE_NAME; ?>"
                                                class="btn btn-outline-secondary">
                                                <i class="bi bi-envelope me-2"></i>Open in Email Client
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <form method="POST" class="d-inline"
                                    onsubmit="return confirm('Are you sure you want to delete this inquiry?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $view_inquiry['id']; ?>">
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="bi bi-trash me-2"></i>Delete Inquiry
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Inquiries List -->
                    <div class="card">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">All Inquiries</h6>
                            <form method="GET" class="d-flex align-items-center">
                                <label class="me-2 small text-muted mb-0">Filter:</label>
                                <select name="status" class="form-select form-select-sm w-auto" style="min-width: 130px;" onchange="this.form.submit()">
                                    <option value="">All Statuses</option>
                                    <option value="new" <?php echo $status_filter === 'new' ? 'selected' : ''; ?>>New</option>
                                    <option value="read" <?php echo $status_filter === 'read' ? 'selected' : ''; ?>>Read</option>
                                    <option value="responded" <?php echo $status_filter === 'responded' ? 'selected' : ''; ?>>Responded</option>
                                </select>
                            </form>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Service</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($inquiries)): ?>
                                            <tr>
                                                <td colspan="8" class="text-center text-muted py-4">No inquiries yet.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($inquiries as $inquiry): ?>
                                                <tr class="<?php echo $inquiry['status'] == 'new' ? 'table-warning' : ''; ?>">
                                                    <td><?php echo $inquiry['id']; ?></td>
                                                    <td><?php echo htmlspecialchars($inquiry['name']); ?></td>
                                                    <td><?php echo htmlspecialchars($inquiry['email']); ?></td>
                                                    <td><?php echo $inquiry['phone'] ?: '-'; ?></td>
                                                    <td><?php echo $inquiry['service_name'] ? htmlspecialchars($inquiry['service_name']) : '<span class="text-muted">General</span>'; ?>
                                                    </td>
                                                    <td><?php echo date('M d, Y', strtotime($inquiry['created_at'])); ?></td>
                                                    <td>
                                                        <?php
                                                        $status_class = [
                                                            'new' => 'bg-warning',
                                                            'read' => 'bg-info',
                                                            'responded' => 'bg-success'
                                                        ];
                                                        ?>
                                                        <span
                                                            class="badge <?php echo $status_class[$inquiry['status']] ?? 'bg-secondary'; ?>">
                                                            <?php echo ucfirst($inquiry['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="?view=<?php echo $inquiry['id']; ?>"
                                                            class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        <form method="POST" class="d-inline"
                                                            onsubmit="return confirm('Are you sure you want to delete this inquiry?');">
                                                            <input type="hidden" name="action" value="delete">
                                                            <input type="hidden" name="id" value="<?php echo $inquiry['id']; ?>">
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
                            
                            <!-- Pagination -->
                            <?php if ($total_pages > 1 && !$view_inquiry): ?>
                            <?php $query_params = $status_filter ? '&status=' . urlencode($status_filter) : ''; ?>
                            <div class="d-flex justify-content-center p-3 border-top">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination mb-0">
                                        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $query_params; ?>">Previous</a>
                                        </li>
                                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                            <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo $query_params; ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $query_params; ?>">Next</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
