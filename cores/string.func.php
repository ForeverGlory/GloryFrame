<?php
/**
 * string.func.php  字符串相关函数
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/cores/string.func.php
 * @version         $
 */

/**
 * 返回经addslashes处理过的字符串或数组
 * @param   string  $string     需要处理的字符串或数组
 * @return  string
 */
function new_addslashes($string){
    if(!is_array($string)){
        //先解一次，然后再加，防止重复处理
        return addslashes(stripslashes(trim($string)));
    }
    foreach($string as $n => $v)
    {
        $string[$n] = new_addslashes($v);
    }
    return $string;
}

/**
 * 返回经stripslashes处理过的字符串或数组
 * @param   stirng  $string     需要处理的字符串或数组
 * @return  string
 */
function new_stripslashes($string){
    if(!is_array($string)){
        return stripslashes($string);
    }
    foreach($string as $n => $v)
    {
        $string[$n] = new_stripslashes($val);
    }
    return $string;
}

/**
 * HTML特殊字符转换
 * @param   string/array    $string     需要转换的HTML或数组
 * @return  string/array
 */
function new_html_special_encode($string){
    if(!is_array($string)){
        $string = htmlspecialchars($string);
    }else{
        foreach($string as $key => $val)
        {
            $string[$key] = new_html_special_encode($val);
        }
    }
    return $string;
}

/**
 * 还原HTML特殊字符
 * @param   stringarray     $string     需要转换的HTML或数组
 * @return  string/array
 */
function new_html_special_decode($string){
    if(!is_array($string)){
        $string = htmlspecialchars_decode($string);
    }else{
        foreach($string as $key => $val)
        {
            $string[$key] = new_html_special_decode($val);
        }
    }
    return $string;
}

/**
 * 安全过滤函数
 *
 * @param   string  $string     需要过滤的字符
 * @return  string
 */
function safe_replace($string){
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
 * 查询字符是否存在于某字符串
 *
 * @param   $haystack   字符串
 * @param   $needle     要查找的字符
 * @return  bool
 */
function str_exists($haystack, $needle){
    return !(strpos($haystack, $needle) === false);
}

/**
 * 圆角转半角字符
 * */
function sbc_dbc($string){
    $Queue = array(
        '０'     => '0',
        '１'     => '1',
        '２'     => '2',
        '３'     => '3',
        '４'     => '4',
        '５'     => '5',
        '６'     => '6',
        '７'     => '7',
        '８'     => '8',
        '９'     => '9',
        'Ａ'     => 'A',
        'Ｂ'     => 'B',
        'Ｃ'     => 'C',
        'Ｄ'     => 'D',
        'Ｅ'     => 'E',
        'Ｆ'     => 'F',
        'Ｇ'     => 'G',
        'Ｈ'     => 'H',
        'Ｉ'     => 'I',
        'Ｊ'     => 'J',
        'Ｋ'     => 'K',
        'Ｌ'     => 'L',
        'Ｍ'     => 'M',
        'Ｎ'     => 'N',
        'Ｏ'     => 'O',
        'Ｐ'     => 'P',
        'Ｑ'     => 'Q',
        'Ｒ'     => 'R',
        'Ｓ'     => 'S',
        'Ｔ'     => 'T',
        'Ｕ'     => 'U',
        'Ｖ'     => 'V',
        'Ｗ'     => 'W',
        'Ｘ'     => 'X',
        'Ｙ'     => 'Y',
        'Ｚ'     => 'Z',
        'ａ'     => 'a',
        'ｂ'     => 'b',
        'ｃ'     => 'c',
        'ｄ'     => 'd',
        'ｅ'     => 'e',
        'ｆ'     => 'f',
        'ｇ'     => 'g',
        'ｈ'     => 'h',
        'ｉ'     => 'i',
        'ｊ'     => 'j',
        'ｋ'     => 'k',
        'ｌ'     => 'l',
        'ｍ'     => 'm',
        'ｎ'     => 'n',
        'ｏ'     => 'o',
        'ｐ'     => 'p',
        'ｑ'     => 'q',
        'ｒ'     => 'r',
        'ｓ'     => 's',
        'ｔ'     => 't',
        'ｕ'     => 'u',
        'ｖ'     => 'v',
        'ｗ'     => 'w',
        'ｘ'     => 'x',
        'ｙ'     => 'y',
        'ｚ'     => 'z',
        '－'     => '-');
    $string = strtr($string, $Queue);
    return $string;
}

/**
 * 过滤ASCII码从0-28的控制字符
 * @return String
 */
function trim_unsafe_control_chars($str){
    $rule = '/[' . chr(1) . '-' . chr(8) . chr(11) . '-' . chr(12) . chr(14) . '-' . chr(31) . ']*/';
    return str_replace(chr(0), '', preg_replace($rule, '', $str));
}

/**
 * 格式化文本域内容
 *
 * @param $string 文本域内容
 * @return string
 */
function trim_textarea($string){
    $string = nl2br(str_replace('  ', '&nbsp;', $string));
    return $string;
}

/**
 * 产生随机字符串
 *
 * @param    int        $length  输出长度
 * @param    string     $chars   可选的 ，默认为 数字、大小写字母
 * @return   string     字符串
 */
function random($length = 16, $chars = '0123456789abcdeGloryFramehijklmnopqrstuvwxyzABCDEGloryFrameHIJKLMNOPQRSTUVWXYZ'){
    $hash = '';
    $max = strlen($chars) - 1;
    for($i = 0; $i < $length; $i++)
    {
        $hash .= $chars[mt_rand(0, $max)];
    }
    return $hash;
}

/**
 * 转义 javascript 代码标记
 *
 * @param $str
 * @return mixed
 */
function trim_script($str){
    $str = preg_replace('/\<([\/]?)script([^\>]*?)\>/si', '&lt;\\1script\\2&gt;', $str);
    $str = preg_replace('/\<([\/]?)iframe([^\>]*?)\>/si', '&lt;\\1iframe\\2&gt;', $str);
    $str = preg_replace('/\<([\/]?)frame([^\>]*?)\>/si', '&lt;\\1frame\\2&gt;', $str);
    $str = preg_replace('/]]\>/si', ']] >', $str);
    return $str;
}

/**
 * 字符截取 支持UTF8/GBK
 * @param $string
 * @param $length
 * @param $dot
 */
function str_cut($string, $length, $dot = '...'){
    $strlen = strlen($string);
    if($strlen <= $length)
        return $string;
    $strcut = '';
    if(strtolower(GloryFrame::load_config('system', 'charset')) == 'utf-8'){
        $length = intval($length - strlen($dot) - $length / 3);
        $n = $tn = $noc = 0;
        while($n < strlen($string))
        {
            $t = ord($string[$n]);
            if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)){
                $tn = 1;
                $n++;
                $noc++;
            }elseif(194 <= $t && $t <= 223){
                $tn = 2;
                $n += 2;
                $noc += 2;
            }elseif(224 <= $t && $t <= 239){
                $tn = 3;
                $n += 3;
                $noc += 2;
            }elseif(240 <= $t && $t <= 247){
                $tn = 4;
                $n += 4;
                $noc += 2;
            }elseif(248 <= $t && $t <= 251){
                $tn = 5;
                $n += 5;
                $noc += 2;
            }elseif($t == 252 || $t == 253){
                $tn = 6;
                $n += 6;
                $noc += 2;
            }else{
                $n++;
            }
            if($noc >= $length){
                break;
            }
        }
        if($noc > $length){
            $n -= $tn;
        }
        $strcut = substr($string, 0, $n);
    }else{
        $dotlen = strlen($dot);
        $maxi = $length - $dotlen - 1;
        $current_str = '';
        $search_arr = array('&', ' ', '"', "'", '“', '”', '—', '<', '>', '·', '…', '∵');
        $replace_arr = array('&amp;', '&nbsp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;', ' ');
        $search_flip = array_flip($search_arr);
        for($i = 0; $i < $maxi; $i++)
        {
            $current_str = ord($string[$i]) > 127 ? $string[$i] . $string[++$i] : $string[$i];
            if(in_array($current_str, $search_arr)){
                $key = $search_flip[$current_str];
            }
            $strcut .= $current_str;
        }
    }
    return $strcut . $dot;
}

/**
 * 判断字符串是否为utf8编码，英文和半角字符返回ture
 * @param $string
 * @return bool
 */
function is_utf8($string){
    return preg_match('%^(?:[\x09\x0A\x0D\x20-\x7E] # ASCII
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
 * 检测输入中是否含有错误字符
 *
 * @param char $string
 * @return TRUE or FALSE
 */
function is_badword($string){
    $badwords = array("\\", '&', ' ', "'", '"', '/', '*', ',', '<', '>', "\r", "\t", "\n", "#");
    foreach($badwords as $value)
    {
        if(strpos($string, $value) !== false){
            return true;
        }
    }
    return false;
}

/**
 * 在指定位置加入指定字符
 * @param 	$string 	源字符
 * @param 	$instr 		需要添加的字符
 * @param 	$length 	固定长度
 * */
function straddstr($string, $instr = "\n", $length = 78){
    $str = "";
    $teststr = str_cut($string, $length, "");
    while(strlen($teststr) > 0)
    {
        $str .= $teststr . $instr;
        $string = substr($string, strlen($teststr));
        $teststr = str_cut($string, $length, "");
    }
    return $str;
}

/**
 * 转换字节数为其他单位
 *
 *
 * @param	string	$filesize	字节大小
 * @return	string	返回大小
 */
function formatBytes($bytes){
    if($bytes >= 1073741824){
        $bytes = round($bytes / 1073741824 * 100) / 100 . 'GB';
    }elseif($bytes >= 1048576){
        $bytes = round($bytes / 1048576 * 100) / 100 . 'MB';
    }elseif($bytes >= 1024){
        $bytes = round($bytes / 1024 * 100) / 100 . 'KB';
    }else{
        $bytes = $bytes . 'Bytes';
    }
    return $bytes;
}

function formatMb($mbs){
    if($mbs > 0){
        $mbs = formatBytes($mbs * 1024 * 1024);
    }elseif($mbs < 0){
        $mbs = "-" . formatBytes(abs($mbs) * 1024 * 1024);
    }else{
        $mbs = "0" . "MB";
    }
    return $mbs;
}
if(!function_exists("xml_decode")){

    /**
     * Decodes a XML
     * 将XML字符串转换成对象
     * @param   type    $xml
     * @param   type    $assoc
     * @return  object
     */
    function xml_decode($xml, $assoc = false){
        $object = simplexml_load_string($xml);
        if($assoc){
            $object = get_object_vars($object);
        }
        return $object;
    }
}
if(!function_exists("xml_encode")){

    /**
     * Returns the XML representation of a value
     * 生成XML字符串
     * @param   type    $value
     * @return  string
     */
    function xml_encode($value, $encoding = 'utf-8', $root = "root"){
        $xml = '<?xml version="1.0" encoding="' . $encoding . '"?>';
        $xml .= '<' . $root . '>';
        $xml .=data_to_xml($value);
        $xml .= '</' . $root . '>';
        return $xml;
    }

    function data_to_xml($value){
        $xml = "";
        if(is_object($value)){
            $value = get_object_vars($value);
        }elseif(!is_array($value)){
            $value = (array)$value;
        }
        foreach($value as $key => $val)
        {
            if(is_numeric($key)){
                $key = "item id=\"$key\"";
            }
            $xml .= "<$key>";
            $xml .= (is_array($val) || is_object($val)) ? data_to_xml($val) : $val;
            list($key, ) = explode(' ', $key);
            $xml .= "</$key>";
        }
        return $xml;
    }
}

/**
 * 变量转换成字符串
 * @param 	string 	$arg
 */
function argToStr($arg){
    $string = "";
    if(is_object($arg)){
        $string = "Object";
        $class = get_class($arg);
        if($class){
            $string.="({$class})";
        }
    }elseif(is_array($arg)){
        $string = "Array";
        if($arg){
            $string.="(";
            $array = array();
            foreach($arg as $k => $v)
            {
                $array[$k] = "";
                if(!is_numeric($k)){
                    $array[$k].="\"{$k}\"=>";
                }
                $array[$k].=argToStr($v);
            }
            $string.=implode(",", $array);
            $string.=")";
        }
    }elseif(is_numeric($arg)){
        $string = $arg;
    }elseif(is_string($arg)){
        $string = "\"{$arg}\"";
    }elseif(is_bool($arg)){
        if(true == $arg){
            $string = "true";
        }else{
            $string = "false";
        }
    }else{
        $string = gettype($arg);
    }
    return $string;
}

/**
 * 格式化正则符号
 * @param 	string 	$str
 */
function formatPregStr($str){
    $preg = array(
        "/", "\\", "^", "\$", "*", "+", "?", ".", "{", "}", "[", "]", ":", "-", "=", "!", "|",
    );
    foreach($preg as $val)
    {
        $str = str_replace($val, "\\" . $val, $str);
    }
    return $str;
}

/**
 * 对比字符串<类似mysql查询语句like>
 * @param   string  $str
 * @param   string  $format 对比格式，同mysql like
 * @return boolean
 */
function like($str, $format = "%"){
    $pattern = "/^" . str_replace("%", "(.*?)", $format) . "$/";
    return preg_match($pattern, $str);
}
?>