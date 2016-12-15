<?php

// データベース
$cfg['database'] = array(
    'host'     => '127.0.0.1',
    'port'     => '3306',
    'dbname'   => 'gyazo_php',
    'username' => 'gyazo_php',
    'password' => '',
    'charset'  => 'utf8',
);

if (_TEST)
{
    $cfg['database']['dbname'] = 'test_gyazo_php';
}

// ディレクトリ
$cfg['storage'] = array(
    'local_dir'  => '/var/opt/gyazo-php/',
    'local_log'  => '/var/log/gyazo-php/',
    'local_tmp'  => '/var/tmp/gyazo-php/',
);

$cfg['path'] = '/';