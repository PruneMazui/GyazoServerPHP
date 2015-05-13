<?php
namespace GyazoHj;

/**
 * ディレクトリにベンダー名が必要ないオートローダー
 *
 * 
 */
class AutoLoader
{
    /**
     * @var string
     */
    private $_prefix;

    /**
     * @var string
     */
    private $_directory;

    /**
     * @var string
     */
    private $_separator;

    /**
     * PSR-0 スタイルのオートローダーを登録する
     *
     * @param string $namespace
     * @param string $directory
     *
     * @return AutoLoader
     */
    public static function addNamespace($namespace, $directory)
    {
        return new self($namespace, $directory, '\\');
    }

    /**
     * PEAR スタイルのオートローダーを登録する
     *
     * @param string $prefix
     * @param string $directory
     *
     * @return AutoLoader
     */
    public static function addPearLegacy($prefix, $directory)
    {
        return new self($prefix, $directory, '_');
    }

    /**
     * コンストラクタ
     *
     * @param string $prefix
     * @param string $directory
     */
    private function __construct($prefix, $directory, $separator)
    {
        $this->_prefix = trim($prefix, $separator) . $separator;

        // 実パスに変換
        $directory = realpath($directory);

        if ($directory === false)
        {
            throw new \RuntimeException("directory notfound \"$directory\".");
        }

        // ディレクトリ終端の区切り文字を追加
        $directory = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $this->_directory = $directory;
        $this->_separator = $separator;

        spl_autoload_register(array($this, '_autoload'));
    }

    /**
     * 登録解除
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, '_autoload'));
    }

    /**
     * @param string $klass
     */
    private function _autoload($klass)
    {
        $klass = trim($klass, $this->_separator);

        if (strncmp($klass, $this->_prefix, strlen($this->_prefix)) === 0)
        {
            $name = substr($klass, strlen($this->_prefix));
            $name = str_replace($this->_separator, DIRECTORY_SEPARATOR, $name);

            $path = $this->_directory . $name . '.php';

            return include $path;
        }

        return false;
    }
}
