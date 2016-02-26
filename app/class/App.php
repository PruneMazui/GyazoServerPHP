<?php
/**
 * アプリケーション
 *
 * 
 */

namespace GyazoPhp;

use GyazoPhp\StdLib\ConfigScanner;
use GyazoPhp\StdLib\PathUtil;

/**
 * アプリケーション
 *
 * 
 */
class App
{
    private static $instance;

    private $_files = array();

    private $_cfg = array();

    private $_servername = null;

    private $_master;

    /**
     * インスタンス取得
     *
     * @return \GyazoPhp\App
     */
    public static function getInstance()
    {
        if (isset(self::$instance) === false)
        {
            self::$instance = new self(getenv('APPLICATION_CONFIG_FILE'));
        }

        return self::$instance;
    }

    /**
     * コンストラクタ
     *
     * @param string $filename
     */
    public function __construct($filename = null)
    {
        $this->_initConfig($filename);
    }

    /**
     * 設定ファイルの読み込み
     */
    private function _initConfig($filename)
    {
        $this->_cfg = array();

        // デフォルト設定ファイル
        $fn = INC_DIR . "/config.php";
        $this->_readConfig($fn);

        if (strlen($filename) !== 0)
        {
            // ファイルを指定

            $fn = PathUtil::combine(APP_DIR . "/config", $filename);
            $this->_readConfig($fn);
        }
        else
        {
            // ホストごと設定

            $scan = new ConfigScanner();

            $dirs = array(
                APP_DIR . "/config",
            );

            $fn = $scan->scan($dirs);

            if ($fn !== false)
            {
                $this->_readConfig($fn);
            }
        }
    }

    private function _readConfig($fn)
    {
        $cfg = $this->_cfg;

        require $fn;

        $this->_cfg = $cfg;

        $this->_files[] = $fn;
    }

    /**
     * インクルードされたコンフィグの一覧を取得
     *
     * @return array
     */
    public function getIncludedConfigFiles()
    {
        return $this->_files;
    }

    /**
     * コンフィグデータを設定
     *
     * @param string $key
     *
     * @return mixed
     */
    public function setConfig(array $cfg)
    {
        $this->_cfg = $cfg;
    }

    /**
     * コンフィグデータを取得
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getConfig($key = null)
    {
        if( $key === null)
        {
            return $this->_cfg;
        }
        else
        {
            $cfg = $this->_cfg;
            $keys = explode('.', $key);

            foreach ($keys as $key)
            {
                $cfg = $cfg[$key];
            }

            return $cfg;
        }
    }

    /**
     * コンフィグのサーバごとのエントリを取得
     */
    public function getServerConfig()
    {
        $cfg = $this->getConfig('servers');

        if ($this->_servername === null)
        {
            $hostname = trim(php_uname('n'));

            list ($this->_servername) = explode('.', $hostname, 2);

            if (!isset($cfg[$this->_servername]))
            {
                $this->_servername = '';
            }
        }

        return $cfg[$this->_servername];
    }

    /**
     * マスターデータを取得
     *
     * @param string $key
     * @throws LogicException
     *
     * @return mixed
     */
    public function getMaster($key = null)
    {
        if (isset($this->_master) === false )
        {
            $master = require INC_DIR . '/master.php';

            if (is_array($master) === false)
            {
                // @codeCoverageIgnoreStart
                throw new \LogicException("master should be array.");
                // @codeCoverageIgnoreEnd
            }

            $this->_master = $master;
        }

        if ($key === null)
        {
            return $this->_master;
        }
        else
        {
            return $this->_master[$key];
        }
    }

    /**
     * データベースアダプタを作成
     *
     * @return ZendX_Db_Adapter_Pdo_Mysql
     */
    public function createDbAdapter()
    {
        $cfg = $this->getConfig('database');

        if (!isset($cfg['driver_options']))
        {
            $cfg['driver_options'] = array();
        }

        // LOAD DATA LOCAL INFILE を使用可能にする
        $cfg['driver_options'] += [
            \PDO::MYSQL_ATTR_LOCAL_INFILE => 1
        ];

        return new \Zend_Db_Adapter_Pdo_Mysql($cfg);
    }

    /**
     * ローカルストレージディレクトリを取得
     *
     * @return string
     */
    public function getLocalDataDir()
    {
        return PathUtil::combine(BASE_DIR, $this->getConfig('storage.local_dir'));
    }

    /**
     * ローカルログディレクトリを取得
     *
     * @return string
     */
    public function getLocalLogDir()
    {
        return PathUtil::combine(BASE_DIR, $this->getConfig('storage.local_log'));
    }

    /**
     * ローカルテンポラリディレクトリを取得
     *
     * @return string
     */
    public function getLocalTmpDir()
    {
        return PathUtil::combine(BASE_DIR, $this->getConfig('storage.local_tmp'));
    }
}
