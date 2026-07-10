<?php
/**
 * Careers Page
 * Accredited Inspection Agency
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/cache.php';

$page_title = 'Careers';
$page_description = 'Join Accredited Inspection Agency - Explore career opportunities in testing, inspection and certification.';

// Get active job posts from database (cached for 5 min)
$job_posts = get_cached_job_posts($pdo, true);

include __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Careers</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/">Home</a></li>
                <li class="breadcrumb-item active">Careers</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Why Work With Us -->
<section class="section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="text-primary fw-semibold text-uppercase">Join Our Team</span>
                <h2 class="section-title mt-2">Build Your Career With Us</h2>
                <p class="text-muted mb-4">
                    At Accredited Inspection Agency, we believe our people are our greatest asset. We offer a
                    dynamic work environment where innovation, integrity, and excellence are valued.
                </p>
                <p class="text-muted mb-4">
                    Whether you're an experienced professional or a fresh graduate looking to start your career
                    in the testing, inspection, and certification industry, we welcome talented individuals who
                    share our commitment to quality and customer satisfaction.
                </p>
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <span>Competitive Salary</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <span>Professional Growth</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <span>Training & Development</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                            <span>Supportive Environment</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="stat-box bg-light rounded-3">
                            <div class="number"><?php echo count($job_posts); ?></div>
                            <div class="label">Open Positions</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-box bg-light rounded-3">
                            <div class="number">50+</div>
                            <div class="label">Team Members</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-box bg-light rounded-3">
                            <div class="number">3+</div>
                            <div class="label">Departments</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-box bg-light rounded-3">
                            <div class="number">100%</div>
                            <div class="label">Growth Focus</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Job Openings -->
<section class="section bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-primary fw-semibold text-uppercase">Open Positions</span>
            <h2 class="section-title mt-2">Current Job Openings</h2>
            <p class="section-subtitle">Explore opportunities to join our growing team</p>
        </div>

        <?php if (empty($job_posts)): ?>
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-briefcase text-muted" style="font-size: 4rem;"></i>
                </div>
                <h4 class="text-muted">No Open Positions Currently</h4>
                <p class="text-muted mb-4">We don't have any open positions at the moment, but we're always looking for
                    talented individuals.</p>
                <a href="<?php echo BASE_URL; ?>/contact.php" class="btn btn-primary">
                    <i class="bi bi-envelope me-2"></i>Send Your Resume
                </a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($job_posts as $job): ?>
                    <div class="col-lg-6">
                        <div class="card job-card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="mb-1"><?php echo htmlspecialchars($job['title']); ?></h5>
                                        <p class="text-muted mb-0">
                                            <?php if ($job['department']): ?>
                                                <span class="me-3"><i
                                                        class="bi bi-building me-1"></i><?php echo htmlspecialchars($job['department']); ?></span>
                                            <?php endif; ?>
                                            <?php if ($job['location']): ?>
                                                <span><i
                                                        class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($job['location']); ?></span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <span class="badge bg-primary"><?php echo ucfirst($job['type']); ?></span>
                                </div>

                                <?php if ($job['description']): ?>
                                    <p class="text-muted small mb-3">
                                        <?php echo htmlspecialchars(substr($job['description'], 0, 150)); ?>...
                                    </p>
                                <?php endif; ?>

                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <?php if ($job['experience']): ?>
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-briefcase me-1"></i><?php echo htmlspecialchars($job['experience']); ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($job['salary_range']): ?>
                                        <span class="badge bg-light text-dark border">
                                            <i
                                                class="bi bi-currency-rupee me-1"></i><?php echo htmlspecialchars($job['salary_range']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="d-flex gap-2">
                                    <a href="<?php echo BASE_URL; ?>/job-detail.php?id=<?php echo $job['id']; ?>"
                                        class="btn btn-outline-primary btn-sm">
                                        View Details <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>/contact.php?job=<?php echo $job['id']; ?>"
                                        class="btn btn-primary btn-sm">
                                        Apply Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Company Values -->
<section class="section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-primary fw-semibold text-uppercase">Our Culture</span>
            <h2 class="section-title mt-2">What We Stand For</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center border-0 shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-lightbulb text-primary mb-3" style="font-size: 2.5rem;"></i>
                        <h5>Innovation</h5>
                        <p class="text-muted small mb-0">We embrace new technologies and methodologies to stay ahead.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center border-0 shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-hand-thumbs-up text-primary mb-3" style="font-size: 2.5rem;"></i>
                        <h5>Integrity</h5>
                        <p class="text-muted small mb-0">Honesty and transparency in everything we do.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center border-0 shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-people text-primary mb-3" style="font-size: 2.5rem;"></i>
                        <h5>Teamwork</h5>
                        <p class="text-muted small mb-0">Collaboration and mutual respect drive our success.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center border-0 shadow-sm">
                    <div class="card-body">
                        <i class="bi bi-graph-up-arrow text-primary mb-3" style="font-size: 2.5rem;"></i>
                        <h5>Excellence</h5>
                        <p class="text-muted small mb-0">We strive for the highest standards in our work.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <h3>Ready to Join Our Team?</h3>
        <p class="mb-4 opacity-75">Send us your resume and let's explore opportunities together.</p>
        <a href="<?php echo BASE_URL; ?>/contact.php" class="btn btn-light btn-lg">Contact Us</a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>