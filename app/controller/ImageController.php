<?php
use GyazoHj\Model\ApplyType;
use GyazoHj\Model\Image;
use GyazoHj\Model\User;

class ImageController extends AbstractController
{
    /**
     * 画像を出力
     */
    public function showAction()
    {
        $access_key = $this->_getParam("access_key");

        if (! strlen($access_key)) {
            return $this->_response404();
        }

        $model_image = new Image($this->_database);
        $row = $model_image->fetchByAccessKey($access_key);

        if (! $row)
        {
            return $this->_response404();
        }

        $this->_disableLayout();

        $response = $this->getResponse();
        $response->clearAllHeaders()
            ->setHeader('Content-type', 'image/png')
            ->setBody($row['data']);
    }

    /**
     * 削除
     */
    public function deleteAction()
    {
        if(!$this->_request->isPost())
        {
            return $this->_response400();
        }

        $access_key = $this->_getParam("access_key");
        $client_id = $this->_getParam('client_id');

        $model_image = new Image($this->_database);

        $this->_helper->Json($model_image->delete($client_id, $access_key));
    }

    /**
     * 画像のアップロード
     */
    public function uploadAction()
    {
        if (! $this->_request->isPost()) {
            return $this->_response400();
        }

        $filename = Image::getUploadFilename($_FILES);
        if (! strlen($filename)) {
            return $this->_response400();
        }

        $db = $this->_database;

        $model_user = new User($this->_database);
        $model_image = new Image($this->_database);

        $db->beginTransaction();

        try {
            $user = $model_user->fetchByRequest($this->_request);
            $access_key = $model_image->registorImage($filename, $user);

            $db->commit();
        } catch (Exception $ex) {
            $db->rollBack();
            throw $ex;
        }

        $this->_disableLayout();

        $this->getResponse()
            ->clearAllHeaders()
            ->setHeader(C_RESPONSE_CLIENT_ID, $user['client_id'])
            ->setBody("http://" . $_SERVER['HTTP_HOST'] . '/image/' . $access_key);
    }
}