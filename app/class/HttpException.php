<?php
/**
 * http例外
 */

namespace GyazoHj;

/**
 * http例外
 *
 * ErrorController内で内部エラー(500)なのか400系なのかを判別するためのクラス。
 * ErrorController内で処理されるので明示的に投げたり受けたりするような例外ではない。
 */
class HttpException extends \Exception
{
    const BAD_REQUEST = 400;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const CONFLICT = 409;
    const METHOD_NOT_ALLOWED = 405;
    const INTERNAL_SERVER_ERROR = 500;

    private $_errorHeaders = array(
        self::BAD_REQUEST           => '400 Bad Request',
        self::FORBIDDEN             => '403 Forbidden',
        self::NOT_FOUND             => '404 Not Found',
        self::METHOD_NOT_ALLOWED    => '405 Method Not Allowed',
        self::CONFLICT              => '409 Conflict',
        self::INTERNAL_SERVER_ERROR => '500 Internal Server Error',
    );

    private $_errorMessages = array(
        self::BAD_REQUEST           => 'リクエストが不正な内容です。',
        self::FORBIDDEN             => '指定されたURLへアクセスする権限がありません。',
        self::NOT_FOUND             => '指定されたURLは見つかりませんでした。',
        self::METHOD_NOT_ALLOWED    => '指定されたリクエストメソッドは使用できません。',
        self::CONFLICT              => '指定されたリソースは競合しています。',
        self::INTERNAL_SERVER_ERROR => '恐れ入りますが暫く時間を置いてアクセスを行ってください。',
    );

    public static function classof()
    {
        return get_called_class();
    }

    public function __construct($message = null, $code = null, $previous = null)
    {
        if($code === null)
        {
            throw new \InvalidArgumentException('"$code" is empty');
        }

        if ($message === null && isset($this->_errorMessages[$code]))
        {
            $message = $this->_errorMessages[$code];
        }

        parent::__construct($message, $code, $previous);
    }

    public function isClient()
    {
        return intval($this->getCode() / 100) == 4;
    }

    public function isServer()
    {
        return intval($this->getCode() / 100) == 5;
    }

    public function getHeader()
    {
        return $this->_errorHeaders[$this->getCode()];
    }
}
