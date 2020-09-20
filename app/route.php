<?php

require_once __DIR__ . '/../vendor/autoload.php';

$router = new AltoRouter();

$router->map('GET','/blockchain', 'App\BlockChainServer::getChain');

include 'route-config.php';
