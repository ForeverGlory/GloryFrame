<?php

/**
 *  global.func.php 公共函数
 *
 * @author			foreverglory@qq.com
 * @license			http://ys20.cn
 * @lastmodify		2011-4-22
 **/
/**
 * 输出自定义错误
 *
 * @param $errno 错误号
 * @param $errstr 错误描述
 * @param $errfile 报错文件地址
 * @param $errline 错误行号
 * @return string 错误提示
 */
function my_error_handler($errno, $errstr, $errfile, $errline)
{
    if ($errno == 8)
        return '';
    //替换文件在服务器上的位置，使用相对地址
    $errfile = str_replace(YS_PATH, '', $errfile);
    if (ys_base::load_config('system', 'errorlog'))
    {
        error_log('<?php exit();?>' . date('m-d H:i:s', SYS_TIME) . ' | ' . $errno . ' | ' . str_pad($errstr, 30) . ' | ' . $errfile . ' | ' . $errline . "\r\n", 3, CACHE_PATH . 'error' . DIRECTORY_SEPARATOR . 'error_log_' . date("Y_m_d", time()) . '.php');
    }
    else
    {
        $str = '<div style="font-size:12px;text-align:left; border-bottom:1px solid #9cc9e0; border-right:1px solid #9cc9e0;padding:1px 4px;color:#000000;font-family:Arial, Helvetica,sans-serif;"><span>errorno:' . $errno . ',str:' . $errstr . ',file:<font color="red">' . $errfile . '</font>,line' . $errline . '</span></div>';
        echo $str;
    }
}

/**
 * 返回经addslashes处理过的字符串或数组
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function new_addslashes($string)
{
    if (!is_array($string))
        return addslashes(sbc_dbc($string));
    foreach ($string as $key => $val)
        $string[$key] = new_addslashes($val);
    return $string;
}

/**
 * 返回经stripslashes处理过的字符串或数组
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function new_stripslashes($string)
{
    if (!is_array($string))
        return stripslashes($string);
    foreach ($string as $key => $val)
        $string[$key] = new_stripslashes($val);
    return $string;
}

/**
 * 返回经addslashe处理过的字符串或数组
 * @param $obj 需要处理的字符串或数组
 * @return mixed
 */
function new_html_special_chars($string)
{
    if (!is_array($string))
        return htmlspecialchars($string);
    foreach ($string as $key => $val)
        $string[$key] = new_html_special_chars($val);
    return $string;
}
/**
 * 安全过滤函数
 *
 * @param $string
 * @return string
 */
function safe_replace($string)
{
    $string = str_replace('%20', '', $string);
    $string = str_replace('%27', '', $string);
    $string = str_replace('%2527', '', $string);
    $string = str_replace('*', '', $string);
    $string = str_replace('"', '&quot;', $string);
    $string = str_replace("'", '', $string);
    $string = str_replace('"', '', $string);
    $string = str_replace(';', '', $string);
    $string = str_replace('<', '&lt;', $string);
    $string = str_replace('>', '&gt;', $string);
    $string = str_replace("{", '', $string);
    $string = str_replace('}', '', $string);
    return $string;
}
/**
 * 圆角转半角字符
 **/
function sbc_dbc($string){
    $Queue = Array('０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4','５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9','Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E','Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J','Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O','Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T','Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y','Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd','ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i','ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n','ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's','ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x','ｙ' => 'y', 'ｚ' => 'z', '－' => '-');
    $string=strtr($string,$Queue);
    return $string;
}
/**
 * 过滤ASCII码从0-28的控制字符
 * @return String
 */
function trim_unsafe_control_chars($str)
{
    $rule = '/[' . chr(1) . '-' . chr(8) . chr(11) . '-' . chr(12) . chr(14) . '-' . chr(31) . ']*/';
    return str_replace(chr(0), '', preg_replace($rule, '', $str));
}

/**
 * 格式化文本域内容
 *
 * @param $string 文本域内容
 * @return string
 */
function trim_textarea($string)
{
    $string = nl2br(str_replace(' ', '&nbsp;', $string));
    return $string;
}

/**
 * 将文本格式成适合js输出的字符串
 * @param string $string 需要处理的字符串
 * @param intval $isjs 是否执行字符串格式化，默认为执行
 * @return string 处理后的字符串
 */
function format_js($string, $isjs = 1)
{
    $string = addslashes(str_replace(array("\r", "\n"), array('', ''), $string));
    return $isjs ? 'document.write("' . $string . '");' : $string;
}

/**
 * 转义 javascript 代码标记
 *
 * @param $str
 * @return mixed
 */
function trim_script($str)
{
    $str = preg_replace('/\<([\/]?)script([^\>]*?)\>/si', '&lt;\\1script\\2&gt;', $str);
    $str = preg_replace('/\<([\/]?)iframe([^\>]*?)\>/si', '&lt;\\1iframe\\2&gt;', $str);
    $str = preg_replace('/\<([\/]?)frame([^\>]*?)\>/si', '&lt;\\1frame\\2&gt;', $str);
    $str = preg_replace('/]]\>/si', ']] >', $str);
    return $str;
}
/**
 * 获取当前页面完整URL地址
 */
function get_url()
{
    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? safe_replace($_SERVER['PHP_SELF']) : safe_replace($_SERVER['SCRIPT_NAME']);
    $path_info = isset($_SERVER['PATH_INFO']) ? safe_replace($_SERVER['PATH_INFO']) : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? safe_replace($_SERVER['REQUEST_URI']) : $php_self . (isset($_SERVER['QUERY_STRING']) ? '?' . safe_replace($_SERVER['QUERY_STRING']) : $path_info);
    return $sys_protocal . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $relate_url;
}
/**
 * 字符截取 支持UTF8/GBK
 * @param $string
 * @param $length
 * @param $dot
 */
function str_cut($string, $length, $dot = '...')
{
    $strlen = strlen($string);
    if ($strlen <= $length)
        return $string;
    //$string = str_replace(array(' ', '&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array('∵', ' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $string);
    $strcut = '';
    if (strtolower(CHARSET) == 'utf-8')
    {
        $length = intval($length - strlen($dot) - $length / 3);
        $n = $tn = $noc = 0;
        while ($n < strlen($string))
        {
            $t = ord($string[$n]);
            if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126))
            {
                $tn = 1;
                $n++;
                $noc++;
            } elseif (194 <= $t && $t <= 223)
            {
                $tn = 2;
                $n += 2;
                $noc += 2;
            } elseif (224 <= $t && $t <= 239)
            {
                $tn = 3;
                $n += 3;
                $noc += 2;
            } elseif (240 <= $t && $t <= 247)
            {
                $tn = 4;
                $n += 4;
                $noc += 2;
            } elseif (248 <= $t && $t <= 251)
            {
                $tn = 5;
                $n += 5;
                $noc += 2;
            } elseif ($t == 252 || $t == 253)
            {
                $tn = 6;
                $n += 6;
                $noc += 2;
            }
            else
            {
                $n++;
            }
            if ($noc >= $length)
            {
                break;
            }
        }
        if ($noc > $length)
        {
            $n -= $tn;
        }
        $strcut = substr($string, 0, $n);
        //$strcut = str_replace(array('∵', ' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), array(' ', '&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), $strcut);
    }
    else
    {
        $dotlen = strlen($dot);
        $maxi = $length - $dotlen - 1;
        $current_str = '';
        $search_arr = array('&', ' ', '"', "'", '“', '”', '—', '<', '>', '·', '…', '∵');
        $replace_arr = array('&amp;', '&nbsp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;', ' ');
        $search_flip = array_flip($search_arr);
        for ($i = 0; $i < $maxi; $i++)
        {
            $current_str = ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];
            if (in_array($current_str, $search_arr))
            {
                $key = $search_flip[$current_str];
                //$current_str = str_replace($search_arr[$key], $replace_arr[$key], $current_str);
            }
            $strcut .= $current_str;
        }
    }
    return $strcut . $dot;
}


/**
 * 获取请求ip
 *
 * @return ip地址
 */
function ip()
{
    if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown'))
    {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown'))
    {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown'))
    {
        $ip = getenv('REMOTE_ADDR');
    } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown'))
    {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return preg_match('/[\d\.]{7,15}/', $ip, $matches) ? $matches[0] : '';
}

function get_cost_time()
{
    $microtime = microtime(true);
    return $microtime - SYS_START_TIME;
}
/**
 * 程序执行时间
 *
 * @return	int	单位ms
 */
function execute_time()
{
    $stime = explode(' ', SYS_START_TIME);
    $etime = explode(' ', microtime());
    return number_format(($etime[1] + $etime[0] - $stime[1] - $stime[0]), 6);
}

/**
 * 产生随机字符串
 *
 * @param    int        $length  输出长度
 * @param    string     $chars   可选的 ，默认为 0123456789
 * @return   string     字符串
 */
function random($length, $chars = '0123456789')
{
    $hash = '';
    $max = strlen($chars) - 1;
    for ($i = 0; $i < $length; $i++)
    {
        $hash .= $chars[mt_rand(0, $max)];
    }
    return $hash;
}

/**
 * 将字符串转换为数组
 *
 * @param	string	$data	字符串
 * @return	array	返回数组格式，如果，data为空，则返回空数组
 */
function string2array($data)
{
    if ($data == '')
        return array();
    eval("\$array = $data;");
    return $array;
}
/**
 * 将数组转换为字符串
 *
 * @param	array	$data		数组
 * @param	bool	$isformdata	如果为0，则不使用new_stripslashes处理，可选参数，默认为1
 * @return	string	返回字符串，如果，data为空，则返回空
 */
function array2string($data, $isformdata = 1)
{
    if ($data == '')
        return '';
    if ($isformdata)
        $data = new_stripslashes($data);
    return addslashes(var_export($data, true));
}

/**
 * 转换字节数为其他单位
 *
 *
 * @param	string	$filesize	字节大小
 * @return	string	返回大小
 */
function sizecount($filesize)
{
    if ($filesize >= 1073741824)
    {
        $filesize = round($filesize / 1073741824 * 100) / 100 . ' GB';
    } elseif ($filesize >= 1048576)
    {
        $filesize = round($filesize / 1048576 * 100) / 100 . ' MB';
    } elseif ($filesize >= 1024)
    {
        $filesize = round($filesize / 1024 * 100) / 100 . ' KB';
    }
    else
    {
        $filesize = $filesize . ' Bytes';
    }
    return $filesize;
}
/**
 * 字符串加密、解密函数
 *
 *
 * @param	string	$txt		字符串
 * @param	string	$operation	ENCODE为加密，DECODE为解密，可选参数，默认为ENCODE，
 * @param	string	$key		密钥：数字、字母、下划线
 * @return	string
 */
function sys_auth($txt, $operation = 'ENCODE', $key = '')
{
    $key = $key ? $key : ys_base::load_config('system', 'auth_key');
    $txt = $operation == 'ENCODE' ? (string )$txt : base64_decode($txt);
    $len = strlen($key);
    $code = '';
    for ($i = 0; $i < strlen($txt); $i++)
    {
        $k = $i % $len;
        $code .= $txt[$i] ^ $key[$k];
    }
    $code = $operation == 'DECODE' ? $code : base64_encode($code);
    return $code;
}

/**
 * 语言文件处理
 *
 * @param	string		$language	标示符
 * @param	array		$pars	转义的数组,二维数组 ,'key1'=>'value1','key2'=>'value2',
 * @param	string		$modules 多个模块之间用半角逗号隔开，如：member,guestbook
 * @return	string		语言字符
 */
function L($language = 'no_language', $pars = array(), $modules = '')
{
    static $LANG = array();
    static $LANG_MODULES = array();
    $lang = ys_base::load_config('system', 'lang');
    if (!$LANG)
    {
        require_once PC_PATH . 'languages' . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . 'system.lang.php';
        if (defined('IN_ADMIN'))
            require_once PC_PATH . 'languages' . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . 'system_menu.lang.php';
        if (file_exists(PC_PATH . 'languages' . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . ROUTE_M . '.lang.php'))
            require PC_PATH . 'languages' . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . ROUTE_M . '.lang.php';
    }
    if (!empty($modules))
    {
        $modules = explode(',', $modules);
        foreach ($modules as $m)
        {
            if (!isset($LANG_MODULES[$m]))
                require PC_PATH . 'languages' . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . $m . '.lang.php';
        }
    }
    if (!array_key_exists($language, $LANG))
    {
        return $LANG['no_language'] . '[' . $language . ']';
    }
    else
    {
        $language = $LANG[$language];
        if ($pars)
        {
            foreach ($pars as $_k => $_v)
            {
                $language = str_replace('{' . $_k . '}', $_v, $language);
            }
        }
        return $language;
    }
}

/**
 * 模板调用
 *
 * @param $module
 * @param $template
 * @param $istag
 * @return unknown_type
 */
function template($template = 'index', $return = 'true')
{
    $template_cache = ys_base::load_sys_class('template_cache');
    $compiledtplfile = YS_PATH . 'caches' . DIRECTORY_SEPARATOR . 'caches_template' . DIRECTORY_SEPARATOR . $template . '.php';
    $templatefile = FR_PATH . 'templates' . DIRECTORY_SEPARATOR . $template . '.php';
    $template_cache->template_compile($template, $return);
//    if (file_exists($templatefile))
//    {
//        if (!file_exists($compiledtplfile) || (@filemtime($templatefile) > @filemtime($compiledtplfile)))
//        {
//            $template_cache->template_compile($template, $return);
//        }
//    }
//    else
//    {
//        if (!file_exists($compiledtplfile) || (@filemtime($templatefile) > @filemtime($compiledtplfile)))
//        {
//            $template_cache->template_compile($template, $return);
//        }
//    }
    return $compiledtplfile;
}

/**
 * 提示信息页面跳转，跳转地址如果传入数组，页面会提示多个地址供用户选择，默认跳转地址为数组的第一个值，时间为5秒。
 * showmessage('登录成功', array('默认跳转地址'=>'http://www.geliest.com'));
 * @param string $msg 提示信息
 * @param mixed(string/array) $url_forward 跳转地址
 * @param int $ms 跳转等待时间
 */
function showmessage($msg, $url_forward = 'goback', $ms = 1250, $dialog = '', $returnjs = '')
{
    include template("message");
    exit;
}
/**
 * 查询字符是否存在于某字符串
 *
 * @param $haystack 字符串
 * @param $needle 要查找的字符
 * @return bool
 */
function str_exists($haystack, $needle)
{
    return !(strpos($haystack, $needle) === false);
}

/**
 * 取得文件扩展
 *
 * @param $filename 文件名
 * @return 扩展名
 */
function fileext($filename)
{
    return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
}

/**
 * 加载模板标签缓存
 * @param string $name 缓存名
 * @param integer $times 缓存时间
 */
function tpl_cache($name, $times = 0)
{
    $filepath = 'tpl_data';
    $info = getcacheinfo($name, $filepath);
    if (SYS_TIME - $info['filemtime'] >= $times)
    {
        return false;
    }
    else
    {
        return getcache($name, $filepath);
    }
}

/**
 * 写入缓存，默认为文件缓存，不加载缓存配置。
 * @param $name 缓存名称
 * @param $data 缓存数据
 * @param $type 缓存类型[array,serialize,string]
 * @param $filepath 数据路径 caches/
 */
function setcache($name, $data, $type = 'array', $filepath = '')
{
    $cache = ys_base::load_sys_class('caches');
    return $cache->set($name, $data, $type, $filepath);
}

/**
 * 读取缓存，默认为文件缓存，不加载缓存配置。
 * @param string $name 缓存名称
 * @param $type 缓存类型[array,serialize,string]
 * @param $filepath 数据路径 caches/
 */
function getcache($name, $type = 'array', $filepath = '')
{
    $cache = ys_base::load_sys_class('caches');
    return $cache->get($name, $type, $filepath);
}

/**
 * 删除缓存，默认为文件缓存，不加载缓存配置。
 * @param $name 缓存名称
 * @param $filepath 数据路径 caches/
 */
function delcache($name, $filepath = '')
{
    $cache = ys_base::load_sys_class('caches');
    return $cache->delete($name, $filepath);
}

/**
 * 读取缓存，默认为文件缓存，不加载缓存配置。
 * @param string $name 缓存名称
 * @param $filepath 数据路径（模块名称） caches/cache_$filepath/
 * @param string $config 配置名称
 */
function getcacheinfo($name, $filepath = '', $type = 'file', $config = '')
{
    $cache = ys_base::load_sys_class('caches');
    return $cache->cacheinfo($name, $filepath);
}

/**
 * 生成sql语句，如果传入$in_cloumn 生成格式为 IN('a', 'b', 'c')
 * @param $data 条件数组或者字符串
 * @param $front 连接符
 * @param $in_column 字段名称
 * @return string
 */
function to_sqls($data, $front = ' AND ', $in_column = false)
{
    if ($in_column && is_array($data))
    {
        $ids = '\'' . implode('\',\'', $data) . '\'';
        $sql = "$in_column IN ($ids)";
        return $sql;
    }
    else
    {
        if ($front == '')
        {
            $front = ' AND ';
        }
        if (is_array($data) && count($data) > 0)
        {
            $sql = '';
            foreach ($data as $key => $val)
            {
                $sql .= $sql ? " $front `$key` = '$val' " : " `$key` = '$val' ";
            }
            return $sql;
        }
        else
        {
            return $data;
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
    if (strpos($urlrule, '~'))
    {
        $urlrules = explode('~', $urlrule);
        $urlrule = $page < 2 ? $urlrules[0] : $urlrules[1];
    }
    $findme = array('{$page}');
    $replaceme = array($page);
    if (is_array($array))
        foreach ($array as $k => $v)
        {
            $findme[] = '{$' . $k . '}';
            $replaceme[] = $v;
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
    if ($url == '')
        $url = get_url();
    $pos = strpos($url, '?');
    if ($pos === false)
    {
        $url .= '?' . $par;
    }
    else
    {
        $querystring = substr(strstr($url, '?'), 1);
        parse_str($querystring, $pars);
        $query_array = array();
        foreach ($pars as $k => $v)
        {
            $query_array[$k] = $v;
        }
        $querystring = http_build_query($query_array) . '&' . $par;
        $url = substr($url, 0, $pos) . '?' . $querystring;
    }
    return $url;
}

/**
 * 判断email格式是否正确
 * @param $email
 */
function is_email($email)
{
    return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}

/**
 * iconv 编辑转换
 */
if (!function_exists('iconv'))
{
    function iconv($in_charset, $out_charset, $str)
    {
        $in_charset = strtoupper($in_charset);
        $out_charset = strtoupper($out_charset);
        if (function_exists('mb_convert_encoding'))
        {
            return mb_convert_encoding($str, $out_charset, $in_charset);
        }
        else
        {
            ys_base::load_sys_func('iconv');
            $in_charset = strtoupper($in_charset);
            $out_charset = strtoupper($out_charset);
            if ($in_charset == 'UTF-8' && ($out_charset == 'GBK' || $out_charset == 'GB2312'))
            {
                return utf8_to_gbk($str);
            }
            if (($in_charset == 'GBK' || $in_charset == 'GB2312') && $out_charset == 'UTF-8')
            {
                return gbk_to_utf8($str);
            }
            return $str;
        }
    }
}


/**
 * IE浏览器判断
 */

function is_ie()
{
    $useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
    if ((strpos($useragent, 'opera') !== false) || (strpos($useragent, 'konqueror') !== false))
        return false;
    if (strpos($useragent, 'msie ') !== false)
        return true;
    return false;
}


/**
 * 文件下载
 * @param $filepath 文件路径
 * @param $filename 文件名称
 */

function file_down($filepath, $filename = '')
{
    if (!$filename)
        $filename = basename($filepath);
    if (is_ie())
        $filename = rawurlencode($filename);
    $filetype = fileext($filename);
    $filesize = sprintf("%u", filesize($filepath));
    if (ob_get_length() !== false)
        @ob_end_clean();
    header('Pragma: public');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: pre-check=0, post-check=0, max-age=0');
    header('Content-Transfer-Encoding: binary');
    header('Content-Encoding: none');
    header('Content-type: ' . $filetype);
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-length: ' . $filesize);
    readfile($filepath);
    exit;
}

/**
 * 判断字符串是否为utf8编码，英文和半角字符返回ture
 * @param $string
 * @return bool
 */
function is_utf8($string)
{
    return preg_match('%^(?:
					[\x09\x0A\x0D\x20-\x7E] # ASCII
					| [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
					| \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
					| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
					| \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
					| \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
					| [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
					| \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
					)*$%xs', $string);
}


/**
 * 解析ID
 * @param $id 评论ID
 */
function id_decode($id)
{
    return explode('-', $id);
}

/**
 * 对用户的密码进行加密
 * @param $password
 * @param $encrypt //传入加密串，在修改密码时做认证
 * @return array/password
 */
function password($password, $encrypt = '')
{
    $pwd = array();
    $pwd['encrypt'] = $encrypt ? $encrypt : create_randomstr();
    $pwd['password'] = md5(md5(trim($password)) . $pwd['encrypt']);
    return $encrypt ? $pwd['password'] : $pwd;
}
/**
 * 生成随机字符串
 * @param string $lenth 长度
 * @return string 字符串
 */
function create_randomstr($lenth = 6)
{
    return random($lenth, '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ');
}

/**
 * 检测输入中是否含有错误字符
 *
 * @param char $string
 * @return TRUE or FALSE
 */
function is_badword($string)
{
    $badwords = array("\\", '&', ' ', "'", '"', '/', '*', ',', '<', '>', "\r", "\t", "\n", "#");
    foreach ($badwords as $value)
    {
        if (strpos($string, $value) !== false)
        {
            return true;
        }
    }
    return false;
}

/**
 * 检查用户名是否符合规定
 *
 * @param STRING $username
 * @return 	TRUE or FALSE
 */
function is_username($username)
{
    $strlen = strlen($username);
    if (is_badword($username) || !preg_match("/^[a-zA-Z0-9_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]+$/", $username))
    {
        return false;
    } elseif (20 <= $strlen || $strlen < 2)
    {
        return false;
    }
    return true;
}

/**
 * 检查id是否存在于数组中
 *
 * @param $id
 * @param $ids
 * @param $s
 */
function check_in($id, $ids = '', $s = ',')
{
    if (!$ids)
        return false;
    $ids = explode($s, $ids);
    return is_array($id) ? array_intersect($id, $ids) : in_array($id, $ids);
}

/**
 * 对数据进行编码转换
 * @param array/string $data       数组
 * @param string $input     需要转换的编码
 * @param string $output    转换后的编码
 */
function array_iconv($data, $input = 'gbk', $output = 'utf-8')
{
    if (!is_array($data))
    {
        return iconv($input, $output, $data);
    }
    else
    {
        foreach ($data as $key => $val)
        {
            if (is_array($val))
            {
                $data[$key] = array_iconv($val, $input, $output);
            }
            else
            {
                $data[$key] = iconv($input, $output, $val);
            }
        }
        return $data;
    }
}

/**
 * 生成缩略图函数
 * @param unknown_type $imgurl 图片路径
 * @param unknown_type $width  缩略图宽度
 * @param unknown_type $height 缩略图高度
 * @param unknown_type $autocut 是否自动裁剪 默认裁剪，当高度或宽度有一个数值为0是，自动关闭
 * @param unknown_type $smallpic 无图片是默认图片路径
 */
function thumb($imgurl, $width = 100, $height = 100, $autocut = 1, $smallpic = 'nopic.gif')
{
    global $image;
    $upload_url = ys_base::load_config('system', 'upload_url');
    $upload_path = ys_base::load_config('system', 'upload_path');
    if (empty($imgurl))
        return IMG_PATH . $smallpic;
    $imgurl_replace = str_replace($upload_url, '', $imgurl);
    if (!extension_loaded('gd') || strpos($imgurl_replace, '://'))
        return $imgurl;
    if (!file_exists($upload_path . $imgurl_replace))
        return IMG_PATH . $smallpic;

    list($width_t, $height_t, $type, $attr) = getimagesize($upload_path . $imgurl_replace);
    if ($width >= $width_t || $height >= $height_t)
        return $imgurl;

    $newimgurl = dirname($imgurl_replace) . '/thumb_' . $width . '_' . $height . '_' . basename($imgurl_replace);

    if (file_exists($upload_path . $newimgurl))
        return $upload_url . $newimgurl;

    if (!is_object($image))
    {
        ys_base::load_sys_class('image', '', '0');
        $image = new image(1, 0);
    }
    return $image->thumb($upload_path . $imgurl_replace, $upload_path . $newimgurl, $width, $height, '', $autocut) ? $upload_url . $newimgurl : $imgurl;
}

/**
 * 水印添加
 * @param $source 原图片路径
 * @param $target 生成水印图片途径，默认为空，覆盖原图
 * @param $siteid 站点id，系统需根据站点id获取水印信息
 */
function watermark($source, $target = '', $siteid)
{
    global $image_w;
    if (empty($source))
        return $source;
    if (!extension_loaded('gd') || strpos($source, '://'))
        return $source;
    if (!$target)
        $target = $source;
    if (!is_object($image_w))
    {
        ys_base::load_sys_class('image', '', '0');
        $image_w = new image(0, $siteid);
    }
    $image_w->watermark($source, $target);
    return $target;
}


/**
 * 将附件地址转换为绝对地址
 * @param $path
 */
function atturl($path)
{
    if (strpos($path, ':/'))
    {
        return $path;
    }
    else
    {
        $sitelist = getcache('sitelist', 'commons');
        $siteid = get_siteid();
        $siteurl = $sitelist[$siteid]['domain'];
        $domainlen = strlen($sitelist[$siteid]['domain']) - 1;
        $path = $siteurl . $path;
        $path = substr_replace($path, '/', strpos($path, '//', $domainlen), 2);
        return $path;
    }
}


/**
 * 生成标题样式
 * @param $style   样式
 * @param $html    是否显示完整的STYLE
 */
function title_style($style, $html = 1)
{
    $str = '';
    if ($html)
        $str = ' style="';
    $style_arr = explode(';', $style);
    if (!empty($style_arr[0]))
        $str .= 'color:' . $style_arr[0] . ';';
    if (!empty($style_arr[1]))
        $str .= 'font-weight:' . $style_arr[1] . ';';
    if ($html)
        $str .= '" ';
    return $str;
}

/**
 * 生成上传附件验证
 */

function upload_key($args, $operation = 'ENCODE')
{
    $pc_auth_key = md5(ys_base::load_config('system', 'auth_key') . $_SERVER['HTTP_USER_AGENT']);
    $authkey = sys_auth($args, $operation, $pc_auth_key);
    return $authkey;
}

function ajaxDone($code = '', $message = '', $callbackType = '', $navTabId = '', $title = '', $forwardUrl = '')
{
    $ajax = array();
    $ajax[statusCode] = $code ? $code : "200";
    $ajax[message] = $message ? $message : ($ajax[statusCode] == 200 ? "操作成功" : "操作失败");
    if ($ajax[message] == "no")
    {
        $ajax[message] = "";
    }
    $ajax[navTabId] = $navTabId;
    $ajax[title] = $title;
    $ajax[callbackType] = $callbackType ? $callbackType : "forward";
    $ajax[forwardUrl] = $forwardUrl;
    exit(json_encode($ajax));
}
function error($error = '')
{
    include template("error");
    exit();
}
/**
 * 在指定位置加入指定字符
 * @param $string 源字符
 * @param $instr 需要添加的字符
 * @param $length 固定长度
 **/
function straddstr($string, $instr = "\n", $length = 78)
{
    $str = "";
    $teststr = str_cut($string, $length, "");
    while (strlen($teststr) > 0)
    {
        $str .= $teststr . $instr;
        $string = substr($string, strlen($teststr));
        $teststr = str_cut($string, $length, "");
    }
    return $str;
}
/**
 * 验证数据，移除多余列
 **/
function checkkey($data, $check)
{
    foreach ($data as $n => $v)
    {
        if (!in_array($n, $check))
        {
            unset($data[$n]);
        }
    }
    return $data;
}
/**
 * 两时间转换距离格式
 **/
function dateDist($starttime,$endtime=SYS_TIME)
{
    if (is_numeric($starttime) && $starttime > 0)
    {
    }
    else
    {
        $starttime = strtotime($starttime);
    }
    if (is_numeric($endtime) && $endtime > 0)
    {
    }
    else
    {
        $endtime = strtotime($endtime);
    }
    $disttime = $endtime - $starttime;
    return formatDate($disttime);
}
/**
 * 将时间戳格式化
 **/
function formatDate($disttime,$trad=false){
    $sec = $disttime % 60;
    $min = intval($disttime / 60 % 60);
    $hour = intval($disttime / 60 / 60 % 24);
    $day = intval($disttime / 60 / 60 / 24);
    if ($day)
    {
        if(!$min){
            $str = $day . "天" . $hour . "时";
        }else{
            $str = $day . "天" . $hour . "时" . $min . "分";
        }
    }
    else
    {
        if ($hour)
        {
            $str = $hour . "时";
            if(!$sec){
                if($min){
                    $str.=$min . "分";
                }elseif($trad){
                    $str=$hour . "小时";
                }
            }else{
                $str.=$min . "分". $sec . "秒";
            }
        }
        else
        {
            if($sec){
                $str = $min . "分" . $sec . "秒";
            }else{
                if($trad){
                    $str = $min . "分钟";
                }else{
                    $str = $min . "分";
                }
            }
        }
    }
    return $str;
}
/**
 * 返回时间提醒
 **/
function expireColor($date)
{
    $str = $date;
    $color = "";
    $expireday = ys_base::load_config("system", "expireday");
    $expirecolor = ys_base::load_config("system", "expirecolor");
    if (is_numeric($date) && $date > 0)
    {
        $date = date("Y-m-d", $date);
    }
    if (!is_numeric($date))
    {
        $time = strtotime($date);
        if (!$time)
        {
            return $str;
        }

    }
    if (SYS_TIME < $time - 3600 * 24 * $expireday)
    {
        $color = $expirecolor[0];
    } elseif (SYS_TIME > $time)
    {
        $color = $expirecolor[2];
    }
    else
    {
        if ($time > 0)
        {
            $color = $expirecolor[1];
        }
    }
    $date = date("Y-m-d", $time);
    $str = "<font class=\"$color\">" . $date . "</font>";
    return $str;
}
/**
 * 生成IP相关的链接
 **/
function getIpsHref($ips)
{
    $ipstr = '';
    if (!is_array($ips))
    {
        $ips = explode(",", $ips);
    }
    foreach ($ips as $ip)
    {
        $ipstr .= "<p><a href=\"?m=ip&c=view&ip=$ip\" rel=\"ip_view\" target=\"dialog\">$ip</a></p>";
    }
    return $ipstr;
}
/**
 * 返回字符串
 * @return string
 **/
function bar_encode($array = array())
{
    $string = "|";
    foreach ($array as $n => $v)
    {
        $v = str_replace("|", "$", $v);
        $string .= $n . ":" . $v . "|";
    }
    return $string;
}
/**
 * 字符串转换成数组
 * @return array
 **/
function bar_decode($string = '|')
{
    $array = array();
    $test = explode("|", $string);
    foreach ($test as $one)
    {
        list($n, $v) = explode(":", $one);
        $array[$n] = str_replace("$", "|", $v);
    }
    return $array;
}
function formatBytes($bytes) {
	if($bytes >= 1073741824) {
		$bytes = round($bytes / 1073741824 * 100) / 100 . 'GB';
	} elseif($bytes >= 1048576) {
		$bytes = round($bytes / 1048576 * 100) / 100 . 'MB';
	} elseif($bytes >= 1024) {
		$bytes = round($bytes / 1024 * 100) / 100 . 'KB';
	} else {
		$bytes = $bytes . 'Bytes';
	}
	return $bytes;
}
?>