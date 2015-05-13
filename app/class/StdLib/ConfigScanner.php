<?php
namespace GyazoHj\StdLib;

/**
 * 設定ファイルスキャナ
 *
 * 
 */
class ConfigScanner
{
    private $_hostname;

    /**
     * コンストラクタ
     */
    public function __construct($hostname = null)
    {
        $this->_hostname = $hostname ?: strtolower(php_uname('n'));
    }

    /**
     * 指定されたディレクトリからホスト名を元に設定ファイルを検索する
     *
     * ファイル名はホスト名をドット区切りの逆順にして拡張子 .php を付与したもの
     *
     *   e.g.) hoge.example.jp → jp.example.hoge.php
     *
     * 設定ファイルが見つかるまでホスト名のパラグラフを先頭から順番に除去して検索を繰り返す
     *
     *   1. jp.example.hoge.php
     *   2. jp.example.php
     *   3. jp.php
     *
     * 複数のディレクトリが指定された場合、ディレクトリごとに上記のルールでファイルを検索して
     * 見つからなかった場合に次のディレクトリが検索される
     * そのため、最初のディレクトリで jp.php のような名前の短いファイルが存在した場合に
     * ２番目のディレクトリにもっと長いファイル名があっても検索されない
     *
     * １番目が本番環境用、２番目が開発環境用、のような使い方を想定しているため
     * 最初のディレクトリにファイルがある場合の動作に最適化したため
     *
     *   e.g.) ホスト名が example.jp でディレクトリで 01/ 02/ を指定した場合 01/jp.php が検索される
     *
     *     01/jp.php
     *     02/jp.example.php
     *
     * @param array $dirs
     * @return string|false
     */
    public function scan(array $dirs)
    {
        // ホスト名を逆順にする
        $namepath = explode(".", trim($this->_hostname));
        $namepath = array_reverse($namepath);

        foreach ($dirs as $dir)
        {
            $fn = $this->_scan($dir, $namepath);

            if ($fn !== false)
            {
                return $fn;
            }
        }

        return false;
    }

    private function _scan($dir, $namepath)
    {
        while (count($namepath) > 0)
        {
            $dn = implode('.', $namepath);

            $fn = $dir . DIRECTORY_SEPARATOR . $dn . '.php';

            if (file_exists($fn))
            {
                return $fn;
            }

            array_pop($namepath);
        }

        return false;
    }
}
