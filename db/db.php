<?php
session_start();
error_reporting(0);
$db = new PDO('mysql:host=localhost;dbname=parser_domain;charset=UTF8','parser_domain','03011998n');
header('Content-Type: text/html; charset=utf-8', true);
$error = $_SESSION['error'];
$login = $_SESSION['login'];
$password = $_SESSION['password'];