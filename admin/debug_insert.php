<?php
include('../config/db.php');

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare("INSERT INTO staff (Name, Email, Bio, Photo, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['Test Name Debug', 'test-debug@test.com', 'Bio', '', '']);
    echo "SUCCESS: Inserted test staff member.";
    
    // Clean up
    $pdo->prepare("DELETE FROM staff WHERE Email = 'test-debug@test.com'")->execute();
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
} catch (Exception $e) {
    echo "GENERAL ERROR: " . $e->getMessage();
}
