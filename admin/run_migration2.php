<?php
include('../config/db.php');
$stmt = $pdo->query("DESCRIBE modules");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($cols);
echo "</pre>";
?>
