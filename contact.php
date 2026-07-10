<?php
/**
 * Contact Page
 * Accredited Inspection Agency
 */

require_once __DIR__ . '/includes/db.php';

$page_title = 'Contact Us';
$page_description = 'Get in touch with Accredited Inspection Agency for Testing, Inspection and Certification services. Request a quote or inquiry today.';

// Get services for dropdown
$services = get_services($pdo);

// Pre-select service if passed via URL
$selected_service = isset($_GET['service']) ? (int) $_GET['service'] : '';

// Generate CSRF token
$csrf_token = generate_csrf_token();

include __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Contact Us</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>/">Home</a></li>
                <li class="breadcrumb-item active">Contact Us</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Contact Section -->
<section class="section">
    <div class="container">
        <div class="row g-5">
            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="contact-form">
                    <h4 class="mb-4">Send Us a Message</h4>
                    <form action="<?php echo BASE_URL; ?>/api/submit_inquiry.php" method="POST" class="needs-validation"
                        novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required
                                    placeholder="Enter your name">
                                <div class="invalid-feedback">Please enter your name.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address <span
                                        class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required
                                    placeholder="Enter your email">
                                <div class="invalid-feedback">Please enter a valid email address.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                    placeholder="Enter your phone number" maxlength="10">
                            </div>
                            <div class="col-md-6">
                                <label for="service" class="form-label">Service Required</label>
                                <select class="form-select" id="service" name="service_id">
                                    <option value="">Select a service</option>
                                    <?php foreach ($services as $service): ?>
                                        <option value="<?php echo $service['id']; ?>" <?php echo $selected_service == $service['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($service['title']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="message" class="form-label">Message <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="message" name="message" rows="5" required
                                    placeholder="Tell us about your requirements"></textarea>
                                <div class="invalid-feedback">Please enter your message.</div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-send me-2"></i>Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-5">
                <div class="contact-info-box">
                    <h4>Get In Touch</h4>
                    <p class="opacity-75 mb-4">Have questions? We're here to help. Contact us through any of the
                        following methods.</p>

                    <div class="contact-info-item">
                        <i class="bi bi-geo-alt"></i>
                        <div>
                            <h6 class="mb-1 text-white">Our Office</h6>
                            <p class="mb-0 opacity-75"><?php echo SITE_ADDRESS; ?></p>
                        </div>
                    </div>

                    <div class="contact-info-item">
                        <i class="bi bi-telephone"></i>
                        <div>
                            <h6 class="mb-1 text-white">Phone Numbers</h6>
                            <p class="mb-0 opacity-75"><?php echo SITE_PHONE; ?></p>

                        </div>
                    </div>

                    <div class="contact-info-item">
                        <i class="bi bi-envelope"></i>
                        <div>
                            <h6 class="mb-1 text-white">Email Address</h6>
                            <p class="mb-0 opacity-75"><?php echo SITE_EMAIL; ?></p>
                        </div>
                    </div>

                    <div class="contact-info-item mb-0">
                        <i class="bi bi-clock"></i>
                        <div>
                            <h6 class="mb-1 text-white">Working Hours</h6>
                            <p class="mb-0 opacity-75">Monday - Saturday: 9:00 AM - 6:00 PM</p>
                            <p class="mb-0 opacity-75">Sunday: Closed</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Contact -->
                <div class="card mt-4">
                    <div class="card-body">
                        <h5 class="mb-3">Quick Contact</h5>
                        <div class="d-grid gap-2">
                            <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', SITE_PHONE); ?>"
                                class="btn btn-outline-primary">
                                <i class="bi bi-telephone me-2"></i>Call Now
                            </a>
                            <a href="mailto:<?php echo SITE_EMAIL; ?>" class="btn btn-outline-primary">
                                <i class="bi bi-envelope me-2"></i>Send Email
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="py-0">
    <div class="ratio ratio-21x9">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3702.3949325774684!2d83.40688907473755!3d21.880862757968814!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3a27256fdcd164e1%3A0x129c50f4a98ee85c!2sIndane%20-%20Khalsa!5e0!3m2!1sen!2sin!4v1766137406057!5m2!1sen!2sin"
            style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>