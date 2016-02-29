<?php

ini_set('display_errors', 0);
error_reporting(0);

//header("HTTP/1.0 404 Not Found");
header('Content-Type: text/html; charset=utf-8');
session_start();

function __autoload($class_name) {
	require_once  '/php/'. $class_name . '.php';
}  

$rute = new ruteClass((object)$_POST);

if($rute->htmlON)
	require_once  'html/index.html';


?>


