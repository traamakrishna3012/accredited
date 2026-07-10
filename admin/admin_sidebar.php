<?php
/**
 * Admin Sidebar Partial
 * Include this in all admin pages for a consistent sidebar.
 * Requires: $admin_page (set by auth_check.php), $pdo, BASE_URL, SITE_NAME
 */

// Get inquiry counts for badge (suppress errors if table doesn't exist)
if (!isset($inquiry_counts)) {
    try {
        $inquiry_counts = get_inquiry_counts($pdo);
    } catch (Exception $e) {
        $inquiry_counts = ['new_count' => 0, 'total' => 0, 'responded_count' => 0];
    }
}

// Helper to check active state for payroll pages
$is_payroll_page = in_array($admin_page, [
    'payroll_batches', 'payroll_batch_view',
    'payroll_upload', 'payroll_upload_process', 'payroll_upload_commit',
    'payroll_employees', 'payroll_employee_edit',
    'payroll_download_template', 'payroll_payslip_pdf',
    'payroll_batch_zip', 'payroll_batch_email_trigger', 'payroll_email_processor'
]);
?>
<!-- Sidebar -->
<div class="admin-sidebar flex-shrink-0" style="width: 250px; min-height: 100vh;">
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
                <?php if (($inquiry_counts['new_count'] ?? 0) > 0): ?>
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

    <div class="text-white mb-2 px-3">
        <small class="opacity-75 text-uppercase fw-bold" style="font-size: 0.75rem;">Payroll</small>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo in_array($admin_page, ['payroll_batches', 'payroll_batch_view']) ? 'active' : ''; ?>" href="payroll_batches.php">
                <i class="bi bi-files"></i> Batches
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo in_array($admin_page, ['payroll_upload', 'payroll_upload_process', 'payroll_upload_commit']) ? 'active' : ''; ?>" href="payroll_upload.php">
                <i class="bi bi-cloud-upload"></i> Bulk Upload
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo in_array($admin_page, ['payroll_employees', 'payroll_employee_edit']) ? 'active' : ''; ?>" href="payroll_employees.php">
                <i class="bi bi-people"></i> Employees
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
