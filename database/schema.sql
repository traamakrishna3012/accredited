-- Accredited Inspection Agency Database Schema
-- Database: agency_db
-- Optimized for MariaDB 10.6+

CREATE DATABASE IF NOT EXISTS agency_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE agency_db;

-- Services Table
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    icon VARCHAR(100) DEFAULT 'bi-gear',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inquiries Table
CREATE TABLE IF NOT EXISTS inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    service_id INT NULL,
    message TEXT,
    status ENUM('new','read','responded') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    INDEX idx_service_id (service_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Admin Users Table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Job Posts Table
CREATE TABLE IF NOT EXISTS job_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    department VARCHAR(100),
    location VARCHAR(100),
    type ENUM('full-time', 'part-time', 'contract', 'internship') DEFAULT 'full-time',
    experience VARCHAR(100),
    salary_range VARCHAR(100),
    description TEXT,
    requirements TEXT,
    responsibilities TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_is_active (is_active),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert Default Services
INSERT INTO services (title, description, icon) VALUES
('Sampling', 'We carry out precise sampling and joint sample preparation as per Indian Standards (IS) or International Standards (ISO, ASTM, etc). As per client requirement from on-site sampling of mines, plants & ports as well as in-house laboratory testing. We are dedicated to mitigating risks associated with the movement of commodities through all modes of transport or stacks, ensuring utmost reliability and accuracy at every stage.', 'bi-eyedropper'),
('Inspection', 'We are committed to delivering our best with professionals for Pre-Shipment Assessment, Loading/Unloading Supervision, and Physical Quality Monitoring during Inspection. Our services include Weighment Assessment, Vessel, Rake, Trucks and Stack Assessment.', 'bi-search'),
('Testing', 'We undertake analysis with our testing partners having NABL ISO/IEC 17025:2017 Testing Laboratories of Coal, Coke, Minerals, Ores, Fertilizers, Oil Cakes/Extractions, Metals, Agricultural Products, Environmental Testing, etc. Our team consists of well-qualified, experienced Technical and Professional persons including Mining Engineers, Scientists, and Chemists.', 'bi-clipboard-check');

-- Insert Default Admin User (password: admin123)
-- Password hash generated with password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO admin_users (username, password, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@accredited.co.in');
