<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/api/send_email.php';

echo "Attempting to send email...\n";
$result = send_email('amitjoshi20011@gmail.com', 'Admin', 'Test Email', 'This is a test email from CLI.');

if ($result) {
    echo "Email sent successfully.\n";
} else {
    echo "Email failed.\n";
}
