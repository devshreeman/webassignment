<?php
session_start();

unset($_SESSION['student']);
session_destroy();

header('Location: ../public/index.php');
exit;
