<?php
// log out staff member and clear session
session_start();
session_unset();
session_destroy();
header("Location: ../public/index.php");
exit;
