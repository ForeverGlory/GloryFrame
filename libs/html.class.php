<?php
/**
 * html.class.php   HTML类
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/libs/html.class.php
 * @version         $
 */
class html
{

    public function __construct()
    {
        static $isFirst = true;
        if($isFirst)
        {
           GloryFrame::Auto($this);
            $isFirst = false;
        }
    }

    /**
     * 输出 hidden 控件
     * 「demo」:1
     *      $data = array( "key1" => "val1" [, "key2" => "val2" ... ] )
     *      html  = <input type="hidden" name="{key1}" value="{val1}" />[<input type="hidden" name="{key2}" value="{val2}" /> ... ]
     * 「demo」:2
     *      $data = array( "key1" => array( "k1" => "v1" [, "k2" => "v2" ...]) [, "key2" => array( "k1" => "v1") ...])
     *      html  = <input type="hidden" name="{key1}[{k1}]" value="{v1}" />[<input type="hidden" name="{key1}[{k2}]" value="{v2}" /> ... <input type="hidden" name="{key2}[{k1}]" value="{v1}" /> ... ]
     * @param   array   $data   将数组转换成hidden控件
     * @return  string
     */
    public function hidden($data)
    {
        $html = "";
        if(is_array($data))
        {
            foreach($data as $key => $val)
            {
                if(is_array($val))
                {
                    foreach($val as $k => $v)
                    {
                        $html.="<input type=\"hidden\" name=\"{$key}[{$k}]\" value=\"{$v}\" />";
                    }
                }
                else
                {
                    $html.="<input type=\"hidden\" name=\"{$key}\" value=\"{$val}\" />";
                }
            }
        }
        return $html;
    }

    /**
     * 输出 input checkbox 控件
     * 「demo」
     * @param   array   $data
     * @param   string  $name
     * @param   type    $check  选中checkbox array/string
     * @param   type    $attr
     * @return  string  HTML
     */
    public function checkbox($data, $name, $check = null, $attr = null)
    {
        $html = "";
        if(is_array($data))
        {
            $check = strToArray(",", $check);
            foreach($data as $key => $val)
            {
                $checked = in_array($key, $check) ? " checked" : "";
                $html.="<label><input type=\"checkbox\" name=\"{$name}[]\" value=\"{$key}\"{$checked}";
                if($attr)
                {
                    if(is_array($attr))
                    {
                        foreach($attr as $k => $v)
                        {
                            $html.=" {$k}=\"{$v}\"";
                        }
                    }
                    else
                    {
                        $html.=" {$attr}";
                    }
                }
                $html.="><span>{$val}</span></label>";
            }
        }
        return $html;
    }

    public function select($data, $name, $select = null, $attr = null)
    {
        $html = "<select name=\"{$name}\"";
        if($attr)
        {
            if(is_array($attr))
            {
                foreach($attr as $key => $val)
                {
                    $html.=" {$key}=\"{$val}\"";
                }
            }
            else
            {
                $html.=" {$attr}";
            }
        }
        $html.=">";
        if(is_array($data))
        {
            foreach($data as $key => $val)
            {
                if(is_array($val))
                {
                    $html.="<optgroup label=\"{$key}\">";
                    foreach($val as $k => $v)
                    {
                        $selected = $select == $k ? " selected" : "";
                        $html.="<option value=\"{$k}\"{$selected}>{$v}</option>";
                    }
                    $html.="</optgroup>";
                }
                else
                {
                    $selected = $select == $key ? " selected" : "";
                    $html.="<option value=\"{$key}\"{$selected}>{$val}</option>";
                }
            }
        }
        $html.="</select>";
        return $html;
    }

}
