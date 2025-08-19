<?php

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

defined('YII_DEBUG') or define('YII_DEBUG', $_ENV['YII_DEBUG'] === 'true');
defined('YII_ENV') or define('YII_ENV', in_array($_ENV['YII_ENV'], ['dev', 'prod', 'test']) ? $_ENV['YII_ENV'] : 'prod');