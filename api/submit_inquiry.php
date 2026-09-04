<?php
/**
 * Inquiry Submission API
 * Accredited Inspection Agency
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/anti_spam.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Location: ' . BASE_URL . '/contact.php');
    exit;
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
    set_flash_message('error', 'Invalid request session. Please refresh and try again.');
    header('Location: ' . BASE_URL . '/contact.php');
    exit;
}

// -------------------------------------------------------------
// Multi-Layer Anti-Spam & Bot Detection
// -------------------------------------------------------------
$spam_reason = '';
if (is_spam_inquiry($_POST, $spam_reason)) {
    // Log blocked spam attempt
    error_log(sprintf(
        "[SPAM BLOCKED] %s | IP: %s | Name: %s | Email: %s",
        $spam_reason,
        get_client_ip(),
        $_POST['name'] ?? '',
        $_POST['email'] ?? ''
    ));

    // SILENT DROP / FAKE SUCCESS:
    // Do NOT send any email (safeguarding OCI monthly mail limit)
    // Do NOT insert into database (preventing database pollution)
    // Return fake success so bots do not retry with other vectors
    set_flash_message('success', 'Thank you for your inquiry! We will get back to you soon.');
    header('Location: ' . BASE_URL . '/contact.php');
    exit;
}

// Validate required fields
$errors = [];

$name = isset($_POST['name']) ? sanitize_input($_POST['name']) : '';
$email = isset($_POST['email']) ? sanitize_input($_POST['email']) : '';
$phone = isset($_POST['phone']) ? sanitize_input($_POST['phone']) : '';
$service_id = isset($_POST['service_id']) && $_POST['service_id'] !== '' ? (int) $_POST['service_id'] : null;
$message = isset($_POST['message']) ? sanitize_input($_POST['message']) : '';

// Validation
if (empty($name)) {
    $errors[] = 'Name is required.';
}

if (empty($email)) {
    $errors[] = 'Email is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

if (empty($message)) {
    $errors[] = 'Message is required.';
}

// Phone validation (optional but must be valid if provided)
if (!empty($phone) && !preg_match('/^[0-9+\s\-]{7,15}$/', $phone)) {
    $errors[] = 'Please enter a valid phone number.';
}

// If errors, redirect back
if (!empty($errors)) {
    set_flash_message('error', implode(' ', $errors));
    header('Location: ' . BASE_URL . '/contact.php');
    exit;
}

try {
    // Save clean inquiry to database
    $data = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'service_id' => $service_id,
        'message' => $message
    ];

    $result = save_inquiry($pdo, $data);

    if ($result) {
        // Get service name for email
        $service_name = 'General Inquiry';
        if ($service_id) {
            $service = get_service($pdo, $service_id);
            if ($service) {
                $service_name = $service['title'];
            }
        }

        // Send email notifications (if SMTP is configured)
        if (SMTP_USER !== 'your-email@gmail.com') {
            require_once __DIR__ . '/send_email.php';

            // Send notification to admin ONLY (Preserving OCI monthly quota & avoiding bounce backscatter)
            $admin_subject = 'New Inquiry: ' . $service_name . ' from ' . $name;
            $admin_body = "
<h3>New Customer Inquiry Received</h3>
<p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>
<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
<p><strong>Phone:</strong> " . htmlspecialchars($phone ?: 'Not provided') . "</p>
<p><strong>Service:</strong> " . htmlspecialchars($service_name) . "</p>
<p><strong>Message:</strong></p>
<div style='background:#f4f4f4; padding:15px; border-left:4px solid #1a365d; margin:10px 0;'>
" . nl2br(htmlspecialchars($message)) . "
</div>
<hr>
<p><a href='" . BASE_URL . "/admin/inquiries.php'>Click here to view all inquiries in Admin Panel</a></p>
";
            send_email(ADMIN_EMAIL, 'Admin', $admin_subject, $admin_body, true);

            // Optional auto-reply to customer (Only if enabled, to protect OCI monthly limit)
            if (defined('ENABLE_INQUIRY_AUTO_REPLY') && ENABLE_INQUIRY_AUTO_REPLY === true) {
                $user_subject = 'Thank you for contacting Accredited Inspection Agency';
                $user_body = "
Dear {$name},

Thank you for reaching out to Accredited Inspection Agency. We have received your inquiry and our team will get back to you shortly.

Your Inquiry Details:
- Service: {$service_name}
- Message: {$message}

If you have any urgent questions, please feel free to call us at " . SITE_PHONE . ".

Best regards,
Accredited Inspection Agency
" . SITE_ADDRESS . "
";
                send_email($email, $name, $user_subject, $user_body);
            }
        }

        set_flash_message('success', 'Thank you for your inquiry! We will get back to you soon.');
    } else {
        set_flash_message('error', 'An error occurred. Please try again later.');
    }
} catch (Exception $e) {
    if (IS_LOCALHOST) {
        set_flash_message('error', 'Error: ' . $e->getMessage());
    } else {
        set_flash_message('error', 'An error occurred. Please try again later.');
    }
}

header('Location: ' . BASE_URL . '/contact.php');
exit;
