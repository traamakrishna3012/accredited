<?php
/**
 * Individual Service Detail Page
 * Accredited Inspection Agency
 */

require_once __DIR__ . '/includes/db.php';

// Get service ID or slug from URL
$service_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (!$service_id) {
    header('Location: ' . BASE_URL . '/services.php');
    exit;
}

// Get service details
$service = get_service($pdo, $service_id);

if (!$service || !$service['is_active']) {
    header('Location: ' . BASE_URL . '/services.php');
    exit;
}

$page_title = $service['title'];
$page_description = substr(strip_tags($service['description']), 0, 160);

// Get all active services for sidebar
$all_services = get_services($pdo);

// Service-specific features
$service_features = [
    'Sampling' => [
        'Precise sampling as per IS, ISO, ASTM standards',
        'On-site sampling of mines, plants & ports',
        'In-house laboratory testing',
        'Joint sample preparation',
        'Risk mitigation for commodity movement',
        'Documentation and reporting'
    ],
    'Inspection' => [
        'Pre-Shipment Assessment',
        'Loading/Unloading Supervision',
        'Physical Quality Monitoring',
        'Weighment Assessment',
        'Vessel, Rake, Trucks & Stack Assessment',
        'Comprehensive inspection reports'
    ],
    'Testing' => [
        'NABL ISO/IEC 17025:2017 accredited labs',
        'Coal, Coke, Minerals, Ores testing',
        'Fertilizers & Agricultural Products',
        'Environmental Testing',
        'Metals and Alloys testing',
        'Compliance testing with standards'
    ],
    'IT Support' => [
        'Network management and troubleshooting',
        'Hardware installation and repair',
        'Software installation and updates',
        'System maintenance and optimization',
        'Technical helpdesk support',
        '24/7 remote assistance available'
    ],
    'Web Application Development' => [
        'Custom web application development',
        'Responsive and mobile-friendly design',
        'E-commerce solutions',
        'Content Management Systems (CMS)',
        'API development and integration',
        'Website maintenance and support'
    ],
    'Digital Marketing' => [
        'Search Engine Optimization (SEO)',
        'Social Media Marketing (SMM)',
        'Pay-Per-Click (PPC) advertising',
        'Content marketing and strategy',
        'Email marketing campaigns',
        'Analytics and performance tracking'
    ]
];

$features = $service_features[$service['title']] ?? [
    'Professional service delivery',
    'Experienced technical team',
    'Quality assurance guaranteed',
    'Competitive pricing',
    'Timely completion',
    'Customer satisfaction focus'
];

include __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1><?php echo htmlspecialchars($service['title']); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/services.php">Services</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($service['title']); ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- Service Detail Section -->
<section class="section">
    <div class="container">
        <div class="row g-5">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="service-detail-content">
                    <div class="icon-box mb-4" style="width: 80px; height: 80px;">
                        <i class="bi <?php echo htmlspecialchars($service['icon']); ?>" style="font-size: 2rem;"></i>
                    </div>

                    <h2 class="mb-4"><?php echo htmlspecialchars($service['title']); ?></h2>

                    <div class="service-description mb-5">
                        <p class="lead text-muted">
                            <?php echo nl2br(htmlspecialchars($service['description'])); ?>
                        </p>
                    </div>

                    <!-- Key Features -->
                    <div class="card mb-5">
                        <div class="card-body">
                            <h4 class="mb-4"><i class="bi bi-check-circle text-primary me-2"></i>Key Features</h4>
                            <div class="row g-3">
                                <?php foreach ($features as $feature): ?>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-check-circle-fill text-success me-3 mt-1"></i>
                                            <span><?php echo htmlspecialchars($feature); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Why Choose Us -->
                    <div class="card mb-5">
                        <div class="card-body">
                            <h4 class="mb-4"><i class="bi bi-star text-primary me-2"></i>Why Choose Us</h4>
                            <div class="row g-4">
                                <div class="col-sm-6 col-md-4">
                                    <div class="text-center">
                                        <i class="bi bi-award text-primary mb-2" style="font-size: 2rem;"></i>
                                        <h6>Expert Team</h6>
                                        <small class="text-muted">Experienced professionals</small>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="text-center">
                                        <i class="bi bi-clock text-primary mb-2" style="font-size: 2rem;"></i>
                                        <h6>Timely Delivery</h6>
                                        <small class="text-muted">On-time completion</small>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4">
                                    <div class="text-center">
                                        <i class="bi bi-shield-check text-primary mb-2" style="font-size: 2rem;"></i>
                                        <h6>Quality Assured</h6>
                                        <small class="text-muted">100% satisfaction</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CTA -->
                    <div class="bg-light rounded-3 p-4 text-center">
                        <h4>Interested in <?php echo htmlspecialchars($service['title']); ?>?</h4>
                        <p class="text-muted mb-3">Contact us today to discuss your requirements and get a customized
                            quote.</p>
                        <a href="<?php echo BASE_URL; ?>/contact.php?service=<?php echo $service['id']; ?>"
                            class="btn btn-primary btn-lg">
                            <i class="bi bi-envelope me-2"></i>Get a Quote
                        </a>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- All Services -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-grid me-2"></i>Our Services</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach ($all_services as $s): ?>
                            <a href="<?php echo BASE_URL; ?>/service-detail.php?id=<?php echo $s['id']; ?>"
                                class="list-group-item list-group-item-action d-flex align-items-center <?php echo $s['id'] == $service['id'] ? 'active' : ''; ?>">
                                <i class="bi <?php echo htmlspecialchars($s['icon']); ?> me-3"></i>
                                <?php echo htmlspecialchars($s['title']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Contact Box -->
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5><i class="bi bi-telephone me-2"></i>Need Help?</h5>
                        <p class="opacity-75 small mb-3">Have questions about our services? Contact us directly.</p>
                        <div class="mb-2">
                            <i class="bi bi-phone me-2"></i>
                            <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', SITE_PHONE); ?>"
                                class="text-white"><?php echo SITE_PHONE; ?></a>
                        </div>
                        <div>
                            <i class="bi bi-envelope me-2"></i>
                            <a href="mailto:<?php echo SITE_EMAIL; ?>" class="text-white"><?php echo SITE_EMAIL; ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Services -->
<section class="section bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h3>Other Services</h3>
            <p class="text-muted">Explore our other professional services</p>
        </div>
        <div class="row g-4">
            <?php
            $count = 0;
            foreach ($all_services as $s):
                if ($s['id'] != $service['id'] && $count < 3):
                    $count++;
                    ?>
                    <div class="col-md-4">
                        <div class="card service-card h-100">
                            <div class="card-body">
                                <div class="icon-box">
                                    <i class="bi <?php echo htmlspecialchars($s['icon']); ?>"></i>
                                </div>
                                <h5><?php echo htmlspecialchars($s['title']); ?></h5>
                                <p class="text-muted small mb-3">
                                    <?php echo htmlspecialchars(substr($s['description'], 0, 100)); ?>...
                                </p>
                                <a href="<?php echo BASE_URL; ?>/service-detail.php?id=<?php echo $s['id']; ?>"
                                    class="btn btn-outline-primary btn-sm">
                                    Learn More <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php
                endif;
            endforeach;
            ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>