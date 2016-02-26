<?php
namespace GyazoPhp\Model;

use GyazoPhp\AppException;
use GyazoPhp\StdLib\SafeInvoker;

/**
 *
 */
abstract class AbstractDaoModel extends AbstractModel
{
    /**
     * @var \Zend_Db_Adapter_Pdo_Mysql
     */
    protected $_db;

    public function __construct(\Zend_Db_Adapter_Pdo_Mysql $db)
    {
        $this->_db = $db;

        parent::__construct();
    }
}
