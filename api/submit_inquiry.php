<?php
/**
 * Inquiry Submission API
 * Accredited Inspection Agency
 */

require_once __DIR__ . '/../includes/db.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Location: ' . BASE_URL . '/contact.php');
    exit;
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
    set_flash_message('error', 'Invalid request. Please try again.');
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
} else {
    // Spam filtering
    if (preg_match('/(http:\/\/|https:\/\/|www\.)/i', $message)) {
        $errors[] = 'Messages containing links are not allowed for security reasons.';
    }
    if (preg_match('/(jackpot|casino|lottery|crypto|bitcoin|serial number id)/i', $message)) {
        $errors[] = 'Your message contains blocked keywords and has been rejected.';
    }
}

// Phone validation (optional but must be valid if provided)
if (!empty($phone) && !preg_match('/^[0-9]{10}$/', $phone)) {
    $errors[] = 'Please enter a valid 10-digit phone number.';
}

// If errors, redirect back
if (!empty($errors)) {
    set_flash_message('error', implode(' ', $errors));
    header('Location: ' . BASE_URL . '/contact.php');
    exit;
}

try {
    // Save inquiry to database
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

            // Send confirmation to user
            $user_subject = 'Thank you for contacting Accredited Inspection Agency';
            $user_body = "
Dear {$name},

Thank you for reaching out to Accredited Inspection Agency. We have received your inquiry and will get back to you shortly.

Your Inquiry Details:
- Service: {$service_name}
- Message: {$message}

If you have any urgent questions, please don't hesitate to call us at " . SITE_PHONE . ".

Best regards,
Accredited Inspection Agency
" . SITE_ADDRESS . "
";
            send_email($email, $name, $user_subject, $user_body);

            // Send notification to admin
            $admin_subject = 'New Inquiry: ' . $service_name;
            $admin_body = "
New inquiry received from the website:

Name: {$name}
Email: {$email}
Phone: {$phone}
Service: {$service_name}

Message:
{$message}

---
View all inquiries at: " . BASE_URL . "/admin/inquiries.php
";
            send_email(ADMIN_EMAIL, 'Admin', $admin_subject, $admin_body);
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
