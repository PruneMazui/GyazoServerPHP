<?php
namespace GyazoHj\Model;

use GyazoHj\AppException;
use GyazoHj\StdLib\SafeInvoker;

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
