<?php
namespace GyazoPhp;

class Application extends \Zend_Application
{
    public function __construct()
    {
        parent::__construct('dummy');
        $this->setBootstrap(null, \GyazoPhp\Bootstrap::classof());
    }
}
