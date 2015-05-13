<?php

// データベース
$cfg['database'] = array(
    'host'     => '127.0.0.1',
    'port'     => '3306',
    'dbname'   => 'gyazo_hj',
    'username' => 'gyazo_hj',
    'password' => '',
    'charset'  => 'utf8',
);

if (_TEST)
{
    $cfg['database']['dbname'] = 'test_gyazo_hj';
}

// ディレクトリ
$cfg['storage'] = array(
    'local_dir'  => '/var/opt/gyazo-hj/',
    'local_log'  => '/var/log/gyazo-hj/',
    'local_tmp'  => '/var/tmp/gyazo-hj/',
);
