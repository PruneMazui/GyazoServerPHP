<?php
namespace GyazoHj;

class Application extends \Zend_Application
{
    public function __construct()
    {
        parent::__construct('dummy');
        $this->setBootstrap(null, \GyazoHj\Bootstrap::classof());
    }
}
