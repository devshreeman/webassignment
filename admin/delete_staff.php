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
$stmt = $pdo->prepare("SELECT * FROM Staff WHERE StaffID = ?");
$stmt->execute([$staffId]);
$staff = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$staff) {
    header("Location: manage_staff.php?msg=Staff+not+found");
    exit;
}

// --- Delete the staff record ---
$delete = $pdo->prepare("DELETE FROM Staff WHERE StaffID = ?");
$delete->execute([$staffId]);

header("Location: manage_staff.php?msg=Staff+deleted+successfully");
exit;
?>
