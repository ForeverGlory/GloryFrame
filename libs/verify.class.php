<?php
/**
 * verify.class.php 验证类
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/libs/verify.class.php
 * @version         $
 */
class verify{

    private $setting;

    public function __construct($setting = ''){
        static $isFirst = true;
        if($isFirst){
            GloryFrame::Auto($this);
            if(empty($setting) || is_string($setting)){

            }
            $this->setting = $setting;
            $isFirst = false;
        }
    }

    /**
     * 判断字符串在多少之间
     * @param   string  $str    字符串
     * @param   string  $min    最小长度
     * @param   string  $max    最大长度
     * @return  bool
     */
    public function strLimit($str, $min, $max){
        if(empty($str)){
            return false;
        }
        $min = intval($min);
        $max = intval($max);
        return preg_match("/^.{{$min},{$max}}$/", $str);
    }

    /**
     * 判断字符串是否是该长度
     * @param   string  $str    字符串
     * @param   int     $size   字符串长度
     * @return  bool
     */
    public function strSize($str, $size){
        if(empty($str)){
            return false;
        }
        $size = intval($size);
        return (strlen($str) == $size);
    }

    /**
     * 判断字符串是否是MD5加密后的字符
     * @param   string  $str
     * @return  bool
     */
    public function isMd5($str){
        if(empty($str)){
            return false;
        }
        return preg_match("/^[0-9A-Za-z]{32}$/", $str);
    }

    /**
     * 判断用户名 数字、字母、下划线、中划线、点、中文字符
     * @param   string  $str    字符串
     * @return  bool
     */
    public function isUser($str){
        if(empty($str)){
            return false;
        }
        return preg_match("/^[0-9A-Za-z_\-\.\x4E00-\x9FA5\xf900-\xfa2d]{1,}$/", $str);
    }

    /**
     * 判断是否是中文
     * @param   string  $chinese
     * @return  boolean
     */
    public function chineseCharset($chinese){
        if(empty($chinese)){
            return false;
        }
        return preg_match("/^[\x4E00-\x9FA5\xf900-\xfa2d]{2,}$/", $chinese);
    }

    /**
     * 判断邮箱地址是否正确
     * @param   string  $mail    邮箱
     * @return  bool
     */
    public function mail($mail){
        if(empty($mail)){
            return false;
        }
        return preg_match("/^[0-9A-Za-z_\-\.]{1,}@([0-9A-Za-z_\-]{1,}[\.]{1}){1,}[a-zA-Z]{2,}$/", $mail);
    }

    /**
     * 判断国内手机号码 13,14,15,18开头的11位数
     * @param   int(11)   $phone
     * @return  bool
     */
    public function phone($phone){
        if(empty($phone)){
            return false;
        }
        return preg_match("/^1[3|4|5|8]{1}[0-9]{9}$/", $phone);
    }

    /**
     * 判断是否QQ号
     * @param   string(5-)  $qq
     * @return  boolean
     */
    public function qq($qq){
        if(empty($qq)){
            return false;
        }
        return preg_match("/^[1-9]{1}\d{4,}$/", $qq);
    }

    /**
     * 判断URL
     * @param   string  $url    网址
     * @return  bool
     */
    public function url($url){
        if(empty($url)){
            return false;
        }
        return preg_match("/^[http:\/\/|https:\/\/]{1}.+/", $url);
    }
}
?>