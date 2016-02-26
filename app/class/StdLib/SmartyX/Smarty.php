<?php
namespace GyazoPhp\StdLib\SmartyX;
/**
 * @package		SmartyX
 */

/**
 * 拡張オブジェクトを保持したSmartyクラス
 */
class Smarty extends \Smarty
{
    /**
     * 拡張オブジェクト
     *
     * @var Extender
     */
    private $_extender = null;

    /**
     * コンストラクタ
     *
     * @param $options オプション配列
     */
    public function __construct($options = array())
    {
        parent::__construct();

        $this->_extender = new Extender($this);

        //StandardExtendを追加しておく
        $standard = new StandardExtend();
        $standard->smarty = $this;
        $standard->documentRoot = isset($options['documentRoot']) ? $options['documentRoot'] : null;
        $this->register($standard);
    }

    /**
     * 全registerを実行。通常はこれを呼ぶだけで良い
     */
    public function register($exntend)
    {
        $this->_extender->registerPlugin($exntend);
        $this->_extender->registerResource($exntend);
    }

    /**
     * 変数がセットされているか調べる
     *
     * @param string  $varname        variable name
     * @param string  $_ptr           optional pointer to data object
     * @param boolean $search_parents include parent templates?
     */
    public function hasTemplateVar($varname, $_ptr = null, $search_parents = true)
    {
        $v = $this->getVariable($varname, $_ptr, $search_parents);
        return !($v instanceof \Undefined_Smarty_Variable);
    }
}
