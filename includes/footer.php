<!-- Footer -->
<footer class="footer bg-dark text-light pt-5 pb-3">
    <div class="container">
        <div class="row g-4">
            <!-- Company Info -->
            <div class="col-lg-4 col-md-6">
                <h5 class="text-white mb-3">Accredited Inspection Agency</h5>
                <p class="text-light-50 mb-3">Delivering precise testing and inspection services that build trust,
                    ensure compliance, and safeguard your business every step of the way.</p>
                <div class="d-flex gap-3">
                    <a href="#" class="text-light"><i class="bi bi-facebook fs-5"></i></a>
                    <a href="#" class="text-light"><i class="bi bi-linkedin fs-5"></i></a>
                    <a href="#" class="text-light"><i class="bi bi-twitter-x fs-5"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6">
                <h6 class="text-white mb-3">Quick Links</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?php echo BASE_URL; ?>/">Home</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/about.php">About Us</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/services.php">Services</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/contact.php">Contact</a></li>
                </ul>
            </div>

            <!-- Services -->
            <div class="col-lg-3 col-md-6">
                <h6 class="text-white mb-3">Our Services</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?php echo BASE_URL; ?>/services.php">Sampling</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/services.php">Inspection</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/services.php">Testing</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/services.php">IT Support</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/services.php">Web App Development</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/services.php">Digital Marketing</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-3 col-md-6">
                <h6 class="text-white mb-3">Contact Us</h6>
                <ul class="list-unstyled text-light-50">
                    <li class="mb-2">
                        <i class="bi bi-geo-alt me-2"></i>
                        <?php echo SITE_ADDRESS; ?>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-telephone me-2"></i>
                        <?php echo SITE_PHONE; ?>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-envelope me-2"></i>
                        <?php echo SITE_EMAIL; ?>
                    </li>
                </ul>
            </div>
        </div>

        <hr class="my-4 border-secondary">

        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0 text-light-50">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All Rights
                    Reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0 text-light-50">Designed for Quality & Trust</p>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<!-- Custom JS -->
<script src="<?php echo BASE_URL; ?>/assets/js/script.js?v=2"></script>
<script>
    // Failsafe to ensure page becomes visible
    document.addEventListener('DOMContentLoaded', () =>  {
        document.body.classList.add('loaded');
        console.log('Page loaded (v3)');
    });
    // Fallback
    setTimeout(() => document.body.classList.add('loaded'), 500);
</script>
</body>

</html>