<?php
return array(
    'image/show' => new \Zend_Controller_Router_Route_Regex(
        'image/([0-9a-z]{32,40})(\.(gif|jpe?g|png))?',
        array(
            'controller' => 'image',
            'action'     => 'show',
        ),
        array(
            1 => 'access_key', // 本家が md5 32文字なので
            3 => 'type',
        )
    ),
    'list' => new \Zend_Controller_Router_Route(
        'list/:client_id',
        array(
            'controller' => 'list',
            'action'     => 'index',
        ),
        array(
            'client_id' => '[0-9a-z]{32,40}' // 本家が md5 32文字なので
        )
    ),
);
