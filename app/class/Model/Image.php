<?php
namespace GyazoPhp\Model;

use GyazoPhp\AppException;

class Image extends AbstractDaoModel
{
    const CONTENT_TYPE_PNG = 'image/png';
    const CONTENT_TYPE_JPG = 'image/jpeg';
    const CONTENT_TYPE_GIF = 'image/gif';
    const CONTENT_TYPE_UNKNOWN = '';

    /**
     * ContentTypeを判別
     * @param string optional $type
     * @return string
     */
    public static function getContentType($type = null)
    {
        if(is_null($type))
        {
            return self::CONTENT_TYPE_PNG;
        }

        $type = strtolower($type);

        if($type == 'png')
        {
            return self::CONTENT_TYPE_PNG;
        }

        if($type == 'gif')
        {
            return self::CONTENT_TYPE_GIF;
        }

        if($type == 'jpg' || $type == 'jpeg')
        {
            return self::CONTENT_TYPE_JPG;
        }

        return self::CONTENT_TYPE_UNKNOWN;
    }

    /**
     *
     * @param array $files
     *            $_FILES
     */
    public static function getUploadFilename(array $files)
    {
        $file = $files['imagedata'];

        if ($file['error'] != 0 || ! file_exists($file['tmp_name'])) {
            return "";
        }
        $filename = $file['tmp_name'];

        // 必ずPNGになるはず
        if (exif_imagetype($filename) != IMAGETYPE_PNG) {
            return "";
        }

        return $filename;
    }

    /**
     * JPG画像に変換
     * @param string $data
     * @return string
     */
    public static function convertJpg($data)
    {
        $im = imagecreatefromstring($data);

        ob_start();
        imagejpeg($im);
        $ret = ob_get_contents();
        ob_end_clean();

        imagedestroy($im);

        return $ret;
    }

    /**
     * GIF画像に変換
     * @param string $data
     * @return string
     */
    public static function convertGif($data)
    {
        $im = imagecreatefromstring($data);

        ob_start();
        imagegif($im);
        $ret = ob_get_contents();
        ob_end_clean();

        imagedestroy($im);

        return $ret;
    }

    /**
     * ファイルの登録
     * @param string $filename
     * @param array $user
     * @throws AppException
     * @return string
     */
    public function registorImage($filename, array $user)
    {
        $file_data = @file_get_contents($filename);
        if($file_data === false)
        {
            throw new AppException("画像ファイルの読み込みに失敗しました。");
        }

        $access_key = sha1(microtime(true) . $file_data);

        list($width, $height) = getimagesize($filename);

        $this->_db->insert('t_image', [
            'access_key' => $access_key,
            'size' => strlen($file_data),
            'width' => $width,
            'height' => $height,
            'user_id' => $user['user_id'],
            'insert_date' => new \Zend_Db_Expr('CURRENT_TIMESTAMP'),
        ]);

        $image_id = $this->_db->lastInsertId('t_image');

        $this->_db->insert('t_image_data', [
            'image_id' => $image_id,
            'data' => $file_data,
        ]);

        return $access_key;
    }

    /**
     * アクセスキーから1件取得
     * @param string $access_key
     * @return array
     */
    public function fetchByAccessKey($access_key)
    {
        $sql = "
            SELECT
                im.*,
                dt.data
            FROM t_image AS im
            INNER JOIN t_image_data AS dt USING(image_id)
            WHERE im.access_key = ?
            ";

        return $this->_db->fetchRow($sql, [$access_key]);
    }

    /**
     * アクセスキーから画像データを1件取得
     *
     * @param string $access_key
     * @param string optional $type
     * @return string
     */
    public function fetchDataByAccessKey($access_key, $type = null)
    {
        $content_type = self::getContentType($type);
        if($content_type == self::CONTENT_TYPE_UNKNOWN)
        {
            return '';
        }

        $row = $this->fetchByAccessKey($access_key);

        if(! $row) {
            return '';
        }

        switch ($content_type)
        {
            case self::CONTENT_TYPE_PNG: return $row['data'];
            case self::CONTENT_TYPE_JPG: return self::convertJpg($row['data']);
            case self::CONTENT_TYPE_GIF: return self::convertGif($row['data']);
            default: return '';
        }
    }

    /**
     * クライアントIDが一致する情報をすべて取得
     *
     * @param string $client_id
     * @return array
     */
    public function fetchAllFromClientId($client_id)
    {
        $sql = "
            SELECT
                im.*
            FROM t_user AS us
            INNER JOIN t_image AS im USING(user_id)
            WHERE us.client_id = ?
            ";

        return $this->_db->fetchAll($sql, [$client_id]);
    }

    /**
     * レコードが一致する画像を削除
     * @param string $client_id
     * @param string $access_key
     * @return bool 削除できたか
     */
    public function delete($client_id, $access_key)
    {
        $sql = "
            DELETE im
            FROM t_image AS im
            INNER JOIN t_user AS us USING(user_id)
            WHERE us.client_id = ?
              AND im.access_key = ?
        ";

        $stmt = $this->_db->prepare($sql);
        $stmt->execute([
            $client_id,
            $access_key,
        ]);

        return $stmt->rowCount() == 1;
    }
}