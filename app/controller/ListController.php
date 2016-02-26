<?php

use GyazoPhp\Model\ApplyType;
use GyazoPhp\Model\Image;

class ListController extends AbstractController
{
    public function indexAction()
    {
        $client_id = $this->_getParam('client_id');

        if(!strlen($client_id))
        {
            return $this->_response404();
        }

        $model_image = new Image($this->_database);
        $this->view->images = $model_image->fetchAllFromClientId($client_id);
        $this->view->client_id = $client_id;
    }
}
