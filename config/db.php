<?php
/* Database Configuration */
$host = "localhost";
$dbname = "student_course_hub";
$username = "root";
$password = "";

/* Establish PDO Connection */
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}