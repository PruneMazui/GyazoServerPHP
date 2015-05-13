<?php
/**
 * ファイルシステム関連ユーティリティ
 *
 * @package   StdLib
 */

namespace GyazoHj\StdLib;

/**
 * ファイルシステムユーティリティ
 *
 * @package   StdLib
 */
class FileSystemUtil extends \GyazoHj\StdLib\ObjectAbstract
{
    /**
     * 書込み可能ディレクトリの作成（モード0777固定）
     * 既にあるなら何もしない。ただしそれが書き込み可能でないなら例外を投げる
     * mkdir -p
     *
     * @param string $directory 作成ディレクトリ名
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    public static function makeWritableDirectory($directory)
    {
        if (!strlen($directory))
        {
            throw new \InvalidArgumentException('Directory path string is required');
        }

        if (!file_exists($directory))
        {
            mkdir($directory, 0777, true);
            chmod($directory, 0777);
        }

        if (!is_dir($directory))
        {
            throw new \UnexpectedValueException("{$directory} is not directory");
        }

        if (!is_writable($directory))
        {
            // @codeCoverageIgnoreStart
            throw new \UnexpectedValueException("Directory is not writable: {$directory}");
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * ディレクトリを中身も含めて削除する。
     * rm -rf
     *
     * @param $dirname 削除するディレクトリ。
     * @throws UnexpectedValueException
     */
    public static function removeDirectory($dirname)
    {
        if (!file_exists($dirname))
        {
            return;
        }

        if (is_file($dirname))
        {
            if (!unlink($dirname))
            {
                // @codeCoverageIgnoreStart
                throw new \UnexpectedValueException("failed to remove {$dirname}");
                // @codeCoverageIgnoreEnd
            }
        }
        else
        {
            $names = scandir($dirname);
            foreach ($names as $fname)
            {
                if ($fname !== '.' && $fname !== '..')
                {
                    self::removeDirectory($dirname . DIRECTORY_SEPARATOR . $fname);
                }
            }

            if (!rmdir($dirname))
            {
                // @codeCoverageIgnoreStart
                throw new \UnexpectedValueException("failed to remove {$dirname}");
                // @codeCoverageIgnoreEnd
            }
        }
    }

    /**
     * 再帰的なglob
     *
     * @param string $pattern 検索パターン
     * @param int $flags GLOB_*
     * @throws \UnexpectedValueException
     * @return array 検索結果
     */
    public static function recursiveGlob($pattern, $flags = 0)
    {
        $DS = DIRECTORY_SEPARATOR;

        //自身の保持ファイル
        $files = glob($pattern, $flags);
        if ($files === false)
        {
            throw new \UnexpectedValueException('failed to glob');
        }

        //自身の保持ディレクトリ
        $dirs = glob(dirname($pattern) . "{$DS}*", GLOB_ONLYDIR | GLOB_NOSORT);

        //に対して再帰実行
        foreach ($dirs as $dir)
        {
            $target = $dir . $DS . basename($pattern);
            $files = array_merge($files, self::recursiveGlob($target, $flags));
        }

        return $files;
    }
}
