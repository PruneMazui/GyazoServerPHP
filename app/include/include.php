<?php
use GyazoPhp\App;
use GyazoPhp\AutoLoader;

call_user_func(function() {

    define('_TEST', !!getenv('APPLICATION_TEST'));

    define('BASE_DIR',  dirname(dirname(__DIR__)));
    define('APP_DIR',   BASE_DIR . '/app');
    define('HTML_DIR',  BASE_DIR . '/htdocs');
    define('INC_DIR',   APP_DIR  . '/include');
    define('CLASS_DIR', APP_DIR  . '/class');
    define('CTRL_DIR',  APP_DIR  . '/controller');

    // umask
    umask(0);

    // マルチバイト
    mb_internal_encoding('UTF-8');
    mb_regex_encoding('UTF-8');

    // Content-Type: text/html; charset=utf-8
    ini_set('default_charset', 'utf-8');

    // ロケール
    //setlocale(LC_ALL, 'ja_JP.UTF-8');
    setlocale(LC_COLLATE,  'ja_JP.UTF-8');
    setlocale(LC_CTYPE,    'ja_JP.UTF-8');
    setlocale(LC_MONETARY, 'ja_JP.UTF-8');
    setlocale(LC_NUMERIC,  'ja_JP.UTF-8');
    setlocale(LC_TIME,     'ja_JP.UTF-8');

    // vendor/autoload
    require BASE_DIR . '/vendor/autoload.php';

    // GyazoPhp
    require_once CLASS_DIR . '/AutoLoader.php';
    AutoLoader::addNamespace('GyazoPhp', CLASS_DIR);

    // ロケール設定(ja_JP.UTF-8がZFで対応されていない)
    \Zend_Locale::setDefault('ja_JP');

    // 定数
    require_once INC_DIR . '/const.php';

    // アプリケーション設定
    App::getInstance();

    defined('_DEBUG') or define('_DEBUG', function_exists('xdebug_is_enabled') && xdebug_is_enabled());
    defined('_BUILD') or define('_BUILD', false);
});
