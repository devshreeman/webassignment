<?php
session_start();
include('../config/db.php');

// --- Restrict access to admins only ---
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// --- Validate the module ID ---
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_modules.php?msg=Invalid+module+ID");
    exit;
}

$moduleId = (int) $_GET['id'];

// --- Check if module exists ---
$stmt = $pdo->prepare("SELECT * FROM modules WHERE ModuleID = ?");
$stmt->execute([$moduleId]);
$module = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$module) {
    header("Location: manage_modules.php?msg=Module+not+found");
    exit;
}

try {
    // --- Delete related records first ---
    // Delete programme-module links
    $pdo->prepare("DELETE FROM programmemodules WHERE ModuleID = ?")->execute([$moduleId]);
    
    // --- Delete the module record ---
    $pdo->prepare("DELETE FROM modules WHERE ModuleID = ?")->execute([$moduleId]);
    
    header("Location: manage_modules.php?msg=Module+deleted+successfully");
} catch (PDOException $e) {
    header("Location: manage_modules.php?msg=Error+deleting+module");
}
exit;
?>
