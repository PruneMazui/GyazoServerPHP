<?php
require_once realpath(__DIR__ . '/../app/include/include.php');

$application = new \GyazoPhp\Application();
$application->bootstrap()->run();
