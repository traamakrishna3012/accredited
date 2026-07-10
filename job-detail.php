<?php
/**
 * Job Detail Page
 * Accredited Inspection Agency
 */

require_once __DIR__ . '/includes/db.php';

// Get job ID from URL
$job_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (!$job_id) {
    header('Location: ' . BASE_URL . '/careers.php');
    exit;
}

// Get job details
$job = get_job_post($pdo, $job_id);

if (!$job || !$job['is_active']) {
    header('Location: ' . BASE_URL . '/careers.php');
    exit;
}

$page_title = $job['title'] . ' - Careers';
$page_description = 'Apply for ' . $job['title'] . ' position at Accredited Inspection Agency. ' . substr($job['description'], 0, 100);

// Get all active jobs for sidebar
$all_jobs = get_job_posts($pdo, true);

include __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1><?php echo htmlspecialchars($job['title']); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/careers.php">Careers</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($job['title']); ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- Job Detail Section -->
<section class="section">
    <div class="container">
        <div class="row g-5">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <!-- Job Header -->
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <h2 class="mb-2"><?php echo htmlspecialchars($job['title']); ?></h2>
                                <div class="d-flex flex-wrap gap-3 text-muted">
                                    <?php if ($job['department']): ?>
                                        <span><i
                                                class="bi bi-building me-1"></i><?php echo htmlspecialchars($job['department']); ?></span>
                                    <?php endif; ?>
                                    <?php if ($job['location']): ?>
                                        <span><i
                                                class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($job['location']); ?></span>
                                    <?php endif; ?>
                                    <span><i class="bi bi-clock me-1"></i><?php echo ucfirst($job['type']); ?></span>
                                </div>
                            </div>
                            <span class="badge bg-success fs-6">Open Position</span>
                        </div>

                        <!-- Quick Info -->
                        <div class="row g-3 mb-4">
                            <?php if ($job['experience']): ?>
                                <div class="col-sm-6 col-md-4">
                                    <div class="bg-light rounded-3 p-3 text-center">
                                        <i class="bi bi-briefcase text-primary mb-2" style="font-size: 1.5rem;"></i>
                                        <div class="small text-muted">Experience</div>
                                        <strong><?php echo htmlspecialchars($job['experience']); ?></strong>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if ($job['salary_range']): ?>
                                <div class="col-sm-6 col-md-4">
                                    <div class="bg-light rounded-3 p-3 text-center">
                                        <i class="bi bi-cash-stack text-primary mb-2" style="font-size: 1.5rem;"></i>
                                        <div class="small text-muted">Salary</div>
                                        <strong><?php echo htmlspecialchars($job['salary_range']); ?></strong>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="col-sm-6 col-md-4">
                                <div class="bg-light rounded-3 p-3 text-center">
                                    <i class="bi bi-calendar3 text-primary mb-2" style="font-size: 1.5rem;"></i>
                                    <div class="small text-muted">Posted</div>
                                    <strong><?php echo date('d M Y', strtotime($job['created_at'])); ?></strong>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Job Description -->
                        <?php if ($job['description']): ?>
                            <div class="mb-4">
                                <h5><i class="bi bi-file-text me-2 text-primary"></i>Job Description</h5>
                                <p class="text-muted"><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- Responsibilities -->
                        <?php if ($job['responsibilities']): ?>
                            <div class="mb-4">
                                <h5><i class="bi bi-list-check me-2 text-primary"></i>Responsibilities</h5>
                                <ul class="text-muted">
                                    <?php
                                    $responsibilities = array_filter(explode("\n", $job['responsibilities']));
                                    foreach ($responsibilities as $item):
                                        if (trim($item)):
                                            ?>
                                            <li class="mb-2"><?php echo htmlspecialchars(trim($item)); ?></li>
                                        <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <!-- Requirements -->
                        <?php if ($job['requirements']): ?>
                            <div class="mb-4">
                                <h5><i class="bi bi-patch-check me-2 text-primary"></i>Requirements</h5>
                                <ul class="text-muted">
                                    <?php
                                    $requirements = array_filter(explode("\n", $job['requirements']));
                                    foreach ($requirements as $item):
                                        if (trim($item)):
                                            ?>
                                            <li class="mb-2"><?php echo htmlspecialchars(trim($item)); ?></li>
                                        <?php
                                        endif;
                                    endforeach;
                                    ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <hr>

                        <!-- Apply CTA -->
                        <div class="bg-light rounded-3 p-4 text-center">
                            <h5>Interested in this position?</h5>
                            <p class="text-muted mb-3">Send us your resume and we'll get back to you.</p>
                            <a href="<?php echo BASE_URL; ?>/contact.php?job=<?php echo $job['id']; ?>"
                                class="btn btn-primary btn-lg">
                                <i class="bi bi-send me-2"></i>Apply Now
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Apply Box -->
                <div class="card bg-primary text-white mb-4">
                    <div class="card-body">
                        <h5><i class="bi bi-send me-2"></i>Apply for this Job</h5>
                        <p class="opacity-75 small mb-3">Ready to join our team? Send us your application.</p>
                        <a href="<?php echo BASE_URL; ?>/contact.php?job=<?php echo $job['id']; ?>"
                            class="btn btn-light w-100">
                            Apply Now
                        </a>
                    </div>
                </div>

                <!-- Contact Box -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5><i class="bi bi-telephone me-2 text-primary"></i>Have Questions?</h5>
                        <p class="text-muted small mb-3">Contact our HR team for any queries.</p>
                        <div class="mb-2">
                            <i class="bi bi-envelope me-2 text-primary"></i>
                            <a href="mailto:<?php echo SITE_EMAIL; ?>"><?php echo SITE_EMAIL; ?></a>
                        </div>
                        <div>
                            <i class="bi bi-phone me-2 text-primary"></i>
                            <a
                                href="tel:<?php echo preg_replace('/[^0-9+]/', '', SITE_PHONE); ?>"><?php echo SITE_PHONE; ?></a>
                        </div>
                    </div>
                </div>

                <!-- Other Openings -->
                <?php if (count($all_jobs) > 1): ?>
                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="bi bi-briefcase me-2"></i>Other Openings</h6>
                        </div>
                        <div class="list-group list-group-flush">
                            <?php foreach ($all_jobs as $j): ?>
                                <?php if ($j['id'] != $job['id']): ?>
                                    <a href="<?php echo BASE_URL; ?>/job-detail.php?id=<?php echo $j['id']; ?>"
                                        class="list-group-item list-group-item-action">
                                        <strong><?php echo htmlspecialchars($j['title']); ?></strong>
                                        <br><small class="text-muted"><?php echo htmlspecialchars($j['location']); ?></small>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>