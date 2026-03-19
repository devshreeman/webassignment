<?php
include('../config/db.php');

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Add AUTO_INCREMENT if missing
    $pdo->exec("ALTER TABLE staff MODIFY StaffID int(11) NOT NULL AUTO_INCREMENT");
    echo "SUCCESS: Added AUTO_INCREMENT to staff table.\n";
    
    $pdo->exec("ALTER TABLE modules MODIFY ModuleID int(11) NOT NULL AUTO_INCREMENT");
    echo "SUCCESS: Added AUTO_INCREMENT to modules table.\n";
    
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
} catch (Exception $e) {
    echo "GENERAL ERROR: " . $e->getMessage();
}
