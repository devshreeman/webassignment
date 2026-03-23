<?php
/**
 * Database Setup & Migration Script
 * Run this after importing example-data.sql to add all required columns and tables
 */
include('../config/db.php');

$errors = [];
$success = [];

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Database Setup & Migration</h2>";
    echo "<hr>";
    
    echo "<h3>0. Fixing auto-increment for Staff and Modules tables...</h3>";
    try {
        // Get current max IDs
        $maxStaffId = $pdo->query("SELECT MAX(StaffID) FROM staff")->fetchColumn();
        $maxModuleId = $pdo->query("SELECT MAX(ModuleID) FROM modules")->fetchColumn();
        
        // Set next auto-increment values
        $nextStaffId = ($maxStaffId ? $maxStaffId + 1 : 1);
        $nextModuleId = ($maxModuleId ? $maxModuleId + 1 : 1);
        
        // Disable foreign key checks temporarily
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        
        // Fix Staff table
        $pdo->exec("ALTER TABLE staff MODIFY StaffID INT NOT NULL AUTO_INCREMENT");
        $pdo->exec("ALTER TABLE staff AUTO_INCREMENT = $nextStaffId");
        $success[] = "✓ Fixed auto-increment for staff table (next ID: $nextStaffId)";
        
        // Fix Modules table
        $pdo->exec("ALTER TABLE modules MODIFY ModuleID INT NOT NULL AUTO_INCREMENT");
        $pdo->exec("ALTER TABLE modules AUTO_INCREMENT = $nextModuleId");
        $success[] = "✓ Fixed auto-increment for modules table (next ID: $nextModuleId)";
        
        // Re-enable foreign key checks
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        
    } catch (PDOException $e) {
        // Re-enable foreign key checks even if there's an error
        try {
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        } catch (PDOException $e2) {}
        
        $errors[] = "⚠ Could not fix auto-increment: " . $e->getMessage();
    }
    
    echo "<h3>0.1. Fixing column types for MySQL compatibility...</h3>";
    try {
        $pdo->exec("ALTER TABLE staff MODIFY Name VARCHAR(100) NOT NULL");
        $pdo->exec("ALTER TABLE modules MODIFY ModuleName VARCHAR(200) NOT NULL");
        $pdo->exec("ALTER TABLE modules MODIFY Description TEXT");
        $pdo->exec("ALTER TABLE modules MODIFY Image VARCHAR(255)");
        $pdo->exec("ALTER TABLE programmes MODIFY ProgrammeName VARCHAR(200) NOT NULL");
        $pdo->exec("ALTER TABLE programmes MODIFY Description TEXT");
        $pdo->exec("ALTER TABLE programmes MODIFY Image VARCHAR(255)");
        $pdo->exec("ALTER TABLE levels MODIFY LevelName VARCHAR(50) NOT NULL");
        $success[] = "✓ Fixed column types (TEXT to VARCHAR where appropriate)";
    } catch (PDOException $e) {
        $success[] = "✓ Column types already correct or partially updated";
    }
    
    echo "<h3>1. Checking staff table structure...</h3>";
    
    $result = $pdo->query("SHOW COLUMNS FROM staff LIKE 'Email'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE staff ADD COLUMN Email VARCHAR(150) DEFAULT NULL AFTER Name");
        $success[] = "✓ Added Email column to staff table";
        
        $pdo->exec("UPDATE staff SET Email = CONCAT(LOWER(REPLACE(Name, ' ', '.')), '@liverpool.ac.uk') WHERE Email IS NULL");
        $pdo->exec("UPDATE staff SET Email = REPLACE(Email, 'dr.', '') WHERE Email LIKE 'dr.%'");
        $pdo->exec("UPDATE staff SET Email = REPLACE(Email, '.@', '@') WHERE Email LIKE '.%'");
        $success[] = "✓ Generated default email addresses for staff";
    } else {
        $success[] = "✓ Staff table already has Email column";
        
        $pdo->exec("UPDATE staff SET Email = REPLACE(Email, '.@', '@') WHERE Email LIKE '.%@'");
        $success[] = "✓ Cleaned up staff emails starting with dot";
    }
    
    $result = $pdo->query("SHOW COLUMNS FROM staff LIKE 'Phone'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE staff ADD COLUMN Phone VARCHAR(20) DEFAULT NULL AFTER Email");
        $success[] = "✓ Added Phone column to staff table";
    } else {
        $success[] = "✓ Staff table already has Phone column";
    }
    
    $result = $pdo->query("SHOW COLUMNS FROM staff LIKE 'Bio'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE staff ADD COLUMN Bio TEXT DEFAULT NULL AFTER Phone");
        $success[] = "✓ Added Bio column to staff table";
    } else {
        $success[] = "✓ Staff table already has Bio column";
    }
    
    $result = $pdo->query("SHOW COLUMNS FROM staff LIKE 'Photo'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE staff ADD COLUMN Photo VARCHAR(255) DEFAULT NULL AFTER Bio");
        $success[] = "✓ Added Photo column to staff table";
    } else {
        $success[] = "✓ Staff table already has Photo column";
    }
    
    $result = $pdo->query("SHOW COLUMNS FROM staff LIKE 'password'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE staff ADD COLUMN password VARCHAR(255) DEFAULT NULL AFTER Photo");
        $success[] = "✓ Added password column to staff table";
    } else {
        $success[] = "✓ Staff table already has password column";
    }
    
    echo "<h3>2. Checking programmes table structure...</h3>";
    $result = $pdo->query("SHOW COLUMNS FROM programmes LIKE 'IsPublished'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE programmes ADD COLUMN IsPublished TINYINT(1) DEFAULT 1 AFTER Image");
        $success[] = "✓ Added IsPublished column to programmes table";
    } else {
        $success[] = "✓ Programmes table already has IsPublished column";
    }
    
    $result = $pdo->query("SHOW COLUMNS FROM programmes LIKE 'Duration'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE programmes ADD COLUMN Duration INT DEFAULT 3 AFTER Image");
        $success[] = "✓ Added Duration column to programmes table";
    } else {
        $success[] = "✓ Programmes table already has Duration column";
    }
    
    echo "<h3>3. Creating students table...</h3>";
    $tables = $pdo->query("SHOW TABLES LIKE 'students'")->fetchAll();
    if (count($tables) == 0) {
        $pdo->exec("CREATE TABLE students (
            StudentID INT AUTO_INCREMENT PRIMARY KEY,
            FullName VARCHAR(100) NOT NULL,
            Email VARCHAR(150) NOT NULL UNIQUE,
            Phone VARCHAR(20) DEFAULT NULL,
            Password VARCHAR(255) NOT NULL,
            CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (Email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
        $success[] = "✓ Created students table";
    } else {
        $success[] = "✓ Students table already exists";
    }
    
    echo "<h3>4. Updating interestedstudents table...</h3>";
    $result = $pdo->query("SHOW COLUMNS FROM interestedstudents LIKE 'StudentID'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE interestedstudents ADD COLUMN StudentID INT DEFAULT NULL AFTER InterestID");
        $success[] = "✓ Added StudentID column to interestedstudents table";
        
        try {
            $pdo->exec("ALTER TABLE interestedstudents ADD CONSTRAINT fk_interested_student 
                        FOREIGN KEY (StudentID) REFERENCES students(StudentID) ON DELETE SET NULL");
            $success[] = "✓ Added foreign key constraint on StudentID";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate key') === false) {
                $errors[] = "⚠ Could not add foreign key: " . $e->getMessage();
            } else {
                $success[] = "✓ Foreign key constraint already exists";
            }
        }
        
        try {
            $pdo->exec("CREATE INDEX idx_student_id ON interestedstudents(StudentID)");
            $success[] = "✓ Added index on StudentID";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate key') === false) {
                $errors[] = "⚠ Could not add index: " . $e->getMessage();
            } else {
                $success[] = "✓ Index on StudentID already exists";
            }
        }
    } else {
        $success[] = "✓ InterestedStudents table already has StudentID column";
    }
    
    echo "<h3>5. Checking admin table...</h3>";
    $tables = $pdo->query("SHOW TABLES LIKE 'admin'")->fetchAll();
    if (count($tables) == 0) {
        $pdo->exec("CREATE TABLE admin (
            AdminID INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
        $success[] = "✓ Created admin table";
        
        $defaultPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO admin (name, email, password) VALUES 
                    ('Admin User', 'admin@liverpool.ac.uk', '$defaultPassword')");
        $success[] = "✓ Created default admin account (email: admin@liverpool.ac.uk, password: admin123)";
        $errors[] = "⚠ IMPORTANT: Change the default admin password after first login!";
    } else {
        $success[] = "✓ Admin table already exists";
    }
    
    echo "<h3>6. Checking contact_messages table...</h3>";
    $tables = $pdo->query("SHOW TABLES LIKE 'contact_messages'")->fetchAll();
    if (count($tables) == 0) {
        $pdo->exec("CREATE TABLE contact_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL,
            subject VARCHAR(200) NOT NULL,
            message TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            is_read TINYINT(1) DEFAULT 0,
            INDEX idx_created (created_at),
            INDEX idx_read (is_read)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
        $success[] = "✓ Created contact_messages table";
    } else {
        $success[] = "✓ Contact_messages table already exists";
    }
    
    echo "<h3>7. Checking levels table...</h3>";
    $count = $pdo->query("SELECT COUNT(*) FROM levels")->fetchColumn();
    if ($count == 0) {
        $pdo->exec("INSERT INTO levels (LevelID, LevelName) VALUES (1, 'Undergraduate'), (2, 'Postgraduate')");
        $success[] = "✓ Populated levels table with Undergraduate and Postgraduate";
    } else {
        $success[] = "✓ Levels table already has $count record(s)";
    }
    
    echo "<h3>8. Verifying database integrity...</h3>";
    
    $orphanedModules = $pdo->query("
        SELECT COUNT(*) FROM modules m 
        WHERE m.ModuleLeaderID IS NOT NULL 
        AND m.ModuleLeaderID NOT IN (SELECT StaffID FROM staff)
    ")->fetchColumn();
    
    if ($orphanedModules > 0) {
        $errors[] = "⚠ Found $orphanedModules module(s) with invalid ModuleLeaderID";
    } else {
        $success[] = "✓ All modules have valid leaders";
    }
    
    $orphanedProgrammes = $pdo->query("
        SELECT COUNT(*) FROM programmes p 
        WHERE p.ProgrammeLeaderID IS NOT NULL 
        AND p.ProgrammeLeaderID NOT IN (SELECT StaffID FROM staff)
    ")->fetchColumn();
    
    if ($orphanedProgrammes > 0) {
        $errors[] = "⚠ Found $orphanedProgrammes programme(s) with invalid ProgrammeLeaderID";
    } else {
        $success[] = "✓ All programmes have valid leaders";
    }
    
    echo "<h3>9. Checking file system...</h3>";
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
        $success[] = "✓ Created uploads directory";
    } else {
        $success[] = "✓ Uploads directory exists";
    }
    
    $staffUploadDir = '../uploads/staff/';
    if (!is_dir($staffUploadDir)) {
        mkdir($staffUploadDir, 0755, true);
        $success[] = "✓ Created uploads/staff directory";
    } else {
        $success[] = "✓ Uploads/staff directory exists";
    }
    
    if (!is_writable($uploadDir)) {
        $errors[] = "⚠ Uploads directory is not writable. Please set permissions to 755 or 777";
    } else {
        $success[] = "✓ Uploads directory is writable";
    }
    
    if (is_dir($staffUploadDir) && !is_writable($staffUploadDir)) {
        $errors[] = "⚠ Uploads/staff directory is not writable. Please set permissions to 755 or 777";
    } elseif (is_dir($staffUploadDir)) {
        $success[] = "✓ Uploads/staff directory is writable";
    }
    
    echo "<hr>";
    echo "<h3>Setup Results:</h3>";
    
    if (!empty($success)) {
        echo "<div style='background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:1rem;margin:1rem 0;border-radius:4px;'>";
        foreach ($success as $msg) {
            echo $msg . "<br>";
        }
        echo "</div>";
    }
    
    if (!empty($errors)) {
        echo "<div style='background:#f8d7da;border:1px solid #f5c6cb;color:#721c24;padding:1rem;margin:1rem 0;border-radius:4px;'>";
        foreach ($errors as $msg) {
            echo $msg . "<br>";
        }
        echo "</div>";
    }
    
    if (empty($errors) || count($errors) == 1) {
        echo "<div style='background:#d1ecf1;border:1px solid #bee5eb;color:#0c5460;padding:1rem;margin:1rem 0;border-radius:4px;'>";
        echo "<strong>✓ Database setup complete!</strong> Your application is ready to use.";
        echo "</div>";
    }
    
    echo "<br><a href='dashboard.php' style='display:inline-block;background:#0d3a6e;color:#fff;padding:0.5rem 1rem;text-decoration:none;border-radius:4px;'>← Back to Dashboard</a>";
    
} catch (PDOException $e) {
    echo "<div style='background:#f8d7da;border:1px solid #f5c6cb;color:#721c24;padding:1rem;margin:1rem 0;border-radius:4px;'>";
    echo "<strong>Error:</strong> " . $e->getMessage();
    echo "</div>";
}
?>
