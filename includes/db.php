<?php
/**
 * Database Connection using PDO
 * Optimized for MariaDB on low-memory servers
 */

require_once __DIR__ . '/config.php';

// Enable output buffering for faster response
if (!ob_get_level()) {
    ob_start('ob_gzhandler');
}

try {
    $pdo_options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        // Persistent connections for connection pooling
        PDO::ATTR_PERSISTENT => IS_PRODUCTION ? true : false,
        // Smaller buffer for memory efficiency
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
    ];

    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        $pdo_options
    );

    // Set session variables for MariaDB optimization
    if (IS_PRODUCTION) {
        $pdo->exec("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO'");
    }

} catch (PDOException $e) {
    // Log error in production, show message in development
    if (IS_PRODUCTION) {
        error_log("Database Connection Failed: " . $e->getMessage());
        die("A database error occurred. Please try again later.");
    } else {
        die("Database Connection Failed: " . $e->getMessage());
    }
}

/**
 * Get all active services
 */
function get_services($pdo, $active_only = true)
{
    $sql = "SELECT * FROM services";
    if ($active_only) {
        $sql .= " WHERE is_active = 1";
    }
    $sql .= " ORDER BY id ASC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

/**
 * Get single service by ID
 */
function get_service($pdo, $id)
{
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Save inquiry to database
 */
function save_inquiry($pdo, $data)
{
    $stmt = $pdo->prepare("
        INSERT INTO inquiries (name, email, phone, service_id, message, status, created_at)
        VALUES (?, ?, ?, ?, ?, 'new', NOW())
    ");
    return $stmt->execute([
        $data['name'],
        $data['email'],
        $data['phone'],
        $data['service_id'] ?: null,
        $data['message']
    ]);
}

/**
 * Get all inquiries
 */
function get_inquiries($pdo, $limit = null, $offset = null, $status = '')
{
    $sql = "SELECT i.*, s.title as service_name 
            FROM inquiries i 
            LEFT JOIN services s ON i.service_id = s.id ";
            
    $params = [];
    if (!empty($status) && in_array($status, ['new', 'read', 'responded'])) {
        $sql .= " WHERE i.status = ? ";
        $params[] = $status;
    }

    $sql .= " ORDER BY i.created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT " . (int) $limit;
        if ($offset !== null) {
            $sql .= " OFFSET " . (int) $offset;
        }
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Get total number of inquiries
 */
function get_total_inquiries($pdo, $status = '')
{
    $sql = "SELECT COUNT(*) FROM inquiries";
    $params = [];
    if (!empty($status) && in_array($status, ['new', 'read', 'responded'])) {
        $sql .= " WHERE status = ? ";
        $params[] = $status;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn();
}

/**
 * Update inquiry status
 */
function update_inquiry_status($pdo, $id, $status)
{
    $stmt = $pdo->prepare("UPDATE inquiries SET status = ? WHERE id = ?");
    return $stmt->execute([$status, $id]);
}

/**
 * Get inquiry counts by status
 */
function get_inquiry_counts($pdo)
{
    $stmt = $pdo->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_count,
            SUM(CASE WHEN status = 'read' THEN 1 ELSE 0 END) as read_count,
            SUM(CASE WHEN status = 'responded' THEN 1 ELSE 0 END) as responded_count
        FROM inquiries
    ");
    return $stmt->fetch();
}

/**
 * Get SQL WHERE clause for spam detection
 */
function get_spam_where_clause()
{
    return "
        name LIKE '%http%' 
        OR message LIKE '%http%'
        OR name LIKE '%www.%'
        OR message LIKE '%www.%'
        OR name LIKE '%.buzz%'
        OR message LIKE '%.buzz%'
        OR name LIKE '%tinyurl%'
        OR message LIKE '%tinyurl%'
        OR name LIKE '%graph.org%'
        OR message LIKE '%graph.org%'
        OR name LIKE '%telegra.ph%'
        OR message LIKE '%telegra.ph%'
        OR name LIKE '%peskartyhrt%'
        OR message LIKE '%peskartyhrt%'
        OR name LIKE '%sekalubanik%'
        OR message LIKE '%sekalubanik%'
        OR name LIKE '%BTC%'
        OR message LIKE '%BTC%'
        OR name LIKE '%Coinbase%'
        OR message LIKE '%Coinbase%'
        OR name LIKE '%Transfer №%'
        OR message LIKE '%Transfer №%'
        OR name LIKE '%withdrawal%'
        OR message LIKE '%withdrawal%'
        OR name = 'RobertGip'
        OR name LIKE '%AledyCeds%'
        OR message LIKE '%AledyCeds%'
        OR name LIKE '%руб%'
        OR message LIKE '%руб%'
        OR name LIKE '%перевод%'
        OR message LIKE '%перевод%'
        OR name LIKE '%бонус%'
        OR message LIKE '%бонус%'
        OR name LIKE '%приз%'
        OR message LIKE '%приз%'
        OR name LIKE '%подарок%'
        OR message LIKE '%подарок%'
        OR name LIKE '%вознаграждение%'
        OR message LIKE '%вознаграждение%'
        OR email LIKE '%@notboxletters.com'
        OR email LIKE '%@nolettersbox.com'
        OR email LIKE '%@notlettersmail.com'
        OR email LIKE '%@mailinator.com'
        OR message LIKE '%Egjnjmfnefjwdifj%'
        OR message LIKE '%YyErjcwdkdjwjjwjjdwjddjwsjf%'
        OR name LIKE '%NARETGR%'
        OR name LIKE '%Nikjhkjhk%'
    ";
}

/**
 * Count spam inquiries matching bot patterns
 */
function count_spam_inquiries($pdo)
{
    $where = get_spam_where_clause();

    try {
        $sql = "SELECT COUNT(*) FROM inquiries WHERE (" . $where . ") 
            OR message REGEXP '[\\x{0400}-\\x{04FF}]' 
            OR name REGEXP '[\\x{0400}-\\x{04FF}]'";
        $stmt = $pdo->query($sql);
        return (int) $stmt->fetchColumn();
    } catch (Exception $e) {
        $sql = "SELECT COUNT(*) FROM inquiries WHERE " . $where;
        $stmt = $pdo->query($sql);
        return (int) $stmt->fetchColumn();
    }
}

/**
 * Delete spam inquiries in bulk
 */
function delete_spam_inquiries($pdo)
{
    $where = get_spam_where_clause();

    try {
        $sql = "DELETE FROM inquiries WHERE (" . $where . ") 
            OR message REGEXP '[\\x{0400}-\\x{04FF}]' 
            OR name REGEXP '[\\x{0400}-\\x{04FF}]'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount();
    } catch (Exception $e) {
        $sql = "DELETE FROM inquiries WHERE " . $where;
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount();
    }
}

function authenticate_admin($pdo, $username, $password)
{
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}

/**
 * Get all job posts
 */
function get_job_posts($pdo, $active_only = true)
{
    $sql = "SELECT * FROM job_posts";
    if ($active_only) {
        $sql .= " WHERE is_active = 1";
    }
    $sql .= " ORDER BY created_at DESC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

/**
 * Get single job post by ID
 */
function get_job_post($pdo, $id)
{
    $stmt = $pdo->prepare("SELECT * FROM job_posts WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Save job post
 */
function save_job_post($pdo, $data)
{
    $stmt = $pdo->prepare("
        INSERT INTO job_posts (title, department, location, type, experience, salary_range, description, requirements, responsibilities)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    return $stmt->execute([
        $data['title'],
        $data['department'],
        $data['location'],
        $data['type'],
        $data['experience'],
        $data['salary_range'],
        $data['description'],
        $data['requirements'],
        $data['responsibilities']
    ]);
}

/**
 * Update job post
 */
function update_job_post($pdo, $id, $data)
{
    $stmt = $pdo->prepare("
        UPDATE job_posts SET 
            title = ?, department = ?, location = ?, type = ?, experience = ?, 
            salary_range = ?, description = ?, requirements = ?, responsibilities = ?, is_active = ?
        WHERE id = ?
    ");
    return $stmt->execute([
        $data['title'],
        $data['department'],
        $data['location'],
        $data['type'],
        $data['experience'],
        $data['salary_range'],
        $data['description'],
        $data['requirements'],
        $data['responsibilities'],
        $data['is_active'],
        $id
    ]);
}

/**
 * Delete job post
 */
function delete_job_post($pdo, $id)
{
    $stmt = $pdo->prepare("DELETE FROM job_posts WHERE id = ?");
    return $stmt->execute([$id]);
}

/**
 * Get job posts count
 */
function get_job_posts_count($pdo)
{
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM job_posts WHERE is_active = 1");
    return $stmt->fetch()['count'];
}

