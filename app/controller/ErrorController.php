<?php

use GyazoHj\HttpException;
use GyazoHj\StdLib\ErrorHandler\LoggingHandler;

/**
 * コントローラ - エラー
 */
class ErrorController extends Zend_Controller_Action
{
    /**
     * エラーページ表示
     * @return	void
     */
    public function errorAction()
    {
        parent::preDispatch();

        $this->_helper->viewRenderer->setViewScriptPathSpec(':controller/:action.:suffix');

        $errors = $this->_getParam('error_handler');
        $ex = $errors->exception;

        $response = $this->getResponse();

        //HttpExceptionでない例外
        if (!$ex instanceof HttpException)
        {
            switch ($errors->type)
            {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                $ex = new HttpException(null, 404);
                break;
            default:
                LoggingHandler::logException($ex);
                $ex = new HttpException(null, 500);
                break;
            }
        }

        $response->setHttpResponseCode($ex->getCode());
        $title = $ex->getHeader();
        $message = $ex->getMessage();

        //Ajaxの場合はレイアウトは使わない。ボディにエラーメッセージのみを含める
        if ($this->_request->isXmlHttpRequest())
        {
            $this->_helper->ViewRenderer->setNoRender();
            $this->_helper->Layout->disableLayout();

            $this->_response->setBody($message);
        }

        $this->view->message = $message;
    }
}
