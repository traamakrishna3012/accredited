<?php
/**
 * Admin Job Posts Management
 * Accredited Inspection Agency
 */

require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../includes/db.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$job_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        set_flash_message('Invalid request. Please try again.', 'error');
        header('Location: jobs.php');
        exit;
    }

    $post_action = $_POST['action'] ?? '';

    if ($post_action === 'save') {
        $data = [
            'title' => sanitize_input($_POST['title']),
            'department' => sanitize_input($_POST['department']),
            'location' => sanitize_input($_POST['location']),
            'type' => sanitize_input($_POST['type']),
            'experience' => sanitize_input($_POST['experience']),
            'salary_range' => sanitize_input($_POST['salary_range']),
            'description' => sanitize_input($_POST['description']),
            'requirements' => sanitize_input($_POST['requirements']),
            'responsibilities' => sanitize_input($_POST['responsibilities'])
        ];

        if (empty($data['title'])) {
            set_flash_message('Job title is required.', 'error');
        } else {
            if (save_job_post($pdo, $data)) {
                set_flash_message('Job post created successfully!', 'success');
                header('Location: jobs.php');
                exit;
            } else {
                set_flash_message('Failed to create job post.', 'error');
            }
        }
    } elseif ($post_action === 'update' && $job_id) {
        $data = [
            'title' => sanitize_input($_POST['title']),
            'department' => sanitize_input($_POST['department']),
            'location' => sanitize_input($_POST['location']),
            'type' => sanitize_input($_POST['type']),
            'experience' => sanitize_input($_POST['experience']),
            'salary_range' => sanitize_input($_POST['salary_range']),
            'description' => sanitize_input($_POST['description']),
            'requirements' => sanitize_input($_POST['requirements']),
            'responsibilities' => sanitize_input($_POST['responsibilities']),
            'is_active' => isset($_POST['is_active']) ? 1 : 0
        ];

        if (update_job_post($pdo, $job_id, $data)) {
            set_flash_message('Job post updated successfully!', 'success');
            header('Location: jobs.php');
            exit;
        } else {
            set_flash_message('Failed to update job post.', 'error');
        }
    } elseif ($post_action === 'delete' && $job_id) {
        if (delete_job_post($pdo, $job_id)) {
            set_flash_message('Job post deleted successfully!', 'success');
        } else {
            set_flash_message('Failed to delete job post.', 'error');
        }
        header('Location: jobs.php');
        exit;
    }
}

// Get data based on action
$job = null;
$jobs = [];

if ($action === 'edit' && $job_id) {
    $job = get_job_post($pdo, $job_id);
    if (!$job) {
        header('Location: jobs.php');
        exit;
    }
} elseif ($action === 'list') {
    $jobs = get_job_posts($pdo, false);
}

$page_title = 'Manage Jobs';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include __DIR__ . '/admin_head.php'; ?>
    <title><?php echo $page_title; ?> | Admin - <?php echo SITE_NAME; ?></title>
</head>

<body class="bg-light">
    <div class="d-flex">
        <?php include __DIR__ . '/admin_sidebar.php'; ?>

            <!-- Main Content -->
            <div class="flex-grow-1 p-4" style="max-width: calc(100vw - 250px);">
                <?php $flash = get_flash_message();
                if ($flash): ?>
                    <div
                        class="alert alert-<?php echo $flash['type'] == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show">
                        <?php echo $flash['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($action === 'list'): ?>
                    <!-- Jobs List -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2>Job Postings</h2>
                            <p class="text-muted mb-0">Manage career opportunities</p>
                        </div>
                        <a href="jobs.php?action=add" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i> Add New Job
                        </a>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <?php if (empty($jobs)): ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-briefcase text-muted" style="font-size: 3rem;"></i>
                                    <p class="mt-3 text-muted">No job posts yet.</p>
                                    <a href="jobs.php?action=add" class="btn btn-primary">Create First Job Post</a>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Job Title</th>
                                                <th>Department</th>
                                                <th>Type</th>
                                                <th>Status</th>
                                                <th>Posted</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($jobs as $j): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($j['title']); ?></strong>
                                                        <br><small
                                                            class="text-muted"><?php echo htmlspecialchars($j['location']); ?></small>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($j['department']); ?></td>
                                                    <td>
                                                        <span class="badge bg-info"><?php echo ucfirst($j['type']); ?></span>
                                                    </td>
                                                    <td>
                                                        <?php if ($j['is_active']): ?>
                                                            <span class="badge bg-success">Active</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Inactive</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo date('d M Y', strtotime($j['created_at'])); ?></td>
                                                    <td>
                                                        <a href="jobs.php?action=edit&id=<?php echo $j['id']; ?>"
                                                            class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form method="POST" action="jobs.php?id=<?php echo $j['id']; ?>"
                                                            class="d-inline"
                                                            onsubmit="return confirm('Are you sure you want to delete this job?');">
                                                            <input type="hidden" name="csrf_token"
                                                                value="<?php echo generate_csrf_token(); ?>">
                                                            <input type="hidden" name="action" value="delete">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php elseif ($action === 'add' || $action === 'edit'): ?>
                    <!-- Add/Edit Job Form -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2><?php echo $action === 'add' ? 'Create New Job Post' : 'Edit Job Post'; ?></h2>
                            <p class="text-muted mb-0">Fill in the job details below</p>
                        </div>
                        <a href="jobs.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Back to List
                        </a>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="jobs.php<?php echo $action === 'edit' ? '?id=' . $job_id : ''; ?>">
                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                <input type="hidden" name="action"
                                    value="<?php echo $action === 'add' ? 'save' : 'update'; ?>">

                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Job Title *</label>
                                        <input type="text" class="form-control" name="title" required
                                            value="<?php echo $job ? htmlspecialchars($job['title']) : ''; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Department</label>
                                        <input type="text" class="form-control" name="department"
                                            placeholder="e.g., Inspection, Testing, IT"
                                            value="<?php echo $job ? htmlspecialchars($job['department']) : ''; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Location</label>
                                        <input type="text" class="form-control" name="location"
                                            placeholder="e.g., Raigarh, Chhattisgarh"
                                            value="<?php echo $job ? htmlspecialchars($job['location']) : ''; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Job Type</label>
                                        <select class="form-select" name="type">
                                            <option value="full-time" <?php echo ($job && $job['type'] == 'full-time') ? 'selected' : ''; ?>>Full-Time</option>
                                            <option value="part-time" <?php echo ($job && $job['type'] == 'part-time') ? 'selected' : ''; ?>>Part-Time</option>
                                            <option value="contract" <?php echo ($job && $job['type'] == 'contract') ? 'selected' : ''; ?>>Contract</option>
                                            <option value="internship" <?php echo ($job && $job['type'] == 'internship') ? 'selected' : ''; ?>>Internship</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Experience Required</label>
                                        <input type="text" class="form-control" name="experience"
                                            placeholder="e.g., 2-5 years"
                                            value="<?php echo $job ? htmlspecialchars($job['experience']) : ''; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Salary Range</label>
                                        <input type="text" class="form-control" name="salary_range"
                                            placeholder="e.g., ₹4-6 LPA"
                                            value="<?php echo $job ? htmlspecialchars($job['salary_range']) : ''; ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Job Description</label>
                                        <textarea class="form-control" name="description" rows="4"
                                            placeholder="Describe the role and what the candidate will be doing..."><?php echo $job ? htmlspecialchars($job['description']) : ''; ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Requirements</label>
                                        <textarea class="form-control" name="requirements" rows="5"
                                            placeholder="Enter each requirement on a new line..."><?php echo $job ? htmlspecialchars($job['requirements']) : ''; ?></textarea>
                                        <small class="text-muted">One requirement per line</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Responsibilities</label>
                                        <textarea class="form-control" name="responsibilities" rows="5"
                                            placeholder="Enter each responsibility on a new line..."><?php echo $job ? htmlspecialchars($job['responsibilities']) : ''; ?></textarea>
                                        <small class="text-muted">One responsibility per line</small>
                                    </div>
                                    <?php if ($action === 'edit'): ?>
                                        <div class="col-12">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="is_active" id="is_active"
                                                    <?php echo $job['is_active'] ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="is_active">Active (visible on
                                                    website)</label>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <div class="col-12">
                                        <hr>
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="bi bi-check-lg me-1"></i>
                                            <?php echo $action === 'add' ? 'Create Job Post' : 'Update Job Post'; ?>
                                        </button>
                                        <a href="jobs.php" class="btn btn-outline-secondary btn-lg ms-2">Cancel</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
