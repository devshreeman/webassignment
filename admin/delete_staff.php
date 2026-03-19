<?php
session_start();
include('../config/db.php');

// --- Restrict access to admins only ---
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// --- Validate the staff ID ---
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_staff.php?msg=Invalid+staff+ID");
    exit;
}

$staffId = (int) $_GET['id'];

// --- Check if staff exists ---
$stmt = $pdo->prepare("SELECT * FROM staff WHERE StaffID = ?");
$stmt->execute([$staffId]);
$staff = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$staff) {
    header("Location: manage_staff.php?msg=Staff+not+found");
    exit;
}

try {
    // --- Check if staff is referenced in programmes or modules ---
    $progCount = $pdo->prepare("SELECT COUNT(*) FROM programmes WHERE ProgrammeLeaderID = ?");
    $progCount->execute([$staffId]);
    $progRefs = $progCount->fetchColumn();
    
    $modCount = $pdo->prepare("SELECT COUNT(*) FROM modules WHERE ModuleLeaderID = ?");
    $modCount->execute([$staffId]);
    $modRefs = $modCount->fetchColumn();
    
    if ($progRefs > 0 || $modRefs > 0) {
        header("Location: manage_staff.php?msg=Cannot+delete+staff+member+who+is+assigned+as+programme+or+module+leader");
        exit;
    }
    
    // --- Delete the staff record ---
    $pdo->prepare("DELETE FROM staff WHERE StaffID = ?")->execute([$staffId]);
    
    header("Location: manage_staff.php?msg=Staff+deleted+successfully");
} catch (PDOException $e) {
    header("Location: manage_staff.php?msg=Error+deleting+staff");
}
exit;
?>
