<?php
/**
 * Email Sending Function using PHPMailer
 * 
 * Note: You need to install PHPMailer via Composer or manually include it.
 * For production, run: composer require phpmailer/phpmailer
 * For development, emails are logged to file instead.
 */

require_once __DIR__ . '/../includes/config.php';

/**
 * Send email using SMTP
 * Falls back to logging if PHPMailer is not installed
 */
function send_email($to_email, $to_name, $subject, $body, $is_html = false)
{
    // Check if PHPMailer is available
    $phpmailer_path = __DIR__ . '/../vendor/autoload.php';

    if (file_exists($phpmailer_path)) {
        require_once $phpmailer_path;

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER;
            $mail->Password = SMTP_PASS;
            $mail->Password = SMTP_PASS;
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = SMTP_PORT;

            // Recipients
            $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress($to_email, $to_name);
            $mail->addReplyTo(SITE_EMAIL, SITE_NAME);

            // Content
            $mail->isHTML($is_html);
            $mail->Subject = $subject;
            $mail->Body = $body;
            if ($is_html) {
                $mail->AltBody = strip_tags($body);
            }

            $mail->send();
            return true;
        } catch (Exception $e) {
            // Log full error details
            $log_dir = __DIR__ . '/../logs';
            if (!is_dir($log_dir)) {
                mkdir($log_dir, 0755, true);
            }
            $error_msg = "[" . date('Y-m-d H:i:s') . "] SMTP Error: " . $mail->ErrorInfo . "\n";
            file_put_contents($log_dir . '/smtp_debug.log', $error_msg, FILE_APPEND);

            error_log('Email Error: ' . $mail->ErrorInfo);
            return false;
        }
    } else {
        // Log email to file for development
        $log_dir = __DIR__ . '/../logs';
        if (!is_dir($log_dir)) {
            mkdir($log_dir, 0755, true);
        }

        $log_file = $log_dir . '/emails_' . date('Y-m-d') . '.log';
        $log_content = "
========================================
Date: " . date('Y-m-d H:i:s') . "
To: {$to_name} <{$to_email}>
Subject: {$subject}
----------------------------------------
{$body}
========================================

";
        file_put_contents($log_file, $log_content, FILE_APPEND);
        return true;
    }
}
