<?php
/**
 * Zx_View_Smarty
 */

use GyazoPhp\StdLib\PathUtil;

/**
 * Zend_Viewをsmartyで使うためのクラス。Smarty3必須.
 */
class Zx_View_Smarty extends Zend_View_Abstract
{
    /**
     * Smarty object
     *
     * @var Smarty
     */
    protected $_smarty;

    /**
     * コンストラクタ。
     * Smartyを拡張したSmartyインスタンスを使うかもしれない。
     * ので内部で生成するのではなく、外部から与える仕様とする(そもそも内部で生成する必然性はない)。
     *
     * @param Smarty $smarty Smartyインスタンス
     * @param array $config Zend_View本来の引数
     * @return void
     */
    public function __construct(Smarty $smarty, $config = array())
    {
        $this->_smarty = $smarty;

        //3系列は_versionではなくSMARTY_VERSIONになったのを利用してバージョン判定
        if (isset($this->_smarty->_version))
            throw new UnexpectedValueException('Smarty2 is not supported');

        parent::__construct($config);
    }

    /**
     * @see Zend_View_Abstract::init()
     */
    public function init()
    {
        parent::init();

        // hoge.php で変数を assign するために必要
        $this->_smarty->assign('view', $this);
    }

    /**
     * 変数をテンプレートに代入します
     *
     * @param string $key 変数名
     * @param mixed $val 変数の値
     * @return void
     */
    public function __set($key, $val)
    {
        $this->_smarty->assign($key, $val);
    }

    /**
     * テンプレート変数を返します
     *
     * @param string $key 変数名
     * @return mixed 変数の値
     */
    public function __get($key)
    {
        if (isset($this->$key))
            return $this->_smarty->getTemplateVars($key);

        throw new InvalidArgumentException("Template var `$key` is not set");
    }

    /**
     * empty() や isset() のテストが動作するようにします
     *
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return isset($this->_smarty->tpl_vars[$key]);
    }

    /**
     * オブジェクトのプロパティに対して unset() が動作するようにします
     *
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        $this->_smarty->clearAssign($key);
    }

    /**
     * テンプレートエンジンオブジェクトを返します
     *
     * @return Smarty
     */
    public function getEngine()
    {
        return $this->_smarty;
    }

    /**
     * テンプレートへのパスを設定します
     *
     * @param string $path パスとして設定するディレクトリ
     * @return void
     */
    public function setScriptPath($path)
    {
        if ($path !== null && !is_readable($path))
            throw new InvalidArgumentException("'$path' is not readable");

        parent::setScriptPath($path);

        $this->_smarty->setTemplateDir($path);
    }

    /**
     * テンプレートへのパスを追加します
     *
     * @param string $path パスとして設定するディレクトリ
     * @return void
     */
    public function addScriptPath($path)
    {
        if ($path !== null && !is_readable($path))
            throw new InvalidArgumentException("'$path' is not readable");

        parent::addScriptPath($path);

        $this->_smarty->addTemplateDir($path);
    }

    /**
     * テンプレート変数をすべて取得します。
     *
     * @see Zend_View_Abstract::getVars()
     */
    public function getVars()
    {
        return $this->_smarty->getTemplateVars();
    }

    /**
     * 代入済みのすべての変数を削除します
     *
     * Zend_View に {@link assign()} やプロパティ
     * ({@link __get()}/{@link __set()}) で代入された変数をすべて削除します
     *
     * @return void
     */
    public function clearVars()
    {
        $this->_smarty->clearAllAssign();
    }

    /**
     * レンダリング実処理
     * 兄弟要素の js と php を自動的に読みこむ
     */
    protected function _run()
    {
        $name = func_get_arg(0);

        // js は script タグとして結合する
        $jsfile = PathUtil::changeExtension($name, 'js');
        if (is_file($jsfile))
        {
            $script = file_get_contents($jsfile);
            echo '<script type="text/javascript">' . $script . '</script>';
        }

        // php は fetch して view 変数を得る
        $phpfile = PathUtil::changeExtension($name, 'php');
        if (is_file($phpfile))
        {
            $dummy = $this->_smarty->fetch('php:' . $phpfile);
        }

        //デバッグコンソールが起動する
        //$this->_smarty->display(func_get_arg(0));

        echo $this->_smarty->fetch(func_get_arg(0));
    }
}
