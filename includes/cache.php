<?php
/**
 * Simple File-based Cache for Low Memory Servers
 * Accredited Inspection Agency
 */

class FileCache
{
    private $cache_dir;
    private $default_ttl = 300; // 5 minutes

    public function __construct($cache_dir = null)
    {
        $this->cache_dir = $cache_dir ?: __DIR__ . '/../cache';

        // Create cache directory if it doesn't exist
        if (!is_dir($this->cache_dir)) {
            mkdir($this->cache_dir, 0755, true);
        }
    }

    /**
     * Get cached data
     */
    public function get($key)
    {
        $file = $this->getCacheFilePath($key);

        if (!file_exists($file)) {
            return null;
        }

        $content = file_get_contents($file);
        $data = unserialize($content);

        // Check if expired
        if ($data['expires'] < time()) {
            unlink($file);
            return null;
        }

        return $data['value'];
    }

    /**
     * Set cached data
     */
    public function set($key, $value, $ttl = null)
    {
        $ttl = $ttl ?: $this->default_ttl;
        $file = $this->getCacheFilePath($key);

        $data = [
            'value' => $value,
            'expires' => time() + $ttl
        ];

        return file_put_contents($file, serialize($data), LOCK_EX) !== false;
    }

    /**
     * Delete cached data
     */
    public function delete($key)
    {
        $file = $this->getCacheFilePath($key);

        if (file_exists($file)) {
            return unlink($file);
        }

        return true;
    }

    /**
     * Clear all cache
     */
    public function clear()
    {
        $files = glob($this->cache_dir . '/cache_*.dat');

        foreach ($files as $file) {
            unlink($file);
        }

        return true;
    }

    /**
     * Get cache file path for a key
     */
    private function getCacheFilePath($key)
    {
        $safe_key = preg_replace('/[^a-zA-Z0-9_]/', '_', $key);
        return $this->cache_dir . '/cache_' . $safe_key . '.dat';
    }
}

// Global cache instance
$cache = new FileCache();

/**
 * Get cached services (5 min TTL)
 */
function get_cached_services($pdo, $active_only = true)
{
    global $cache;

    $cache_key = 'services_' . ($active_only ? 'active' : 'all');
    $cached = $cache->get($cache_key);

    if ($cached !== null) {
        return $cached;
    }

    // Fetch from database
    $services = get_services($pdo, $active_only);

    // Cache for 5 minutes
    $cache->set($cache_key, $services, 300);

    return $services;
}

/**
 * Get cached job posts (5 min TTL)
 */
function get_cached_job_posts($pdo, $active_only = true)
{
    global $cache;

    $cache_key = 'jobs_' . ($active_only ? 'active' : 'all');
    $cached = $cache->get($cache_key);

    if ($cached !== null) {
        return $cached;
    }

    // Fetch from database
    $jobs = get_job_posts($pdo, $active_only);

    // Cache for 5 minutes
    $cache->set($cache_key, $jobs, 300);

    return $jobs;
}

/**
 * Clear caches (call after admin updates)
 */
function clear_service_cache()
{
    global $cache;
    $cache->delete('services_active');
    $cache->delete('services_all');
}

function clear_job_cache()
{
    global $cache;
    $cache->delete('jobs_active');
    $cache->delete('jobs_all');
}

function clear_all_caches()
{
    global $cache;
    $cache->clear();
}
