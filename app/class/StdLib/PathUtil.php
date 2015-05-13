<?php
/**
 * ファイルパス関連ユーティリティ
 *
 * @package   StdLib
 * 
 */

namespace GyazoHj\StdLib;

/**
 * ファイルパス関連ユーティリティ
 *
 * @package   StdLib
 * 
 */
class PathUtil extends \GyazoHj\StdLib\ObjectAbstract
{
    /**
     * 指定したパス文字列にルートが含まれているかどうかを返す
     *
     * @param string $path
     * @return boolean
     */
    public static function isPathRooted($path)
    {
        if (substr($path, 0, 1) == '/')
        {
            return true;
        }

        if (DIRECTORY_SEPARATOR === '\\')
        {
            if (preg_match('/^[a-z]+:(\\\\|\\/|$)/i', $path) !== 0)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * 複数のパスを1つのパスに結合する
     *
     * @param string $path
     * @param string ...
     *
     * @return string
     */
    public static function combine($path)
    {
        $args = func_get_args();

        $cnt = count($args);

        for ($i = $cnt - 1; $i > 0; $i--)
        {
            $path = $args[$i];

            // 絶対パス
            if (self::isPathRooted($path))
            {
                $args = array_splice($args, $i);
                break;
            }
        }

        // 空を除外
        $args = array_filter($args, 'strlen');

        $args = array_map(function($val) { return rtrim($val, '/'); }, $args);

        return implode('/', $args);
    }

    /**
     * あるファイルがあるディレクトリ内にあるか調べる
     *
     * @param string $filename ファイル名
     * @param string $directory ディレクトリ名
     * @return bool
     */
    public static function isInDirectory($filename, $directory = null)
    {
        if ($directory === null)
        {
            $directory = getcwd();
        }

        if (!self::isPathRooted($directory) || !self::isPathRooted($filename))
        {
            throw new \InvalidArgumentException('arguments must be absolute path');
        }

        $directory = realpath($directory);
        $filename = realpath($filename);

        if ($directory === false || $filename === false)
        {
            return false;
        }

        return strpos($filename, $directory) === 0;
    }

    /**
     * ファイルの拡張子を変更する。
     * pathinfoに準拠。例えば「filename.hoge.fuga」のような形式は「fuga」が変換対象になる。
     *
     * @param string $filename ファイル名
     * @param string $extension 拡張子。nullや空文字なら拡張子削除
     * @return string 拡張子変換後のファイル名
     */
    public static function changeExtension($filepath, $extension)
    {
        if (!StringUtil::isEmpty($extension))
        {
            $extension = '.' . ltrim($extension, '.');
        }

        $pathinfo = pathinfo($filepath);

        $basename = $pathinfo['filename'] . $extension;

        if ($pathinfo['dirname'] === '.')
        {
            return $basename;
        }

        return $pathinfo['dirname'] . DIRECTORY_SEPARATOR . $basename;
    }
}
