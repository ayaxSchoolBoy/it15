-- Database: gym_qr
CREATE DATABASE IF NOT EXISTS gym_qr;
USE gym_qr;

-- Table: memberships
CREATE TABLE IF NOT EXISTS memberships (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  membership_code VARCHAR(50) NOT NULL UNIQUE,
  member_name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  plan VARCHAR(50) NOT NULL,
  expiration_date DATE NOT NULL,
  status ENUM('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
  qr_path VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: users
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('customer','admin') NOT NULL DEFAULT 'customer',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table: scan_logs (admin scan history)
CREATE TABLE IF NOT EXISTS scan_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  membership_code VARCHAR(50) NOT NULL,
  result_status ENUM('ACTIVE','EXPIRED','INVALID') NOT NULL,
  scanned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  ip_address VARCHAR(45) NOT NULL
);

-- Sample records
INSERT INTO memberships (user_id, membership_code, member_name, email, plan, expiration_date, status)
VALUES
('1', 'MEM-2026-0001', 'John Doe', 'john@example.com', 'Monthly', '2026-03-01', 'ACTIVE'),
('1', 'MEM-2026-0002', 'Jane Smith', 'jane@example.com', 'Annual', '2025-12-31', 'INACTIVE');
