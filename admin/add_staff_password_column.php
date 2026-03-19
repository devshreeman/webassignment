<?php
/**
 * Migration: Add password column to staff table
 * Run this once to add the missing password column
 */
include('../config/db.php');

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if password column exists
    $result = $pdo->query("SHOW COLUMNS FROM staff LIKE 'password'");
    
    if ($result->rowCount() == 0) {
        echo "Adding password column to staff table...<br>";
        $pdo->exec("ALTER TABLE staff ADD COLUMN password VARCHAR(255) DEFAULT NULL AFTER Photo");
        echo "✓ Password column added successfully.<br>";
    } else {
        echo "✓ Password column already exists.<br>";
    }
    
    echo "<br><a href='dashboard.php'>← Back to Dashboard</a>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
