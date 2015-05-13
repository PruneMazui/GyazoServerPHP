<?php
namespace GyazoHj\Model;

use GyazoHj\App;

/**
 * モデル - 抽象
 *
 * 
 */
abstract class AbstractModel extends \GyazoHj\StdLib\ObjectAbstract
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
