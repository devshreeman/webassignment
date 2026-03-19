<?php
/**
 * Migration: Populate levels table with default values
 * Run this once to add Undergraduate and Postgraduate levels
 */
include('../config/db.php');

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if levels already exist
    $count = $pdo->query("SELECT COUNT(*) FROM levels")->fetchColumn();
    
    if ($count == 0) {
        echo "Populating levels table...<br>";
        
        $pdo->exec("INSERT INTO levels (LevelID, LevelName) VALUES (1, 'Undergraduate')");
        $pdo->exec("INSERT INTO levels (LevelID, LevelName) VALUES (2, 'Postgraduate')");
        
        echo "✓ Levels table populated successfully.<br>";
        echo "  - Undergraduate (ID: 1)<br>";
        echo "  - Postgraduate (ID: 2)<br>";
    } else {
        echo "✓ Levels table already has $count record(s).<br>";
        
        // Display existing levels
        $levels = $pdo->query("SELECT * FROM levels ORDER BY LevelID")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($levels as $level) {
            echo "  - {$level['LevelName']} (ID: {$level['LevelID']})<br>";
        }
    }
    
    echo "<br><a href='dashboard.php'>← Back to Dashboard</a>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
