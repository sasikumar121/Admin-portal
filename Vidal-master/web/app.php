<?php

error_reporting(0);

use Symfony\Component\HttpFoundation\Request;

if(empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off") {
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if (strpos($redirect, '/api/') === false && strpos($redirect, '/archive') === false) {
        header('HTTP/2 301 Moved Permanently');
        header('Location: ' . $redirect);
        exit();
    }
}

# память, необходимая админке Сонаты
if (strpos($_SERVER['REQUEST_URI'], '/admin/vidal/') !== false) {
	ini_set('memory_limit', -1);
}

$loader = require_once __DIR__ . '/../app/bootstrap.php.cache';
require_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('prod', false);
$kernel->loadClassCache();
$request  = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
