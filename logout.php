<?php
require_once 'auth_functions.php';

session_start();
logout();
header('Location: admin_login.php');
exit;
?>