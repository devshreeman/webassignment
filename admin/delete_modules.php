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
$stmt = $pdo->prepare("SELECT * FROM Modules WHERE ModuleID = ?");
$stmt->execute([$moduleId]);
$module = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$module) {
    header("Location: manage_modules.php?msg=Module+not+found");
    exit;
}

// --- Delete the module record ---
$delete = $pdo->prepare("DELETE FROM Modules WHERE ModuleID = ?");
$delete->execute([$moduleId]);

header("Location: manage_modules.php?msg=Module+deleted+successfully");
exit;
?>
