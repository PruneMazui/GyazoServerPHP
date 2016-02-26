<?php
namespace GyazoPhp\Model;

use GyazoPhp\App;

/**
 * モデル - 抽象
 *
 * 
 */
abstract class AbstractModel extends \GyazoPhp\StdLib\ObjectAbstract
{
    public function __construct()
    {
        $this->_init();
    }

    protected function _init()
    {
        // noop
    }

    /**
     * @return App
     */
    protected function _getApp()
    {
        return App::getInstance();
    }
}
