<?php
/**
 * Services Page
 * Accredited Inspection Agency
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/cache.php';

$page_title = 'Services';
$page_description = 'Explore our comprehensive Testing, Inspection and Certification services including Sampling, Inspection, and Testing following IS, ISO, ASTM standards.';

// Get services from database (cached for 5 min)
$services = get_cached_services($pdo);

include __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Our Services</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/">Home</a></li>
                <li class="breadcrumb-item active">Services</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Services Intro -->
<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center animate-on-scroll fade-up">
                <span class="text-primary fw-semibold text-uppercase">What We Offer</span>
                <h2 class="section-title mt-2">Testing, Inspection and Certification</h2>
                <p class="section-subtitle mb-0">
                    We are dedicated to mitigating risks associated with the movement of commodities through all modes
                    of transport or stacks, ensuring utmost reliability and accuracy at every stage.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Services List -->
<?php foreach ($services as $index => $service): ?>
    <section class="section <?php echo $index % 2 == 0 ? '' : 'bg-light'; ?>" id="service-<?php echo $service['id']; ?>">
        <div class="container">
            <div class="row align-items-center g-5 <?php echo $index % 2 == 0 ? '' : 'flex-row-reverse'; ?>">
                <div class="col-lg-6 animate-on-scroll slide-in-left">
                    <div class="icon-box mb-4" style="width: 80px; height: 80px;">
                        <i class="bi <?php echo htmlspecialchars($service['icon']); ?>" style="font-size: 2rem;"></i>
                    </div>
                    <h3 class="mb-3"><?php echo htmlspecialchars($service['title']); ?></h3>
                    <p class="text-muted mb-4">
                        <?php echo nl2br(htmlspecialchars($service['description'])); ?>
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?php echo BASE_URL; ?>/service-detail.php?id=<?php echo $service['id']; ?>"
                            class="btn btn-outline-primary">
                            Learn More <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                        <a href="<?php echo BASE_URL; ?>/contact.php?service=<?php echo $service['id']; ?>"
                            class="btn btn-primary">
                            Get a Quote <i class="bi bi-send ms-1"></i>
                        </a>
                    </div>

                </div>
                <div class="col-lg-6 animate-on-scroll slide-in-right">
                    <div class="card border-0 shadow-lg">
                        <div class="card-body p-4">
                            <h5 class="mb-4">Key Features</h5>
                            <?php if ($service['title'] == 'Sampling'): ?>
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-3 d-flex">
                                        <i class="bi bi-check-circle-fill text-success me-3"></i>
                                        <span>Precise sampling as per IS, ISO, ASTM standards</span>
                                    </li>
                                    <li class="mb-3 d-flex">
                                        <i class="bi bi-check-circle-fill text-success me-3"></i>
                                        <span>On-site sampling of mines, plants & ports</span>
                                    </li>
                                    <li class="mb-3 d-flex">
                                        <i class="bi bi-check-circle-fill text-success me-3"></i>
                                        <span>In-house laboratory testing</span>
                                    </li>
                                    <li class="mb-3 d-flex">
                                        <i class="bi bi-check-circle-fill text-success me-3"></i>
                                        <span>Joint sample preparation</span>
                                    </li>
                                    <li class="d-flex">
                                        <i class="bi bi-check-circle-fill text-success me-3"></i>
                                        <span>Risk mitigation for commodity movement</span>
                                    </li>
                                </ul>
                            <?php elseif ($service['title'] == 'Inspection'): ?>
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-3 d-flex">
                                        <i class="bi bi-check-circle-fill text-success me-3"></i>
                                        <span>Pre-Shipment Assessment</span>
                                    </li>
                                    <li class="mb-3 d-flex">
                                        <i class="bi bi-check-circle-fill text-success me-3"></i>
                                        <span>Loading/Unloading Supervision</span>
                                    </li>
                                    <li class="mb-3 d-flex">
                                        <i class="bi bi-check-circle-fill text-success me-3"></i>
                                        <span>Physical Quality Monitoring</span>
                                    </li>
                                    <li class="mb-3 d-flex">
                                        <i class="bi bi-check-circle-fill text-success me-3"></i>
                                        <span>Weighment Assessment</span>
                                    </li>
                                    <li class="d-flex">
                                        <i class="bi bi-check-circle-fill text-success me-3"></i>
                                        <span>Vessel, Rake, Trucks & Stack Assessment</span>
                                    </li>
                                </ul>
                            <?php elseif ($service['title'] == 'Testing'): ?>
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-3 d-flex">
                                        <i class="bi bi-check-circle-fill text-success me-3"></i>
                                        <span>NABL ISO/IEC 17025:2017 accredited labs</span>
                                    </li>
                                    <li class="mb-3 d-flex">
                                        <i class="bi bi-check-circle-fill text-success me-3"></i>
                                        <span>Coal, Coke, Minerals, Ores testing</span>
                                    </li>
                                    <li class="mb-3 d-flex">
                                        <i class="bi bi-check-circle-fill text-success me-3"></i>
                                        <span>Fertilizers & Agricultural Products</span>
                                    </li>
                                    <li class="mb-3 d-flex">
                                        <i class="bi bi-check-circle-fill text-success me-3"></i>
                                        <span>Environmental Testing</span>
                                    </li>
                                    <li class="d-flex">
                                        <i class="bi bi-check-circle-fill text-success me-3"></i>
                                        <span>Compliance testing with applicable standards</span>
                                    </li>
                                </ul>
                            <?php else: ?>
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-3 d-flex">
                                        <i class="bi bi-check-circle-fill text-success me-3"></i>
                                        <span>Professional service delivery</span>
                                    </li>
                                    <li class="mb-3 d-flex">
                                        <i class="bi bi-check-circle-fill text-success me-3"></i>
                                        <span>Experienced technical team</span>
                                    </li>
                                    <li class="d-flex">
                                        <i class="bi bi-check-circle-fill text-success me-3"></i>
                                        <span>Quality assurance guaranteed</span>
                                    </li>
                                </ul>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endforeach; ?>

<!-- Industries Served -->
<section class="section bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-primary fw-semibold text-uppercase">Industries</span>
            <h2 class="section-title mt-2">Industries We Serve</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4 col-lg-2">
                <div class="card h-100 text-center py-3">
                    <div class="card-body">
                        <i class="bi bi-building text-primary mb-2" style="font-size: 2rem;"></i>
                        <h6 class="mb-0">Steel</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="card h-100 text-center py-3">
                    <div class="card-body">
                        <i class="bi bi-lightning-charge text-primary mb-2" style="font-size: 2rem;"></i>
                        <h6 class="mb-0">Power</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="card h-100 text-center py-3">
                    <div class="card-body">
                        <i class="bi bi-bricks text-primary mb-2" style="font-size: 2rem;"></i>
                        <h6 class="mb-0">Cement</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="card h-100 text-center py-3">
                    <div class="card-body">
                        <i class="bi bi-minecart-loaded text-primary mb-2" style="font-size: 2rem;"></i>
                        <h6 class="mb-0">Mining</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="card h-100 text-center py-3">
                    <div class="card-body">
                        <i class="bi bi-flower2 text-primary mb-2" style="font-size: 2rem;"></i>
                        <h6 class="mb-0">Agriculture</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-2">
                <div class="card h-100 text-center py-3">
                    <div class="card-body">
                        <i class="bi bi-tree text-primary mb-2" style="font-size: 2rem;"></i>
                        <h6 class="mb-0">Environment</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <h3>Need Professional Inspection Services?</h3>
        <p class="mb-4 opacity-75">Contact us today for a customized solution tailored to your requirements.</p>
        <a href="<?php echo BASE_URL; ?>/contact.php" class="btn btn-light btn-lg">Request a Quote</a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>