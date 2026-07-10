<?php
/**
 * Home Page
 * Accredited Inspection Agency
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/cache.php';

$page_title = 'Home';
$page_description = 'Accredited Inspection Agency - Professional Testing, Inspection and Certification Services. Delivering precise services that build trust and ensure compliance.';

// Get services for preview (cached for 5 min)
$services = get_cached_services($pdo);

include __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h1 class="animate-on-scroll fade-up"><?php echo SITE_TAGLINE; ?></h1>
                <p class="lead animate-on-scroll fade-up delay-100">Delivering precise testing and inspection services
                    that build trust, ensure compliance,
                    and safeguard your business every step of the way.</p>
                <div class="d-flex fltiex-wrap gap-3 animate-on-scroll fade-up delay-200">
                    <a href="<?php echo BASE_URL; ?>/services.php" class="btn btn-light btn-lg">Our Services</a>
                    <a href="<?php echo BASE_URL; ?>/contact.php" class="btn btn-outline-light btn-lg">Get a Quote</a>
                </div>
            </div>
            <div class="col-lg-5 mt-5 mt-lg-0 animate-on-scroll fade-in delay-300">
                <div class="position-relative rounded-4 overflow-hidden shadow-lg border border-3 border-white-50 ms-auto"
                    style="max-width: 450px;">
                    <video autoplay muted loop playsinline class="w-100 h-100 object-fit-cover d-block">
                        <source src="<?php echo BASE_URL; ?>/assets/videos/hero_video.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Preview Section -->
<section class="section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 animate-on-scroll slide-in-left">
                <span class="text-primary fw-semibold text-uppercase">About Us</span>
                <h2 class="section-title mt-2">Testing, Inspection and Certification Excellence</h2>
                <p class="text-muted mb-4">
                    Accredited Inspection Agency (AIA) was founded in 2025, bringing over 20 years of experience in
                    inspection and testing across industries such as steel, power, and cement. We provide accurate,
                    reliable, and transparent inspection and testing services that add true value to trade and industry.
                </p>
                <div class="row g-4 mb-4">
                    <div class="col-sm-6">
                        <div class="feature-box">
                            <div class="icon">
                                <i class="bi bi-award"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">20+ Years</h6>
                                <small class="text-muted">Industry Experience</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="feature-box">
                            <div class="icon">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Certified</h6>
                                <small class="text-muted">Quality Standards</small>
                            </div>
                        </div>
                    </div>
                </div>
                <a href="<?php echo BASE_URL; ?>/about.php" class="btn btn-primary">Learn More About Us</a>
            </div>
            <div class="col-lg-6 animate-on-scroll slide-in-right">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="stat-box bg-light rounded-3">
                            <div class="number">20+</div>
                            <div class="label">Years Experience</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-box bg-light rounded-3">
                            <div class="number">3</div>
                            <div class="label">Core Services</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-box bg-light rounded-3">
                            <div class="number">100%</div>
                            <div class="label">Quality Assured</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-box bg-light rounded-3">
                            <div class="number">24/7</div>
                            <div class="label">Support Available</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="section bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-primary fw-semibold text-uppercase">What We Offer</span>
            <h2 class="section-title mt-2">Our Services</h2>
            <p class="section-subtitle">We are dedicated to mitigating risks associated with the movement of
                commodities, ensuring reliability and accuracy at every stage.</p>
        </div>
        <div class="row g-4">
            <?php foreach ($services as $index => $service): ?>
                <div class="col-md-4 animate-on-scroll fade-up <?php echo $index > 0 ? 'delay-' . ($index * 100) : ''; ?>">
                    <div class="card service-card h-100">
                        <div class="card-body">
                            <div class="icon-box">
                                <i class="bi <?php echo htmlspecialchars($service['icon']); ?>"></i>
                            </div>
                            <h5><?php echo htmlspecialchars($service['title']); ?></h5>
                            <p class="text-muted mb-3">
                                <?php echo htmlspecialchars(substr($service['description'], 0, 100)); ?>...
                            </p>
                            <a href="<?php echo BASE_URL; ?>/service-detail.php?id=<?php echo $service['id']; ?>"
                                class="btn btn-outline-primary btn-sm">
                                Learn More <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
        <div class="text-center mt-5">
            <a href="<?php echo BASE_URL; ?>/services.php" class="btn btn-primary btn-lg">View All Services</a>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-primary fw-semibold text-uppercase">Why Choose Us</span>
            <h2 class="section-title mt-2">Our Commitment to Excellence</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3 animate-on-scroll fade-up">
                <div class="text-center">
                    <div class="icon-box mx-auto mb-3">
                        <i class="bi bi-patch-check"></i>
                    </div>
                    <h5>Certified Experts</h5>
                    <p class="text-muted small">Well-qualified technical professionals including Mining Engineers,
                        Scientists, and Chemists.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 animate-on-scroll fade-up delay-100">
                <div class="text-center">
                    <div class="icon-box mx-auto mb-3">
                        <i class="bi bi-bullseye"></i>
                    </div>
                    <h5>Accuracy</h5>
                    <p class="text-muted small">Precise testing and inspection following Indian and International
                        standards.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 animate-on-scroll fade-up delay-200">
                <div class="text-center">
                    <div class="icon-box mx-auto mb-3">
                        <i class="bi bi-speedometer2"></i>
                    </div>
                    <h5>Fast Turnaround</h5>
                    <p class="text-muted small">Quick and efficient service delivery without compromising quality.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 animate-on-scroll fade-up delay-300">
                <div class="text-center">
                    <div class="icon-box mx-auto mb-3">
                        <i class="bi bi-headset"></i>
                    </div>
                    <h5>24/7 Support</h5>
                    <p class="text-muted small">Round-the-clock customer support for all your inquiries.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Certifications & Credentials Section -->
<section class="section bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-primary fw-semibold text-uppercase">Our Credentials</span>
            <h2 class="section-title mt-2">Certifications & Legal Documents</h2>
            <p class="section-subtitle">We are a legally registered and government-certified company. View and download
                our official documents below.</p>
        </div>
        <div class="row g-4 justify-content-center">
            <!-- UDYAM Certificate -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="icon-box mx-auto mb-3"
                            style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                            <i class="bi bi-patch-check-fill text-white"></i>
                        </div>
                        <h5 class="card-title">UDYAM Registration</h5>
                        <p class="text-muted small mb-3">Ministry of MSME, Government of India certified enterprise
                            registration.</p>
                        <span class="badge bg-success mb-3">MSME Registered</span>
                        <div>
                            <a href="<?php echo BASE_URL; ?>/assets/certificate and documents/UDYAM CERTIFICATE AIA.pdf"
                                class="btn btn-outline-primary btn-sm" target="_blank">
                                <i class="bi bi-file-earmark-pdf me-1"></i>View Certificate
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MCA Registration -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="icon-box mx-auto mb-3"
                            style="background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);">
                            <i class="bi bi-building-check text-white"></i>
                        </div>
                        <h5 class="card-title">Company Incorporation</h5>
                        <p class="text-muted small mb-3">Ministry of Corporate Affairs (MCA) registered Private Limited
                            Company.</p>
                        <span class="badge bg-primary mb-3">MCA Registered</span>
                        <div>
                            <a href="<?php echo BASE_URL; ?>/assets/certificate and documents/SPICE + Part B_Approval Letter_AB6808527 (4).pdf"
                                class="btn btn-outline-primary btn-sm" target="_blank">
                                <i class="bi bi-file-earmark-pdf me-1"></i>View Certificate
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GST Registration -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="icon-box mx-auto mb-3"
                            style="background: linear-gradient(135deg, #fd7e14 0%, #dc3545 100%);">
                            <i class="bi bi-receipt-cutoff text-white"></i>
                        </div>
                        <h5 class="card-title">Startup India Registration</h5>
                        <p class="text-muted small mb-3">Recognized by Department for Promotion of Industry and Internal
                            Trade.</p>
                        <span class="badge bg-warning text-dark mb-3">Startup India Recognized</span>
                        <div>
                            <a href="<?php echo BASE_URL; ?>/assets/certificate and documents/10122025202542419-9ccba450-25df-416f-8e1a-ba102c27336d.pdf"
                                class="btn btn-outline-primary btn-sm" target="_blank">
                                <i class="bi bi-file-earmark-pdf me-1"></i>View Certificate
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Documents Row -->
        <div class="row g-4 mt-2 justify-content-center">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="mb-3"><i class="bi bi-folder2-open text-primary me-2"></i>Additional Company
                            Documents</h5>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <a href="<?php echo BASE_URL; ?>/assets/certificate and documents/ACCREDITED INSPECTION AGENCY PRIVATE LIMITED 9.pdf"
                                    class="btn btn-outline-secondary w-100 text-start" target="_blank">
                                    <i class="bi bi-file-earmark-pdf text-danger me-2"></i>Company Document 1
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="<?php echo BASE_URL; ?>/assets/certificate and documents/ACCREDITED INSPECTION AGENCY PRIVATE LIMITED 14.pdf"
                                    class="btn btn-outline-secondary w-100 text-start" target="_blank">
                                    <i class="bi bi-file-earmark-pdf text-danger me-2"></i>Company Document 2
                                </a>
                            </div>
                            <div class="col-md-4">
                                <a href="<?php echo BASE_URL; ?>/assets/certificate and documents/ACCREDITED INSPECTION AGENCY PRIVATE LIMITED 45.pdf"
                                    class="btn btn-outline-secondary w-100 text-start" target="_blank">
                                    <i class="bi bi-file-earmark-pdf text-danger me-2"></i>Company Document 3
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Client Section -->
<section class="section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-primary fw-semibold text-uppercase">Our Partners</span>
            <h2 class="section-title mt-2">Our Valued Clients</h2>
        </div>
        <div class="row justify-content-center align-items-center g-4">
            <div class="col-6 col-md-4 col-lg-3">
                <div
                    class="client-logo-box text-center p-3 animate-on-scroll fade-up border rounded shadow-sm hover-shadow transition-all">
                    <img src="<?php echo BASE_URL; ?>/assets/images/clients/ganesha_steel.jpg" alt="Ganesha Steel"
                        class="img-fluid" style="max-height: 120px;">
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div
                    class="client-logo-box text-center p-3 animate-on-scroll fade-up border rounded shadow-sm hover-shadow transition-all">
                    <img src="<?php echo BASE_URL; ?>/assets/images/clients/Bengal_Energy.jpeg" alt="Bengal Energy"
                        class="img-fluid" style="max-height: 120px;">
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div
                    class="client-logo-box text-center p-3 animate-on-scroll fade-up border rounded shadow-sm hover-shadow transition-all">
                    <img src="<?php echo BASE_URL; ?>/assets/images/clients/Precise_Global.jpeg" alt="Precise Global"
                        class="img-fluid" style="max-height: 120px;">
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div
                    class="client-logo-box text-center p-3 animate-on-scroll fade-up border rounded shadow-sm hover-shadow transition-all">
                    <img src="<?php echo BASE_URL; ?>/assets/images/clients/OMPL.jpeg" alt="OMPL"
                        class="img-fluid" style="max-height: 120px;">
                </div>
            </div>
            <div class="col-6 col-md-4 col-lg-3">
                <div
                    class="client-logo-box text-center p-3 animate-on-scroll fade-up border rounded shadow-sm hover-shadow transition-all">
                    <img src="<?php echo BASE_URL; ?>/assets/images/clients/SPRN.jpeg" alt="SPRN"
                        class="img-fluid" style="max-height: 120px;">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <h3>Ready to Ensure Quality and Compliance?</h3>
        <p class="mb-4 opacity-75">Contact us today for professional inspection and testing services.</p>
        <a href="<?php echo BASE_URL; ?>/contact.php" class="btn btn-light btn-lg">Get Started</a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>