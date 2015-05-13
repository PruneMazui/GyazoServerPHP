<?php
namespace GyazoHj\StdLib\SmartyX;

use GyazoHj\StdLib\ArrayUtil;
use WebApply\Model\Material;

/**
 * Smarty拡張
 */
class SmartyExtend
{
    /**
     * 読み込み済みファイルのリスト
     *
     * @var array
     */
    private $_fileList = array();

    /**
     * @var Smarty
     */
    private $_smarty;

    public function __construct(\Smarty $smarty)
    {
        $this->_smarty = $smarty;
    }

    /**
     * ファンクション - import
     *
     * 指定テンプレートと.js、.phpも同時に読み込む
     *
     * @param array $params
     * @param Smarty $smarty
     */
    public function function_import($params, &$smarty)
    {
        if (!isset($params['file']))
        {
            throw new \InvalidArgumentException('"file" is not set');
        }

        $file = ArrayUtil::unsetKey($params, 'file');
        $once = ArrayUtil::unsetKey($params, 'once');
        $view = $smarty->getTemplateVars('view');

        //一度きりで既に読んでるなら何もしない
        if ($once === true && isset($this->_fileList[$file]))
        {
            return;
        }

        $this->_fileList[$file] = true;

        foreach ($params as $var => $value)
        {
            $view->$var = $value;
        }

        return $view->render($file);
    }

    /**
     * ログの時間表示
     *
     * @param	mixed	$log_date
     * @return	string
     */
    public function modifier_log_date($log_date)
    {
        if (strlen($log_date) === 0)
        {
            // 空文字ならそのまま
            return "";
        }
        else if (is_numeric($log_date) === false)
        {
            // 非数値なら日付文字列としてみる
            $sec = strtotime($log_date);

            if ($sec === false)
            {
                // 謎の値ならそのまま表示する
                return $log_date;
            }

            return date('Y/m/d H:i:s', $sec) . ".000";
        }
        else
        {
            $ftime = (float) $log_date;

            $ftime = (int) ($ftime * 1000);

            $sec = (int) ($ftime / 1000);

            $msec = (int) ($ftime % 1000);

            return date('Y/m/d H:i:s', $sec) . sprintf(".%03d", $msec);
        }
    }

    /**
     * 素材コードの表示
     *
     * @param   mixed $material_cd
     * @return  string
     */
    public function modifier_material_cd($material_cd)
    {
        return Material::formatMaterialCd($material_cd);
    }

    /**
     * クロージャーを修飾子として使用するための修飾子
     *
     * e.g.)
     *     view.php
     *       $view->func = function ($val, $arg1, $arg2) { return "..."; };
     *     view.tpl
     *       {$val|call:$func:$arg1:$arg2}
     *
     * @param   mixed $val
     * @param   callable $func
     * @return  string
     */
    public function modifier_call($val)
    {
        $args = func_get_args();

        if (is_object($val) && $val instanceof \Closure)
        {
            array_shift($args);
            return call_user_func_array($val, $args);
        }
        else
        {
            list($func) = array_splice($args, 1, 1);
            return call_user_func_array($func, $args);
        }
    }

    /**
     * smarty_modifier_date_format へのプロキシ
     */
    private function _date_format_args($args)
    {
        $func = 'smarty_modifier_date_format';

        if (!function_exists($func))
        {
            $this->_smarty->loadPlugin($func);
        }

        return call_user_func_array($func, $args);
    }

    /**
     * 日時書式修飾子 yyyy/mm/dd hh:mm:ss
     *
     * @param string $string
     * @param string $format
     */
    public function modifier_date_ymdhms($string, $format = null)
    {
        if ($format === null)
        {
            $format = "%Y/%m/%d %H:%M:%S";
        }

        return $this->_date_format_args([$string, $format] + func_get_args());
    }


    /**
     * 日時書式修飾子 yyyy/mm/dd hh:mm
     *
     * @param string $string
     * @param string $format
     */
    public function modifier_date_ymdhm($string, $format = null)
    {
        if ($format === null)
        {
            $format = "%Y/%m/%d %H:%M";
        }

        return $this->_date_format_args([$string, $format] + func_get_args());
    }
}
