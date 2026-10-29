CREATE DATABASE IF NOT EXISTS `equipment_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `equipment_db`;

-- ตารางผู้ใช้งาน
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `role` ENUM('Student', 'Teacher', 'Admin') NOT NULL DEFAULT 'Student',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ตารางอุปกรณ์
CREATE TABLE IF NOT EXISTS `equipment` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `name` VARCHAR(150) NOT NULL,
  `type` ENUM('IT', 'Lab') NOT NULL,
  `status` ENUM('Available', 'Borrowed', 'Maintenance') NOT NULL DEFAULT 'Available',
  `daily_penalty_rate` DECIMAL(8,2) NOT NULL DEFAULT 50.00,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ตารางการยืม-คืน
CREATE TABLE IF NOT EXISTS `loans` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `equipment_id` INT NOT NULL,
  `borrow_date` DATE NOT NULL,
  `due_date` DATE NOT NULL,
  `return_date` DATE NULL,
  `penalty_fee` DECIMAL(8,2) DEFAULT 0.00,
  `status` ENUM('Active', 'Returned', 'Overdue') NOT NULL DEFAULT 'Active',
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`equipment_id`) REFERENCES `equipment`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ข้อมูลตัวอย่าง (Sample Data)
INSERT INTO `users` (`name`, `email`, `role`) VALUES
('สมชาย สายเรียน (นักศึกษา)', 'somchai@student.ac.th', 'Student'),
('ดร.วิชัย สอนดี (อาจารย์)', 'wichai@teacher.ac.th', 'Teacher'),
('ผู้ดูแลระบบ (Admin)', 'admin@lab.ac.th', 'Admin');

INSERT INTO `equipment` (`code`, `name`, `type`, `status`, `daily_penalty_rate`) VALUES
('LAP-001', 'MacBook Pro 16"', 'IT', 'Available', 100.00),
('CAM-002', 'Sony Alpha A7 IV', 'IT', 'Available', 80.00),
('MIC-001', 'Microscope Digital 4K', 'Lab', 'Available', 150.00),
('OSC-002', 'Digital Oscilloscope', 'Lab', 'Available', 120.00);
