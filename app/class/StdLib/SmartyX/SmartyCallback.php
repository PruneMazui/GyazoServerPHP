<?php
namespace GyazoHj\StdLib\SmartyX;

/**
 * Smarty のプラグインでクロージャーを使うためのラッパー
 *
 * プライベートメソッドをプラグイン関数として登録するための仕込み
 *
 * 
 */
class SmartyCallback
{
    private $_callback;

    public static function block(callable $callback)
    {
        $obj = new static($callback);
        return array($obj, '_block');
    }

    public function __construct(callable $callback)
    {
        $this->_callback = $callback;
    }

    public function _block($params, $content, $template, &$repeat)
    {
        $callback = $this->_callback;
        return $callback($params, $content, $template, $repeat);
    }
}
