<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('../config/db.php');

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Dropping ProgrammeID column from modules...<br>";
    $pdo->exec("ALTER TABLE modules DROP COLUMN ProgrammeID");
    echo "Done.<br>";

    echo "Dropping redundant StaffID column from modules...<br>";
    $pdo->exec("ALTER TABLE modules DROP COLUMN StaffID");
    echo "Done.<br>";

} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'check that column/key exists') !== false) {
        echo "Note: Columns already modified/exist. Moving on...<br>" . $e->getMessage();
    } else {
        echo "ERROR: " . $e->getMessage();
    }
}
?>
