<?php
require_once realpath(__DIR__ . '/../app/include/include.php');

$application = new \GyazoHj\Application();
$application->bootstrap()->run();
