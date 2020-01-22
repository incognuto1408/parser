<?php
session_start();
define('unisitecms', true);
header('Content-Type: text/html; charset=utf-8', true);
$_SERVER['DOCUMENT_ROOT'] = "/var/www/klaster-web/data/www/parser.trk.kz";
include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/config.php");
include_once("{$_SERVER['DOCUMENT_ROOT']}/systems/classes/incognito1408.php");
$query = db_query("SELECT * FROM settings");
echo "<pre>";
var_dump($query);
echo "</pre>";
?>