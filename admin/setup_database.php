<?php
/**
 * Database Setup & Migration Script
 * Run this once after importing schema.sql to ensure all required data and columns exist
 */
include('../config/db.php');

$errors = [];
$success = [];

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Database Setup & Migration</h2>";
    echo "<hr>";
    
    // 1. Add password column to staff table if missing
    echo "<h3>1. Checking staff table structure...</h3>";
    $result = $pdo->query("SHOW COLUMNS FROM staff LIKE 'password'");
    if ($result->rowCount() == 0) {
        $pdo->exec("ALTER TABLE staff ADD COLUMN password VARCHAR(255) DEFAULT NULL AFTER Photo");
        $success[] = "✓ Added password column to staff table";
    } else {
        $success[] = "✓ Staff table already has password column";
    }
    
    // 2. Populate levels table if empty
    echo "<h3>2. Checking levels table...</h3>";
    $count = $pdo->query("SELECT COUNT(*) FROM levels")->fetchColumn();
    if ($count == 0) {
        $pdo->exec("INSERT INTO levels (LevelID, LevelName) VALUES (1, 'Undergraduate'), (2, 'Postgraduate')");
        $success[] = "✓ Populated levels table with Undergraduate and Postgraduate";
    } else {
        $success[] = "✓ Levels table already has $count record(s)";
    }
    
    // 3. Create contact_messages table if missing
    echo "<h3>3. Checking contact_messages table...</h3>";
    $tables = $pdo->query("SHOW TABLES LIKE 'contact_messages'")->fetchAll();
    if (count($tables) == 0) {
        $pdo->exec("CREATE TABLE contact_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(150) NOT NULL,
            subject VARCHAR(200) NOT NULL,
            message TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            is_read TINYINT(1) DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
        $success[] = "✓ Created contact_messages table";
    } else {
        $success[] = "✓ Contact_messages table already exists";
    }
    
    // 4. Verify foreign key constraints
    echo "<h3>4. Verifying database integrity...</h3>";
    
    // Check for orphaned records
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
    
    // 5. Check uploads directory
    echo "<h3>5. Checking file system...</h3>";
    $uploadDir = '../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
        $success[] = "✓ Created uploads directory";
    } else {
        $success[] = "✓ Uploads directory exists";
    }
    
    if (!is_writable($uploadDir)) {
        $errors[] = "⚠ Uploads directory is not writable. Please set permissions to 755 or 777";
    } else {
        $success[] = "✓ Uploads directory is writable";
    }
    
    // Display results
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
    
    if (empty($errors)) {
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
