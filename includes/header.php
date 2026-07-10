<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/seo.php';

// Get current page for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Set default SEO values
$page_title = isset($page_title) ? $page_title : 'Home';
$page_description = isset($page_description) ? $page_description : 'Accredited Inspection Agency - Professional Testing, Inspection and Certification Services in India. ISO 9001:2015 certified agency serving steel, power, cement, and mining industries.';
$page_keywords = isset($page_keywords) ? $page_keywords : '';

// Build canonical URL
$canonical_url = BASE_URL . '/' . ($current_page == 'index' ? '' : $current_page . '.php');

// Extended keywords based on page
$base_keywords = 'inspection agency India, testing services, certification services, third party inspection, lab testing services, coal testing, steel inspection, mineral testing, sampling services, quality assurance, ISO certified inspection, NABL accredited lab, Raigarh, Chhattisgarh';
$full_keywords = $page_keywords ? $page_keywords . ', ' . $base_keywords : $base_keywords;

// Full title for SEO
$full_title = $page_title . ' | ' . SITE_NAME . ' - Testing, Inspection & Certification';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Primary Meta Tags -->
    <title><?php echo $full_title; ?></title>
    <meta name="title" content="<?php echo $full_title; ?>">
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($full_keywords); ?>">
    <meta name="author" content="Accredited Inspection Agency Private Limited">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="googlebot" content="index, follow">

    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo $canonical_url; ?>">

    <!-- Geographic Tags for Local SEO -->
    <meta name="geo.region" content="IN-CT">
    <meta name="geo.placename" content="Raigarh, Chhattisgarh">
    <meta name="geo.position" content="21.8809;83.4069">
    <meta name="ICBM" content="21.8809, 83.4069">

    <!-- Open Graph / Facebook -->
    <?php output_og_tags($full_title, $page_description, $canonical_url); ?>

    <!-- Additional Meta for AI Search Engines -->
    <meta name="application-name" content="Accredited Inspection Agency">
    <meta name="subject" content="Testing, Inspection and Certification Services">
    <meta name="coverage" content="India">
    <meta name="distribution" content="global">
    <meta name="rating" content="general">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?php echo BASE_URL; ?>/assets/images/Agency_Logo.svg">
    <link rel="alternate icon" type="image/x-icon" href="<?php echo BASE_URL; ?>/assets/images/favicon.ico">
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>/assets/images/favicon.ico">
    <link rel="apple-touch-icon" href="<?php echo BASE_URL; ?>/assets/images/Agency_Logo.svg">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php echo BASE_URL; ?>/assets/css/style.css" rel="stylesheet">

    <!-- JSON-LD Structured Data -->
    <?php output_schemas($current_page); ?>
</head>


<body>
    <!-- Top Bar -->
    <div class="top-bar py-2 d-none d-md-block">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <span class="me-4"><i class="bi bi-telephone me-2"></i><?php echo SITE_PHONE; ?></span>
                    <span><i class="bi bi-envelope me-2"></i><?php echo SITE_EMAIL; ?></span>
                </div>
                <div class="col-md-6 text-end">
                    <span><i class="bi bi-clock me-2"></i>Mon - Sat: 9:00 AM - 6:00 PM</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?php echo BASE_URL; ?>/">
                <img src="<?php echo BASE_URL; ?>/assets/images/Agency_Logo.svg?v=2" alt="Accredited Inspection Agency"
                    height="70" class="me-2">
                <span class="brand-text d-none d-md-inline">
                    <strong class="text-primary">Accredited</strong> <span class="text-dark">Inspection Agency</span>
                    <span class="d-block"
                        style="font-size: 0.65rem; color: #28a745; font-weight: 600; letter-spacing: 0.5px;">
                        <i class="bi bi-patch-check-fill me-1"></i>AN ISO CERTIFIED COMPANY 9001-2015
                    </span>
                </span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'index' ? 'active' : ''; ?>"
                            href="<?php echo BASE_URL; ?>/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'about' ? 'active' : ''; ?>"
                            href="<?php echo BASE_URL; ?>/about.php">About Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'services' ? 'active' : ''; ?>"
                            href="<?php echo BASE_URL; ?>/services.php">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'careers' ? 'active' : ''; ?>"
                            href="<?php echo BASE_URL; ?>/careers.php">Careers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'contact' ? 'active' : ''; ?>"
                            href="<?php echo BASE_URL; ?>/contact.php">Contact Us</a>
                    </li>
                </ul>
                <a href="<?php echo BASE_URL; ?>/contact.php" class="btn btn-primary ms-lg-3">Get a Quote</a>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php $flash = get_flash_message();
    if ($flash): ?>
        <div class="container mt-3">
            <div class="alert alert-<?php echo $flash['type'] == 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show"
                role="alert">
                <?php echo $flash['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>