<?php
include $_SERVER['DOCUMENT_ROOT'].'/db/db.php';
header('Content-Type: text/html; charset=utf-8', true);
unset($_SESSION['password']);
unset($_SESSION['login']);
$server = $_SERVER['HTTP_REFERER'];
echo "<meta http-equiv='Refresh' content='0; URL=$server'>";