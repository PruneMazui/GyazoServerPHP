<?php
namespace GyazoPhp\Model;

class User extends AbstractDaoModel
{

    /**
     * ユーザーIDから1件取得
     *
     * @param string $client_id
     * @return array
     */
    public function fetchByUserId($user_id)
    {
        return $this->_db->fetchRow("
            SELECT
                *
            FROM t_user
            WHERE user_id = ?
        ", [
            $user_id
        ]);
    }

    /**
     * クライアントIDから1件取得
     *
     * @param string $client_id
     * @return array
     */
    public function fetchByClientId($client_id)
    {
        return $this->_db->fetchRow("
            SELECT
                *
            FROM t_user
            WHERE client_id = ?
        ", [
            $client_id
        ]);
    }

    /**
     * リクエストから1件取得
     *
     * @param \Zend_Controller_Request_Http $request
     * @return array
     */
    public function fetchByRequest(\Zend_Controller_Request_Http $request)
    {
        $client_id = $request->getParam('id');
        if (strlen($client_id)) {
            $row = $this->fetchByClientId($client_id);

            if ($row) {
                return $row;
            }
        }

        // ユニークになるまで生成
        $ip = $request->getClientIp();
        while (!strlen($client_id) || $this->fetchByClientId($client_id)) {
            $client_id = sha1($ip . microtime(true));
        }

        $this->_db->insert('t_user', [
            'client_id' => $client_id,
            'ip_addr' => $ip,
            'insert_date' => new \Zend_Db_Expr('CURRENT_TIMESTAMP'),
            'update_date' => new \Zend_Db_Expr('CURRENT_TIMESTAMP')
        ]);

        return $this->fetchByUserId($this->_db->lastInsertId('t_user'));
    }
}