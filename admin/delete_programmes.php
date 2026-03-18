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
$stmt = $pdo->prepare("SELECT * FROM Programmes WHERE ProgrammeID = ?");
$stmt->execute([$programmeId]);
$programme = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$programme) {
    header("Location: manage_programmes.php?msg=Programme+not+found");
    exit;
}

// --- Delete the programme record ---
$delete = $pdo->prepare("DELETE FROM Programmes WHERE ProgrammeID = ?");
$delete->execute([$programmeId]);

header("Location: manage_programmes.php?msg=Programme+deleted+successfully");
exit;
?>
