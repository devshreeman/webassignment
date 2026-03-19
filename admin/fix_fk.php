<?php
include('../config/db.php');

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Disable FK checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

    // Add AUTO_INCREMENT
    $pdo->exec("ALTER TABLE staff MODIFY StaffID int(11) NOT NULL AUTO_INCREMENT;");
    echo "SUCCESS: Added AUTO_INCREMENT to staff table.|";
    
    $pdo->exec("ALTER TABLE modules MODIFY ModuleID int(11) NOT NULL AUTO_INCREMENT;");
    echo "SUCCESS: Added AUTO_INCREMENT to modules table.|";

    // Enable FK checks
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
} catch (Exception $e) {
    echo "GENERAL ERROR: " . $e->getMessage();
}
