<?php
/**
 * About Page
 * Accredited Inspection Agency
 */

require_once __DIR__ . '/includes/config.php';

$page_title = 'About Us';
$page_description = 'Learn about Accredited Inspection Agency - Founded by Amit Joshi with over 20 years of experience in testing and inspection across steel, power, and cement industries.';

include __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>About Us</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/">Home</a></li>
                <li class="breadcrumb-item active">About Us</li>
            </ol>
        </nav>
    </div>
</section>

<!-- About Section -->
<section class="section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="text-primary fw-semibold text-uppercase">Who We Are</span>
                <h2 class="section-title mt-2">Testing, Inspection and Certification</h2>
                <p class="text-muted mb-4">
                    Accredited Inspection Agency (AIA) was founded in 2025 with a clear vision—to provide accurate,
                    reliable, and transparent inspection and testing services that add true value to trade and industry.
                </p>
                <p class="text-muted mb-4">
                    With over 20 years of industry experience spanning steel, power, and cement sectors, we bring
                    unparalleled expertise to every project. Our team comprises well-qualified, experienced Technical
                    and Professional persons including Mining Engineers, Scientists, and Chemists.
                </p>
                <p class="text-muted">
                    At AIA, we believe in offering personalized attention to every client, ensuring uncompromised
                    quality and integrity in all our services.
                </p>
            </div>
            <div class="col-lg-6">
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
                            <div class="number">ISO</div>
                            <div class="label">Standards Compliant</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-box bg-light rounded-3">
                            <div class="number">NABL</div>
                            <div class="label">Partner Labs</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Founder Section -->
<section class="section bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="text-center mb-5">
                    <span class="text-primary fw-semibold text-uppercase">Leadership</span>
                    <h2 class="section-title mt-2">Founder's Message</h2>
                </div>
                <div class="founder-card">
                    <div class="row align-items-center g-4">
                        <div class="col-md-3 text-center">
                            <img src="<?php echo BASE_URL; ?>/assets/images/Amit_Joshi.png"
                                alt="Mr. Amit Joshi - Founder" class="rounded-circle mb-3 shadow"
                                style="width: 150px; height: 150px; object-fit: cover; object-position: center top;">
                            <h5 class="mb-1">Mr. Amit Joshi</h5>
                            <span class="text-muted">Founder</span>
                        </div>
                        <div class="col-md-9">
                            <blockquote class="mb-0">
                                <p class="text-muted mb-3">
                                    "Accredited Inspection Agency (AIA) was founded in 2025, bringing with me over 20
                                    years of experience in inspection and testing across industries such as steel,
                                    power, and cement. Having previously worked with leading organizations like SGS and
                                    IGI, I established AIA in Raigarh, Chhattisgarh, with a clear vision—to provide
                                    accurate, reliable, and transparent inspection and testing services that add true
                                    value to trade and industry."
                                </p>
                                <p class="text-muted mb-3">
                                    "At AIA, we believe in offering personalised attention to every client, ensuring
                                    uncompromised quality and integrity in all our services. I take immense pride in our
                                    team's dedication to exceeding client expectations while navigating today's global
                                    complexities with agility, innovation, and collaboration."
                                </p>
                                <p class="text-muted mb-0">
                                    "Looking ahead, our focus remains on expanding AIA's global footprint through
                                    strategic partnerships and advanced technologies. We are committed to setting new
                                    benchmarks in inspection services and reinforcing our reputation as a trusted
                                    partner in quality assurance."
                                </p>
                            </blockquote>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Vision Section -->
<section class="section">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="icon-box mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-bullseye" style="font-size: 1.5rem;"></i>
                        </div>
                        <h4>Our Mission</h4>
                        <p class="text-muted mb-0">
                            To provide accurate, reliable, and transparent inspection and testing services that add true
                            value to trade and industry. We are dedicated to mitigating risks associated with the
                            movement of commodities through all modes of transport, ensuring utmost reliability and
                            accuracy at every stage.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="icon-box mb-3" style="width: 60px; height: 60px;">
                            <i class="bi bi-eye" style="font-size: 1.5rem;"></i>
                        </div>
                        <h4>Our Vision</h4>
                        <p class="text-muted mb-0">
                            To expand AIA's global footprint through strategic partnerships and advanced technologies.
                            We aim to set new benchmarks in inspection services and reinforce our reputation as a
                            trusted partner in quality assurance, contributing to nation-building through international
                            trade excellence.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="section bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-primary fw-semibold text-uppercase">What Drives Us</span>
            <h2 class="section-title mt-2">Our Core Values</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="bi bi-shield-check text-primary mb-3" style="font-size: 2.5rem;"></i>
                        <h5>Integrity</h5>
                        <p class="text-muted small mb-0">Uncompromised quality and transparency in all our services.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="bi bi-graph-up-arrow text-primary mb-3" style="font-size: 2.5rem;"></i>
                        <h5>Excellence</h5>
                        <p class="text-muted small mb-0">Commitment to exceeding client expectations at every step.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="bi bi-lightbulb text-primary mb-3" style="font-size: 2.5rem;"></i>
                        <h5>Innovation</h5>
                        <p class="text-muted small mb-0">Embracing advanced technologies and modern methodologies.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center">
                    <div class="card-body">
                        <i class="bi bi-people text-primary mb-3" style="font-size: 2.5rem;"></i>
                        <h5>Collaboration</h5>
                        <p class="text-muted small mb-0">Building strategic partnerships for mutual growth.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Certificates & Documents Section -->
<section class="section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="text-primary fw-semibold text-uppercase">Trust & Compliance</span>
            <h2 class="section-title mt-2">Our Certifications & Documents</h2>
            <p class="section-subtitle">We are proud to be certified and registered with various government and
                international bodies.</p>
        </div>

        <?php
        $certificates = [
            [
                'title' => 'UDYAM Registration',
                'description' => 'Ministry of MSME registration certificate.',
                'icon' => 'bi-award',
                'file' => 'UDYAM CERTIFICATE AIA.pdf',
                'type' => 'Government'
            ],
            [
                'title' => 'ISO 9001:2015',
                'description' => 'Quality Management System certification.',
                'icon' => 'bi-patch-check',
                'file' => 'ACCREDITED INSPECTION AGENCY PRIVATE LIMITED 9.pdf',
                'type' => 'Quality'
            ],
            [
                'title' => 'ISO 14001:2015',
                'description' => 'Environmental Management System certification.',
                'icon' => 'bi-tree',
                'file' => 'ACCREDITED INSPECTION AGENCY PRIVATE LIMITED 14.pdf',
                'type' => 'Environment'
            ],
            [
                'title' => 'ISO 45001:2018',
                'description' => 'Occupational Health & Safety certification.',
                'icon' => 'bi-shield-check',
                'file' => 'ACCREDITED INSPECTION AGENCY PRIVATE LIMITED 45.pdf',
                'type' => 'Safety'
            ],
            [
                'title' => 'Company Incorporation',
                'description' => 'Official incorporation from MCA.',
                'icon' => 'bi-building',
                'file' => 'SPICE + Part B_Approval Letter_AB6808527 (4).pdf',
                'type' => 'Legal'
            ],
            [
                'title' => 'Startup India Registration',
                'description' => 'Recognized by DPIIT.',
                'icon' => 'bi-rocket-takeoff',
                'file' => '10122025202542419-9ccba450-25df-416f-8e1a-ba102c27336d.pdf',
                'type' => 'Recognition'
            ]
        ];
        ?>

        <div class="row g-4">
            <?php foreach ($certificates as $cert): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 certificate-card">
                        <div class="card-body">
                            <div class="d-flex align-items-start mb-3">
                                <div class="cert-icon me-3">
                                    <i class="bi <?php echo $cert['icon']; ?>"></i>
                                </div>
                                <div>
                                    <span
                                        class="badge bg-primary-subtle text-primary mb-2"><?php echo $cert['type']; ?></span>
                                    <h6 class="mb-0"><?php echo $cert['title']; ?></h6>
                                </div>
                            </div>
                            <p class="text-muted small mb-3"><?php echo $cert['description']; ?></p>
                            <a href="<?php echo BASE_URL; ?>/assets/certificate and documents/<?php echo rawurlencode($cert['file']); ?>"
                                target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-file-earmark-pdf me-1"></i>View
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <h3>Partner With Us for Quality Assurance</h3>
        <p class="mb-4 opacity-75">Let's work together to ensure compliance and build trust.</p>
        <a href="<?php echo BASE_URL; ?>/contact.php" class="btn btn-light btn-lg">Contact Us Today</a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>