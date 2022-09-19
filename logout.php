<?php
session_start();
unset($_SESSION['form']);
header('Location: login.php'); 
exit();
?>