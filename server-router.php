<?php
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = urldecode($uri);

if (preg_match('#^/Inventory_CI#i', $uri)) {
    require 'C:/xampp/htdocs/Inventory_CI/router.php';
    return;
}

$file = __DIR__ . $uri;
if ($uri !== '/' && file_exists($file) && is_file($file)) {
    return false;
}

$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['PATH_INFO'] = $uri;
require __DIR__ . '/index.php';
