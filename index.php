<?php

require 'config.php';
require 'util.php';
require VIEW_DIR . 'html_class.php';

$path = isset($_GET['path']) ? $_GET['path'] : '/';
$html = new Html();
global $sql ;
$sql = new MySql( DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if($path === '/')
	return $html->render('main');

$pathParts = explode('/', $path);

$className = $pathParts[0];
$methodName = isset($pathParts[1]) ? $pathParts[1] : 'main';

(new $className())->$methodName($sql);