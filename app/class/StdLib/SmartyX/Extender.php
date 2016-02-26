<?php
namespace GyazoPhp\StdLib\SmartyX;

/**
 * @package		SmartyX
 */

/**
 * 与えられたインスタンスの定型パターンにマッチするメソッドをプラグインとして登録する
 */
class Extender
{
    /**
     * Smarty object
     *
     * @var Smarty
     */
    private $_smarty = null;

    /**
     * コンストラクタ.
     *
     * @param Smarty $smarty
     */
    public function __construct($smarty)
    {
        $this->_smarty = $smarty;
    }

    /**
     * プラグイン登録。function_***,modifier_***,block_***,compiler_***のメソッド名が自動登録される。
     *
     * @param object $extend 対象インスタンス
     */
    public function registerPlugin($exntend)
    {
        foreach (get_class_methods($exntend) as $method)
        {
            if ($method === __FUNCTION__)
            {
                continue;
            }

            if (preg_match('/^([^_].+?)_(.+)$/', $method, $match))
            {
                $this->_smarty->registerPlugin($match[1], $match[2], array($exntend, $method));
            }
        }
    }

    /**
     * リソースプラグイン登録。resource_xxx_***のメソッド名がxxxでグルーピングされて登録される。
     *
     * @param object $extend 対象インスタンス
     */
    public function registerResource($exntend)
    {
        $resources = array();
        foreach (get_class_methods($exntend) as $method)
        {
            if ($method === __FUNCTION__)
            {
                continue;
            }

            if (preg_match('/^([^_].+?)_(.+)_(.+)$/', $method, $match))
            {
                $resources[$match[2]][] = array(
                    $exntend,
                    $method
                );
            }
        }

        foreach ($resources as $name => $resource)
        {
            $this->_smarty->registerResource($name, $resource);
        }
    }
}
