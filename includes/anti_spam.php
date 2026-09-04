<?php
/**
 * Anti-Spam and Bot Protection Module
 * Accredited Inspection Agency
 */

if (!defined('BASE_URL')) {
    require_once __DIR__ . '/config.php';
}

/**
 * Generate Anti-Spam Token for Form
 * Stores form generation timestamp in session
 */
function generate_form_token()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $time = time();
    $_SESSION['form_load_time'] = $time;
    $hash = hash_hmac('sha256', (string)$time, 'aia_antispam_key_' . session_id());
    return base64_encode($time . ':' . $hash);
}

/**
 * Get Client IP Address safely
 */
function get_client_ip()
{
    $ip_keys = [
        'HTTP_CF_CONNECTING_IP', // Cloudflare
        'HTTP_X_FORWARDED_FOR',  // Proxy / Load balancer
        'HTTP_X_REAL_IP',        // Nginx proxy
        'REMOTE_ADDR'            // Direct connection
    ];

    foreach ($ip_keys as $key) {
        if (!empty($_SERVER[$key])) {
            $ip_list = explode(',', $_SERVER[$key]);
            $ip = trim($ip_list[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
}

/**
 * IP Rate Limiter
 * Maximum 5 inquiries per hour per IP
 */
function check_ip_rate_limit($max_per_hour = 5)
{
    $ip = get_client_ip();
    $cache_dir = __DIR__ . '/../cache';
    if (!is_dir($cache_dir)) {
        @mkdir($cache_dir, 0755, true);
    }

    $ip_hash = md5($ip);
    $rate_file = $cache_dir . '/rate_' . $ip_hash . '.json';
    $now = time();
    $window = 3600; // 1 hour

    $history = [];
    if (file_exists($rate_file)) {
        $content = @file_get_contents($rate_file);
        if ($content) {
            $data = json_decode($content, true);
            if (is_array($data)) {
                // Keep only timestamps within the last hour
                foreach ($data as $timestamp) {
                    if ($now - $timestamp < $window) {
                        $history[] = $timestamp;
                    }
                }
            }
        }
    }

    if (count($history) >= $max_per_hour) {
        return false; // Rate limit exceeded
    }

    $history[] = $now;
    @file_put_contents($rate_file, json_encode($history), LOCK_EX);
    return true;
}

/**
 * Comprehensive Anti-Spam Evaluation
 * Returns true if spam, false if clean
 *
 * @param array $data Input data ($_POST)
 * @param string|null &$reason Output reason for logging/debugging
 * @return bool True if SPAM, False if legitimate
 */
function is_spam_inquiry($data, &$reason = null)
{
    // 1. Honeypot check (invisible fields filled by bots)
    if (!empty($data['website_url']) || !empty($data['business_fax']) || !empty($data['company_website'])) {
        $reason = 'Honeypot trap triggered';
        return true;
    }

    // 2. Time-gate check (submission completed unnaturally fast or stale)
    $form_load_time = $_SESSION['form_load_time'] ?? null;
    if (!empty($data['form_time'])) {
        $decoded = base64_decode($data['form_time']);
        if ($decoded && strpos($decoded, ':') !== false) {
            list($t, $hash) = explode(':', $decoded, 2);
            $expected_hash = hash_hmac('sha256', (string)$t, 'aia_antispam_key_' . session_id());
            if (hash_equals($expected_hash, $hash)) {
                $form_load_time = (int)$t;
            }
        }
    }

    if ($form_load_time) {
        $elapsed = time() - $form_load_time;
        // Humans take at least 3 seconds to complete this form
        if ($elapsed < 3) {
            $reason = 'Submission too fast (' . $elapsed . 's) - bot activity';
            return true;
        }
        // Stale form (> 24 hours)
        if ($elapsed > 86400) {
            $reason = 'Form session expired';
            return true;
        }
    }

    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $phone = trim($data['phone'] ?? '');
    $message = trim($data['message'] ?? '');
    $combined_text = $name . ' ' . $message;

    // 3. Cyrillic / Russian character detection
    // Inspection agency operates in India (English/Hindi). All Russian Cyrillic messages are 100% spam.
    if (preg_match('/[\x{0400}-\x{04FF}]/u', $combined_text)) {
        $reason = 'Cyrillic / Russian characters detected';
        return true;
    }

    // 4. URL & Link Detection (Inspection inquiries do not need links)
    // Common URL patterns, protocols, and high-spam TLDs (.buzz, .ru, .xyz, etc.)
    $link_patterns = [
        '/(http:\/\/|https:\/\/|ftp:\/\/|www\.)/i',
        '/\b(peskartyhrt|sekalubanik|tinyurl|bit\.ly|t\.co|goo\.gl|is\.gd|ow\.ly|buff\.ly)\b/i',
        '/\b[a-zA-Z0-9-]+\.(buzz|ru|xyz|top|site|online|link|click|tk|ml|ga|cf|gq|club|icu|rest|beauty|best|monster|hair|live|shop|cfd|sbs)\b/i',
        '/\b[a-zA-Z0-9-]{3,}\.(com|net|org|biz|info)\/[a-zA-Z0-9_\-\?\#\=\&\.]+/i'
    ];

    foreach ($link_patterns as $pattern) {
        if (preg_match($pattern, $message) || preg_match($pattern, $name)) {
            $reason = 'Disallowed link or domain detected';
            return true;
        }
    }

    // 5. Spam / Phishing / Fraud keywords
    $spam_keywords = [
        // Russian spam terms (transliterated / common)
        'перевод', 'руб', 'бонус', 'выигрыш', 'приз', 'подарок', 'вознаграждение',
        'забрать тут', 'получить тут', 'откройте подробности', 'по ссылке',
        // Financial & crypto phishing
        'withdrawal operation', 'account will be blocked', 'crypto', 'bitcoin',
        'usdt', 'ethereum', 'wallet balance', 'earn money', 'passive income',
        'payout', 'claim your prize', 'lottery winner', 'jackpot', 'casino',
        'telegram:', 't.me/', 'whatsapp:', 'viagra', 'cialis', 'backlinks',
        'seo ranking', 'first page of google', 'guest post', 'AledyCeds'
    ];

    $combined_lower = mb_strtolower($combined_text, 'UTF-8');
    foreach ($spam_keywords as $keyword) {
        if (mb_stripos($combined_lower, $keyword, 0, 'UTF-8') !== false) {
            $reason = 'Spam keyword matched: ' . $keyword;
            return true;
        }
    }

    // 6. Blacklisted / Disposable email domains
    $blacklisted_domains = [
        'nolettersbox.com',
        'notboxletters.com',
        'mailinator.com',
        'tempmail.com',
        'guerrillamail.com',
        '10minutemail.com',
        'throwawaymail.com',
        'trashmail.com',
        'yopmail.com'
    ];

    if (strpos($email, '@') !== false) {
        $email_domain = strtolower(substr(strrchr($email, "@"), 1));
        if (in_array($email_domain, $blacklisted_domains)) {
            $reason = 'Blacklisted disposable email domain: ' . $email_domain;
            return true;
        }
    }

    // 7. Russian 10-digit phone number format (starts with 8) when combined with odd patterns
    // Russian phone numbers in the spam entries: 8462733127, 8531262831, 8993314339, etc.
    if (preg_match('/^8\d{9}$/', $phone)) {
        // If phone starts with 8, check if name looks like typical Russian bot pattern (e.g. single word like 'AledyCeds' or similar)
        if (str_word_count($name) <= 1 && strlen($message) < 20) {
            $reason = 'Suspicious phone format and bot profile';
            return true;
        }
    }

    // 8. IP Rate limiting
    if (!check_ip_rate_limit(5)) {
        $reason = 'Rate limit exceeded for IP: ' . get_client_ip();
        return true;
    }

    return false; // Clean inquiry
}
