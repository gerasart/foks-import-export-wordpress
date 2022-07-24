#!/usr/bin/env php
<?php
$rootDir = dirname(__DIR__, 4);

$_SERVER['QUERY_STRING'] = '';

require($rootDir . '/wp-config.php');
require $rootDir . '/wp-load.php';
require_once FOKS_PATH . '/vendor/autoload.php';

$app = new \Symfony\Component\Console\Application();
$app->add(new \Foks\Console\ImportCommand());
$app->run();
