<?php
session_start();
include('../config/db.php');

// --- Restrict access to admins only ---
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// --- Validate the programme ID ---
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_programmes.php?msg=Invalid+programme+ID");
    exit;
}

$programmeId = (int) $_GET['id'];

// --- Check if programme exists ---
$stmt = $pdo->prepare("SELECT * FROM programmes WHERE ProgrammeID = ?");
$stmt->execute([$programmeId]);
$programme = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$programme) {
    header("Location: manage_programmes.php?msg=Programme+not+found");
    exit;
}

try {
    // --- Delete related records first ---
    // Delete programme-module links
    $pdo->prepare("DELETE FROM programmemodules WHERE ProgrammeID = ?")->execute([$programmeId]);
    
    // --- Delete the programme record (interestedstudents will cascade) ---
    $pdo->prepare("DELETE FROM programmes WHERE ProgrammeID = ?")->execute([$programmeId]);
    
    header("Location: manage_programmes.php?msg=Programme+deleted+successfully");
} catch (PDOException $e) {
    header("Location: manage_programmes.php?msg=Error+deleting+programme");
}
exit;
?>
