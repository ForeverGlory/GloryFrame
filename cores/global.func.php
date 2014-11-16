<?php
/**
 * global.func.php  公共函数
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/cores/global.func.php
 * @version         $
 */

/**
 * 比较多个参数，权重在前面
 * 为空值时，跳到下一参数
 */
function compareArgs()
{
    $args = func_get_args();
    foreach($args as $v)
    {
        if(!empty($v))
        {
            return $v;
        }
    }
}



/**
 * 返回分页路径
 *
 * @param $urlrule 分页规则
 * @param $page 当前页
 * @param $array 需要传递的数组，用于增加额外的方法
 * @return 完整的URL路径
 */
function pageurl($urlrule, $page, $array = array())
{
    if(strpos($urlrule, '~'))
    {
        $urlrules = explode('~', $urlrule);
        $urlrule = $page < 2 ? $urlrules[0] : $urlrules[1];
    }
    $findme = array('{$page}');
    $replaceme = array($page);
    if(is_array($array))
    {
        foreach($array as $k => $v)
        {
            $findme[] = '{$' . $k . '}';
            $replaceme[] = $v;
        }
    }
    $url = str_replace($findme, $replaceme, $urlrule);
    $url = str_replace(array('http://', '//', '~'), array('~', '/', 'http://'), $url);
    return $url;
}

/**
 * URL路径解析，pages 函数的辅助函数
 *
 * @param $par 传入需要解析的变量 默认为，page={$page}
 * @param $url URL地址
 * @return URL
 */
function url_par($par, $url = '')
{
    if($url == '')
        $url = get_url();
    $pos = strpos($url, '?');
    if($pos === false)
    {
        $url .= '?' . $par;
    }
    else
    {
        $querystring = substr(strstr($url, '?'), 1);
        parse_str($querystring, $pars);
        $query_array = array();
        foreach($pars as $k => $v)
        {
            $query_array[$k] = $v;
        }
        $querystring = http_build_query($query_array) . '&' . $par;
        $url = substr($url, 0, $pos) . '?' . $querystring;
    }
    return $url;
}

/**
 * 精简代码
 * // ..//..
 * / *.....* /
 * <!-- ..... --> 不包括<!--[IE]-->
 * \f\n\r\t\v
 * */
function compactCode($str)
{
    $str = trim($str);
    $str = preg_replace("/([^:\\\])\/\/.*/", "\\1", $str);
    $str = preg_replace("/\/\*.*\*\//Us", "", $str);
    $str = preg_replace("/<!--[^\[]{1}[^i]{1}[^f]{1}[^ ]{1}[^I]{1}[^E]{1}[^\]]{1}.*-->/Us", "", $str);
    $str = preg_replace("/[\f\r\t\v]/", "", $str);
    $str = preg_replace("/[\n]/", "", $str);
    //$str = str_replace("    ","",$str);
    $str = preg_replace("/([ ]{2,})/", "", $str);
    return $str;
}

?>