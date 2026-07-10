-- Phase 1: DB Migration SQL for Payroll Management Module
-- Prefix: payroll_

-- 1. Employees Master
CREATE TABLE IF NOT EXISTS `payroll_employees` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `emp_code` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(100) NOT NULL,
    `designation` VARCHAR(100) NOT NULL,
    `location` VARCHAR(100) DEFAULT NULL,
    `doj` DATE DEFAULT NULL,
    `bank_name` VARCHAR(100) DEFAULT NULL,
    `bank_account` VARCHAR(50) DEFAULT NULL,
    `pan_no` VARCHAR(20) DEFAULT NULL,
    `pf_no` VARCHAR(50) DEFAULT NULL,
    `uan_no` VARCHAR(50) DEFAULT NULL,
    `esic_no` VARCHAR(50) DEFAULT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Salary Components Master
-- Models different types of earnings and deductions as dynamic rows.
CREATE TABLE IF NOT EXISTS `payroll_salary_components` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL, -- e.g., 'Basic', 'HRA', 'PF'
    `type` ENUM('earning', 'deduction') NOT NULL,
    `is_prorated` TINYINT(1) DEFAULT 1, -- 1 = reduces on LOP, 0 = fixed amount
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert some default common components
INSERT IGNORE INTO `payroll_salary_components` (`id`, `name`, `type`, `is_prorated`) VALUES
(1, 'Basic Salary', 'earning', 1),
(2, 'HRA', 'earning', 1),
(3, 'Conveyance Allowance', 'earning', 1),
(4, 'Medical Allowance', 'earning', 1),
(5, 'Special Allowance', 'earning', 1),
(6, 'PF Contribution', 'deduction', 1),
(7, 'ESIC Contribution', 'deduction', 1),
(8, 'Professional Tax', 'deduction', 0),
(9, 'TDS', 'deduction', 0);

-- 3. Employee Salary Structure
-- Per-employee standard entitlement per component
CREATE TABLE IF NOT EXISTS `payroll_employee_salary_structure` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `employee_id` INT NOT NULL,
    `component_id` INT NOT NULL,
    `standard_amount` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`employee_id`) REFERENCES `payroll_employees`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`component_id`) REFERENCES `payroll_salary_components`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_emp_comp` (`employee_id`, `component_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Payroll Batches
-- One batch per month
CREATE TABLE IF NOT EXISTS `payroll_batches` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `month` VARCHAR(7) NOT NULL UNIQUE, -- Format: YYYY-MM
    `standard_working_days` DECIMAL(4,1) NOT NULL,
    `status` ENUM('draft', 'generated', 'locked') DEFAULT 'draft',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Payslips
-- One per employee per batch
CREATE TABLE IF NOT EXISTS `payroll_payslips` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `batch_id` INT NOT NULL,
    `employee_id` INT NOT NULL,
    `work_days` DECIMAL(4,1) NOT NULL DEFAULT 0,
    `lop_days` DECIMAL(4,1) NOT NULL DEFAULT 0,
    `total_earnings` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `total_deductions` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `net_pay` DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    `pdf_path` VARCHAR(255) DEFAULT NULL,
    `email_status` ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`batch_id`) REFERENCES `payroll_batches`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`employee_id`) REFERENCES `payroll_employees`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_batch_emp` (`batch_id`, `employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Payslip Items
-- Line-item Standard vs. Actual amount per component per payslip
CREATE TABLE IF NOT EXISTS `payroll_payslip_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `payslip_id` INT NOT NULL,
    `component_id` INT NOT NULL,
    `standard_amount` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    `actual_amount` DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (`payslip_id`) REFERENCES `payroll_payslips`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`component_id`) REFERENCES `payroll_salary_components`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Upload Logs (Audit)
CREATE TABLE IF NOT EXISTS `payroll_upload_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `batch_id` INT DEFAULT NULL,
    `uploaded_by` INT DEFAULT NULL,
    `log_message` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`batch_id`) REFERENCES `payroll_batches`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
