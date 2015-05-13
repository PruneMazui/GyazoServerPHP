<?php
namespace GyazoHj\StdLib\SmartyX;

/**
 * @package		SmartyX
 */

/**
 * 汎用的に使えそうなSmartyのプラグイン関数をまとめたクラス
 * Smartyに倣ってプロパティはpublicにする。
 */
class StandardExtend
{
    /**
     * 元となるインスタンスを保持しておくと何かと便利である。
     *
     * @var \Smarty
     */
    public $smarty = null;

    /**
     * {javascript}{stylesheet}で書き出すディレクトリを指定する。
     * Web用途であれば大抵はドキュメントルートになるはず(という意味で分かりやすい変数名にしている)。
     *
     * @var string
     */
    public $documentRoot = null;

    /**
     * 変数値を指定文字で囲って表示する
     *
     * @param string $text テンプレート変数値
     * @param string $default 空文字だった場合のデフォルト値
     * @param string $suffix サフィックス
     * @param string $prefix プレフィックス
     * @return string wrapされた文字列
     */
    public function modifier_wrap($text, $default = '', $suffix = null, $prefix = null)
    {
        if (intval($text) === 0)
        {
            return $default;
        }

        if ($prefix !== null)
        {
            $text = "{$prefix}{$text}";
        }

        if ($suffix !== null)
        {
            $text = "{$text}{$suffix}";
        }

        return $text;
    }

    /**
     * 数値を2進接頭辞変換する。
     *
     * @param string $number テンプレート変数値
     * @param int $decimals 小数点以下の文字数
     * @param array $units 単位配列
     * @param string $format フォーマット
     * @return string SI接頭辞が付加された数値
     */
    public function modifier_unitize($number, $decimals = 3, $format = '%s %s', $units = array())
    {
        if (strlen($number) === 0)
        {
            return $number;
        }

        if (count($units) == 0)
        {
            $units = str_split('KMGTPEZY');
        }

        $number = floatval($number);
        for (reset($units); current($units) !== false; next($units))
        {
            $number = $number / 1024;
            if ($number < 1024)
            {
                break;
            }
        }

        $number = number_format($number, $decimals);
        $unit = current($units);

        return sprintf($format, $number, $unit);
    }

    /**
     * 指定位置毎に文字列挿入(マルチバイト対応wordwrapに近い)
     * utf8のみ対応
     *
     * @param string $text 対象文字列
     * @param string $break 挿入文字
     * @param int $width 長さ
     * @param int $count 挿入回数。-1で制限なし
     * @return string 挿入された文字列
     */
    public function modifier_wordwrap($text, $break, $width, $count = -1)
    {
        if (!ctype_digit("$width"))
        {
            throw new \InvalidArgumentException('"width" is not digit');
        }

        $pattern = '@(.{' . $width . '})+?@u';
        $replace = '$1' . $break;

        return preg_replace($pattern, $replace, $text, $count);
    }

    /**
     * 3項演算子的な動作をする修飾子
     *
     * @param mixed $condition 条件
     * @param mixed $trueValue trueの時の値
     * @param mixed $falseValue falseの時の値。省略可
     * @return $condition ? $trueValue : $falseValue
     */
    public function modifier_which($condition, $trueValue, $falseValue = "")
    {
        return $condition ? $trueValue : $falseValue;
    }

    /**
     * 配列をlookupする
     *
     * @param string|integer $key リストのインデックス
     * @param array $master 配列
     * @return string 得られた文字列
     */
    public function modifier_lookup($key, $master, $default = null)
    {
        if (isset($master[$key]))
        {
            return $master[$key];
        }
        else if (!is_null($default))
        {
            return $default;
        }
        else
        {
            throw new \InvalidArgumentException("Undefined index: $key");
        }
    }

    /**
     * 現在設定されているテンプレート変数を元にクエリ文字列を生成する。
     *
     * @param array $names キー名
     * @return string クエリ文字列
     */
    public function modifier_query($names)
    {
        $names = (array) $names;

        $query = array();

        foreach ($names as $name)
        {
            if (!$this->smarty->hasTemplateVar($name))
            {
                throw new \InvalidArgumentException("'$name' is not set");
            }

            $query[$name] = $this->smarty->getTemplateVars($name);
        }

        return http_build_query($query);
    }

    /**
     * テンプレート変数をhtmlの属性化する。
     * いわゆる連想配列を渡した場合はキーを属性化したものを返す
     * いわゆる数値配列を渡した場合はそのキーとassignされている値を属性化したものを返す
     * いずれにせよ変数名として相応しくない場合はスキップされる
     *
     * @param mixed $var 変数、あるいは名前
     * @param array $filter 連想配列が渡された場合のキーフィルタ
     * @return string 属性文字列
     */
    public function modifier_attribute($var, $filter = array())
    {
        //引数は配列、あるいは単一の文字列のみ
        if (!is_array($var) && !is_string($var))
        {
            throw new \InvalidArgumentException("'$var' is not array or string");
        }

        //変数宣言
        $var = (array) $var;
        $filter = (array) $filter;
        $result = array();
        $pattern = '/^[a-z_][0-9a-z_\-]*$/i';

        //連想配列の場合、キーと値で変数宣言
        if ($this->_isHashArray($var))
        {
            foreach ($var as $name => $value)
            {
                if (!preg_match($pattern, $name))
                {
                    throw new \InvalidArgumentException("'$name' is invalid name");
                }
                if (empty($filter) || in_array($name, $filter))
                {
                    $result[] = $name . '="' . htmlspecialchars($value) . '"';
                }
            }
        }
        //数値配列の場合、値をキーにしてassignされている値で宣言
        else
        {
            foreach ($var as $name)
            {
                if (!preg_match($pattern, $name))
                {
                    throw new \InvalidArgumentException("'$name' is invalid name");
                }
                if (!$this->smarty->hasTemplateVar($name))
                {
                    throw new \InvalidArgumentException("'$name' is not set");
                }

                $value = $this->smarty->getTemplateVars($name);
                $result[] = $name . '="' . htmlspecialchars($value) . '"';
            }
        }

        if (empty($result))
        {
            return '';
        }

        return implode(' ', $result);
    }

    /**
     * テンプレート変数をjsに持ち込む。
     * 非推奨。使うとしたら1tplあたり1回くらいに留めるべき
     * いわゆる連想配列を渡した場合はキーで個別に変数が宣言される
     * いわゆる数値配列を渡した場合はそのキーとassignされている値で宣言される
     * いずれにせよ変数名として相応しくない場合はスキップされる
     *
     * @param mixed $var 変数、あるいは名前
     * @param bool $toUpper 大文字js変数名にするか。グローバルに宣言されるのでデフォルトtrue
     * @param string $prefix js変数名プレフィックス
     * @return string 宣言が含まれたscriptタグ
     */
    public function modifier_jsvar($var, $toUpper = true, $prefix = '')
    {
        //引数は配列、あるいは単一の文字列のみ
        if (!is_array($var) && !is_string($var))
        {
            throw new \InvalidArgumentException("'$var' is not array or string");
        }

        //js変数を宣言するクロージャ
        $tovar = function ($name, $value) use ($toUpper, $prefix)
        {
            $name = "{$prefix}{$name}";
            if ($toUpper)
            {
                $name = strtoupper($name);
            }

            $jop = (defined('JSON_UNESCAPED_UNICODE')) ? JSON_UNESCAPED_UNICODE : 0;
            $value = json_encode($value, $jop);

            return "var $name = $value;\n";
        };

        //変数宣言
        $var = (array) $var;
        $pattern = '/^[a-z_][0-9a-z_]*$/i';
        $result = '';

        //連想配列の場合、キーと値で変数宣言
        if ($this->_isHashArray($var))
        {
            foreach ($var as $name => $value)
            {
                if (preg_match($pattern, $name))
                {
                    $result .= $tovar($name, $value);
                }
            }
        }
        //数値配列の場合、値をキーにしてassignされている値で宣言
        else
        {
            foreach ($var as $name)
            {
                if (!$this->smarty->hasTemplateVar($name))
                {
                    throw new \InvalidArgumentException("'$name' is not set");
                }

                if (preg_match($pattern, $name))
                {
                    $result .= $tovar($name, $this->smarty->getTemplateVars($name));
                }
            }
        }

        if ($result === '')
        {
            return '';
        }

        return "<script type=\"text/javascript\">\n$result</script>";
    }

    /**
     * 配列が連想配列か判定する
     *
     * @param uarray $array 対象配列
     * @return boolean 連想配列ならtrue
     */
    private function _isHashArray($array)
    {
        $i = 0;
        foreach ($array as $k => $dummy)
        {
            if ($k !== $i++)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * _combineのjavascript用ショートカット
     */
    public function function_javascript($params, &$smarty)
    {
        if (!isset($params['template']))
        {
            $params['template'] = '<script type="text/javascript" src="{$file}"></script>';
        }
        return $this->_combine($params, $smarty);
    }

    /**
     * _combineのスタイルシート用ショートカット
     */
    public function function_stylesheet($params, &$smarty)
    {
        if (!isset($params['template']))
        {
            $params['template'] = '<link type="text/css" href="{$file}" media="all" rel="stylesheet" />';
        }
        return $this->_combine($params, $smarty);
    }

    /**
     * html_imageを簡易的に扱えるようにしたfunction
     * file(src)でキャッシュされるが、cache_idを渡せば任意の値をキーにキャッシュ可能
     *
     * @param array $params オプション
     * @param Smarty $smarty
     * @return string imgタグ
     */
    public function function_img($params, &$smarty)
    {
        static $cache = array();

        //alt指定時はtitleにも設定する
        if (isset($params['alt']) && !isset($params['title']))
        {
            $params['title'] = $params['alt'];
        }

        //srcはfileと同じ挙動にする
        if (isset($params['src']) && !isset($params['file']))
        {
            $params['file'] = $params['src'];
            unset($params['src']);
        }

        //キャッシュid未指定時はfileを使用する
        if (!isset($params['cache_id']))
        {
            $params['cache_id'] = $params['file'];
        }

        $cache_id = $params['cache_id'];
        unset($params['cache_id']);
        if (!isset($cache[$cache_id]))
        {
            require_once SMARTY_PLUGINS_DIR . 'function.html_image.php';
            $cache[$cache_id] = smarty_function_html_image($params, $smarty);
        }

        return $cache[$cache_id];
    }

    /**
     * html_optionsは融通がきかないので配列に基づいてoptionを吐き出すような処理を自作。
     *
     * optionsは必須。
     * selectタグは吐かない。
     *
     * @param array $params オプション
     * @return string optionタグ
     */
    public function function_options($params, &$smarty)
    {
        //引数チェック
        if (!isset($params['options']))
        {
            throw new \InvalidArgumentException('options is not set');
        }
        if (!is_array($params['options']))
        {
            throw new \InvalidArgumentException('options is not array');
        }
        if (!isset($params['selected']))
        {
            $params['selected'] = null;
        }

        //options
        $options = $params['options'];
        unset($params['options']);

        //値
        $selected = $params['selected'];
        unset($params['selected']);

        $pattern = '/^[a-z_][0-9a-z_\-]*$/i';

        //組み立て用クロージャ
        $createOption = function ($key, $text) use ($params, $selected, $pattern)
        {
            //optionタグの属性格納用変数
            $oattrs = array();

            //value属性は共通で付加
            $oattrs[] = 'value="' . htmlspecialchars($key) . '"';

            //selected属性は値が一致するときのみ付加
            if ($selected !== null && $key == $selected)
            {
                $oattrs[] = 'selected="selected"';
            }

            //引数で与えられた配列はoptionそれぞれの属性となる
            foreach ($params as $name => $value)
            {
                if (!preg_match($pattern, $name))
                {
                    throw new \InvalidArgumentException("'$name' is invalid name");
                }

                //配列だったら値が一致するもののみ属性付加する
                if (is_array($value))
                {
                    if (!isset($value[$key]))
                    {
                        continue;
                    }

                    $value = $value[$key];
                }

                $hval = htmlspecialchars(sprintf($value, $key), ENT_QUOTES);
                $oattrs[] = "$name=\"$hval\"";
            }

            return "<option " . implode(' ', $oattrs) . '>' . htmlspecialchars($text, ENT_QUOTES) . '</option>';
        };

        //optionタグの組立て
        $result = array();
        foreach ($options as $key => $text)
        {
            //普通のoption
            if (!is_array($text))
            {
                $result[] = $createOption($key, $text);
            }
            //optgroup
            else
            {
                $optgroup = '<optgroup label="' . htmlspecialchars($key, ENT_QUOTES) . '">';
                foreach ($text as $key2 => $text2)
                {
                    $optgroup .= $createOption($key2, $text2);
                }
                $result[] = $optgroup . '</optgroup>';
            }
        }

        return implode('', $result);
    }

    /**
     * 複数ファイルの結合処理。
     * 複数ファイルを結合したり、更新日時を付加したりする。
     * {javascript}{stylesheet}の実体。
     *
     * @param array $params オプション
     * @param Smarty $smarty
     * @return string タグリスト、あるいはファイルを結合した単一のタグ
     */
    private function _combine($params, &$smarty)
    {
        if (strlen($this->documentRoot) == 0)
        {
            throw new \UnexpectedValueException('$documentRoot is not set');
        }

        if (!isset($params['template']))
        {
            throw new \InvalidArgumentException('"template" key is not set');
        }

        if (!isset($params['files']))
        {
            throw new \InvalidArgumentException('"files" key is not set');
        }

        $params += array(
            'build'   => false,
            'enable'  => true,
            'combine' => "combine.js",
            'addtime' => true,
        );

        $build = $params['build'];
        $enable = $params['enable'];
        $combine = $params['combine'];
        $addtime = $params['addtime'];
        $template = $params['template'];
        $files = (array) $params['files'];

        //結合するなら生成
        if ($build)
        {
            $create = true;
            $filename = $this->documentRoot . $combine;

            //存在するなら全ファイルの内一つでも更新時間が上回っているとき再生成する
            if (is_file($filename))
            {
                $maxtime = 0;
                foreach ($files as $file)
                {
                    $_file = $this->documentRoot . $file;
                    $time = filemtime($_file);
                    if ($time > $maxtime)
                    {
                        $maxtime = $time;
                    }
                }

                $mtime = filemtime($filename);
                $create = ($mtime < $maxtime);
            }

            //作成
            if ($create)
            {
                $content = "";
                foreach ($files as $file)
                {
                    $_file = $this->documentRoot . $file;
                    $content .= file_get_contents($_file) . "\n";
                }
                file_put_contents($filename, $content);
            }
        }

        //有効なら結合ファイルを返す
        if ($enable)
        {
            $combine .= $addtime ? ('?' . filemtime($this->documentRoot . $combine)) : '';
            $smarty->assign('file', $combine);
            return $smarty->fetch("string:$template");
        }
        //有効でないならバラで返す
        else
        {
            $result = array();
            foreach ($files as $file)
            {
                $file .= $addtime ? ('?' . filemtime($this->documentRoot . $file)) : '';
                $smarty->assign('file', $file);
                $result[] = $smarty->fetch("string:$template");
            }
            return implode("\n", $result);
        }
    }

    /**
     * 空実装。コピペ用として残しておく。
     *
     * @param unknown_type $tag_arg
     * @param unknown_type $smarty
     */
    public function compiler_hogehoge($tag_arg, &$smarty)
    {
    }
}
