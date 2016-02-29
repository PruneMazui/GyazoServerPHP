<?php

use GyazoPhp\Model\ApplyType;

class IndexController extends AbstractController
{
    public function indexAction()
    {
        $host = $_SERVER['HTTP_HOST'];
        $port = null;

        $matches = array();
        if(preg_match('/^(.*):(\d{1,5})$/', $host, $matches)) {
            $host = $matches[1];
            $port = $matches[2];
        }
        $this->view->host = $host;
        $this->view->port = $port;
    }
}
