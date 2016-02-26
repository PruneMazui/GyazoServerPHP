<?php
namespace GyazoPhp;

use GyazoPhp\App;
use GyazoPhp\StdLib\FileSystemUtil;
use GyazoPhp\StdLib\SmartyX;

class Bootstrap extends \Zend_Application_Bootstrap_Bootstrap
{
    public static function classof()
    {
        return get_called_class();
    }

    /**
     * コントローラ用オートロード
     */
    protected function _initAutoLoad()
    {
        spl_autoload_register(function ($classname)
        {

            $fn = CTRL_DIR . DIRECTORY_SEPARATOR . str_replace('_', DIRECTORY_SEPARATOR, $classname) . '.php';

            if (is_file($fn))
            {
                return require $fn;
            }

            return false;
        });
    }

    /**
     * データベース
     *
     * @return \ZendX_Db_Adapter_Pdo_Mysql
     */
    protected function _initDatabase()
    {
        return App::getInstance()->createDbAdapter();
    }

    /**
     * フロントコントローラ
     *
     * @return \Zend_Controller_Front
     */
    protected function _initFrontController()
    {
        $front = \Zend_Controller_Front::getInstance();

        // コントローラーディレクトリ
        $front->setControllerDirectory(CTRL_DIR);

        return $front;
    }

    /**
     * ビュー
     *
     * @return \Zend_View
     */
    protected function _initView()
    {
        $app = App::getInstance();

        //準備
        $compile_dir = $app->getLocalDataDir() . '/templates_c';
        FileSystemUtil::makeWritableDirectory($compile_dir);

        //Smartyインスタンス生成
        $smarty = new SmartyX\Smarty(array(
            'documentRoot' => HTML_DIR
        ));
        $smarty->escape_html = true;
        $smarty->compile_dir = $compile_dir;
        $smarty->force_compile = _BUILD;
        $smarty->debugging = _DEBUG;
        $smarty->allow_php_templates = true;

        //ビジネスロジカルな拡張を追加
        $extend = new SmartyX\SmartyExtend($smarty);
        $smarty->register($extend);

        //SmartyでZend_Viewを使用できるように
        $view = new \Zx_View_Smarty($smarty);
        $view->setScriptPath(APP_DIR . '/views');
        $viewRenderer = \Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view)->setViewSuffix('tpl');

        //設定を$configでアクセスできるように
        $view->config = $app->getConfig();

        //マスタを$masterでアクセスできるように
        $view->master = $app->getMaster();

        return $view;
    }

    /**
     * レイアウト
     *
     * @return \Zend_Layout
     */
    protected function _initLayout()
    {
        $view = $this->getResource('View');

        $layout = \Zend_Layout::startMvc();
        $layout->setView($view)->setViewSuffix('tpl');

        $view->layout = $layout;

        return $layout;
    }

    /**
     * ルーティング
     *
     * @return \Zend_Controller_Router_Abstract
     */
    protected function _initRoute()
    {
        $front = \Zend_Controller_Front::getInstance();
        $router = $front->getRouter();

        $route = include INC_DIR . '/route.php';
        foreach($route as $key => $val) {
            $router->addRoute($key, $val);
        }

        return $router;
    }
}
