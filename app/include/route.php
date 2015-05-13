<?php
return array(
    'image/show' => new \Zend_Controller_Router_Route(
        'image/:access_key',
        array(
            'controller' => 'image',
            'action'     => 'show',
        ),
        array(
            'access_key' => '[0-9a-z]{40}'
        )
    ),
    'list' => new \Zend_Controller_Router_Route(
        'list/:client_id',
        array(
            'controller' => 'list',
            'action'     => 'index',
        ),
        array(
            'client_id' => '[0-9a-z]{40}'
        )
    ),
);