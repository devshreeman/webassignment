-- Migration Script to Update Database Schema
-- This adds all necessary columns and tables for the Staff & Student Portal Enhancement
-- Run this AFTER importing example-data.sql

USE student_course_hub;

-- 0. Fix auto-increment for Staff and Modules tables (critical fix)
ALTER TABLE Staff MODIFY StaffID INT AUTO_INCREMENT;
ALTER TABLE Modules MODIFY ModuleID INT AUTO_INCREMENT;

-- 0.1 Fix column types for MySQL compatibility
ALTER TABLE Staff MODIFY Name VARCHAR(100) NOT NULL;
ALTER TABLE Modules MODIFY ModuleName VARCHAR(200) NOT NULL;
ALTER TABLE Modules MODIFY Description TEXT;
ALTER TABLE Modules MODIFY Image VARCHAR(255);
ALTER TABLE Programmes MODIFY ProgrammeName VARCHAR(200) NOT NULL;
ALTER TABLE Programmes MODIFY Description TEXT;
ALTER TABLE Programmes MODIFY Image VARCHAR(255);
ALTER TABLE Levels MODIFY LevelName VARCHAR(50) NOT NULL;

-- 1. Add missing columns to Staff table
ALTER TABLE Staff 
ADD COLUMN Email VARCHAR(150) DEFAULT NULL AFTER Name,
ADD COLUMN Phone VARCHAR(20) DEFAULT NULL AFTER Email,
ADD COLUMN Bio TEXT DEFAULT NULL AFTER Phone,
ADD COLUMN Photo VARCHAR(255) DEFAULT NULL AFTER Bio,
ADD COLUMN password VARCHAR(255) DEFAULT NULL AFTER Photo;

-- Update staff with default email addresses (you can update these later)
UPDATE Staff SET Email = CONCAT(LOWER(REPLACE(Name, ' ', '.')), '@liverpool.ac.uk') WHERE Email IS NULL;
UPDATE Staff SET Email = REPLACE(Email, 'dr.', '') WHERE Email LIKE 'dr.%';

-- 2. Add IsPublished column to Programmes table
ALTER TABLE Programmes 
ADD COLUMN IsPublished TINYINT(1) DEFAULT 1 AFTER Image;

-- 2.1 Add Duration column to Programmes table
ALTER TABLE Programmes 
ADD COLUMN Duration INT DEFAULT 3 AFTER Image;

-- 3. Create students table
CREATE TABLE IF NOT EXISTS students (
    StudentID INT AUTO_INCREMENT PRIMARY KEY,
    FullName VARCHAR(100) NOT NULL,
    Email VARCHAR(150) NOT NULL UNIQUE,
    Phone VARCHAR(20) DEFAULT NULL,
    Password VARCHAR(255) NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (Email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. Add StudentID column to InterestedStudents table
ALTER TABLE InterestedStudents 
ADD COLUMN StudentID INT DEFAULT NULL AFTER InterestID;

-- 5. Add foreign key constraint for StudentID
ALTER TABLE InterestedStudents 
ADD CONSTRAINT fk_interested_student 
FOREIGN KEY (StudentID) REFERENCES students(StudentID) ON DELETE SET NULL;

-- 6. Add index on StudentID for better query performance
CREATE INDEX idx_student_id ON InterestedStudents(StudentID);

-- 7. Create admin table if it doesn't exist
CREATE TABLE IF NOT EXISTS admin (
    AdminID INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default admin account (password: admin123)
-- You should change this password after first login
INSERT INTO admin (name, email, password) VALUES 
('Admin User', 'admin@liverpool.ac.uk', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')
ON DUPLICATE KEY UPDATE name=name;

-- 8. Create contact_messages table if it doesn't exist
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_read TINYINT(1) DEFAULT 0,
    INDEX idx_created (created_at),
    INDEX idx_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Verify the changes
SELECT 'Migration completed successfully!' as Status;

-- Show updated table structures
SHOW COLUMNS FROM Staff;
SHOW COLUMNS FROM Programmes;
SHOW COLUMNS FROM InterestedStudents;
SHOW TABLES;
