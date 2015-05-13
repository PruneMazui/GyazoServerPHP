<?php
use GyazoHj\App;
use GyazoHj\Model;
use GyazoHj\HttpException;

/**
 * コントローラー基底クラス
 * @author tanaka
 */
abstract class AbstractController extends \Zend_Controller_Action
{
    /**
     * データベースアダプタ
     *
     * @var ZendX_Db_Adapter_Pdo_Mysql
     */
    protected $_database = null;

    /**
     * Abstract なので Http になるように再宣言
     *
     * @var Zend_Controller_Request_Http
     */
    protected $_request;

    /**
     * Abstract なので Http になるように再宣言
     *
     * @var Zend_Controller_Response_Http
     */
    protected $_response;

    /**
     * @see Zend_Controller_Action::init()
     */
    public function init()
    {
        parent::init();

        $this->_helper->Redirector->setUseAbsoluteUri(true);

        if (!defined('_TEST') || !_TEST)
        {
            $this->_helper->Redirector->setExit(false);
            $this->_helper->Json->suppressExit = false;
        }

        //db接続
        $this->_database = $this->getInvokeArg('bootstrap')->getResource('database');
    }

    /**
     * @see Zend_Controller_Action::preDispatch()
     */
    public function preDispatch()
    {
        parent::preDispatch();

        // コントローラ
        $this->view->controller = $this;
    }

    /**
     * @see Zend_Controller_Action::postDispatch()
     */
    public function postDispatch()
    {
        parent::postDispatch();

        //存在するプロパティをviewに割り当てる
        $property = get_object_vars($this);
        foreach ($property as $key => $value)
        {
            if (substr($key, 0, 1) === '_' && $key !== '_invokeArgs')//でかすぎる
            {
                $key = ltrim($key, '_');
                $this->view->$key = $value;
            }
        }
    }

    /**
     * @see Zend_Controller_Request_Http::getPost()
     */
    protected function _getPost($key = null, $default = null)
    {
        // POST メソッドなアクションを他のメソッドで呼び出した場合は404
        if (!$this->_request->isPost())
        {
            $this->_response404();
        }

        return $this->_request->getPost($key, $default);
    }

    /**
     * レイアウトを無効にする。
     * 名前が意図と違うかもしれないが良い名前が浮かばない。
     * そもそもレイアウト「だけ」無効にしたい状況なんてまずありえないのでこのままでもいいかも。
     */
    protected function _disableLayout()
    {
        $this->_helper->ViewRenderer->setNoRender();
        $this->_helper->Layout->disableLayout();
    }

    /**
     * レンダリングファイルを明示的に指定
     * @param string $tplFileName
     * @param string $tplFilePath
     */
    protected function _setViewRenderFile($tplFileName, $tplFilePath = ':controller/')
    {
        return $this->_helper->viewRenderer->setViewScriptPathSpec($tplFilePath . $tplFileName . '.:suffix');
    }

    /**
     * ルート名でリダイレクト
     * @param array $urlOptions
     * @param string $name
     * @param string $reset
     * @param string $encode
     */
    protected function _redirectRoute(array $urlOptions = array(), $name = null, $reset = false, $encode = true)
    {
        $this->_helper->redirector->gotoRoute($urlOptions, $name, $reset, $encode);
    }

    /**
     * アクション、コントローラ名でリダイレクト
     * @param string $action
     * @param string|array $controller
     * @param string $module
     * @param array $params
     */
    protected function _redirectSimple($action, $controller = null, $module = null, array $params = array())
    {
        //簡易化のため第2引数が配列だったら$paramsとみなす
        if (is_array($controller))
        {
            $params = $controller;
            $controller = null;
        }
        //moduleも同様
        if (is_array($module))
        {
            $params = $module;
            $module = null;
        }

        $this->_helper->redirector->gotoSimple($action, $controller, $module, $params);
    }

    /**
     * リダイレクト時の自動exitを無効にしてるので明らかな不要な場合でも無駄な処理が走る。
     * のでリダイレクトの後すぐに終了したい場合はこのメソッドを使う
     * @param string $action
     * @param string|array $controller
     * @param string $module
     * @param array $params
     */
    protected function _redirectAndExit($action, $controller = null, $module = null, array $params = array())
    {
        $this->_redirectSimple($action, $controller, $module, $params);
        $this->_helper->redirector->redirectAndExit();
    }

    /**
     * 400レスポンスを返す
     * @param string $message 表示メッセージ
     * @throws HttpException
     */
    protected function _response400($message = null)
    {
        throw new HttpException($message, 400);
    }

    /**
     * 403レスポンスを返す
     * @param string $message 表示メッセージ
     * @throws HttpException
     */
    protected function _response403($message = null)
    {
        throw new HttpException($message, 403);
    }

    /**
     * 404レスポンスを返す
     * @param string $message 表示メッセージ
     * @throws HttpException
     */
    protected function _response404($message = null)
    {
        throw new HttpException($message, 404);
    }

    /**
     * 405レスポンスを返す (Method Not Allowed)
     *
     * @param string $message 表示メッセージ
     * @throws HttpException
     */
    protected function _response405($message = null)
    {
        throw new HttpException($message, HttpException::METHOD_NOT_ALLOWED);
    }

    /**
     * リクエストURLからコントローラ名とアクション名を取り除いたパスを得る
     */
    protected function _getRequestPathInfo($depth = 1, $ltrim = '!')
    {
        $self = $this->_request->getServer('PHP_SELF');
        $path = $this->_request->getServer('REQUEST_URI');

        $self = parse_url($self, PHP_URL_PATH);
        $path = parse_url($path, PHP_URL_PATH);

        $self = explode("/", $self);
        $path = explode("/", $path);

        $path = array_slice($path, count($self) + $depth);
        $path = implode("/", $path);

        if (strlen($ltrim))
        {
            $path = ltrim($path, $ltrim);
        }

        return $path;
    }
}
