<?php

require_once __DIR__ . '/../vendor/autoload.php';

$router = new AltoRouter();

$router->map( 'GET', '/', include '../view/top.php');
$router->map( 'GET', '/blockchain', include '../app/BlockChain.php');
