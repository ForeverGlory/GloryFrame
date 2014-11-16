<?php
/**
 * encrypt.class.php加解密类
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/libs/encrypt.class.php
 * @version         $
 */
class encrypt
{
    /**
     * 基本配置
     * @var type
     */
    private $setting;

    function __construct($setting = '')
    {
        static $isFirst = true;
        if($isFirst)
        {
           GloryFrame::Auto($this);
            if(empty($setting))
            {
                $this->setting = array();
            }
            $this->setting = $setting;
            $isFirst = false;
        }
    }

    /**
     * base64 按位异或 加解密方法
     * @param   string  $txt        需要加解密的字符串
     * @param   string  $encrypt    1 加密 0 解密
     * @param   string  $key        加解密字符串
     * @return  string  加解密后字符串
     * */
    public function base64_Xor($txt, $encrypt = 1, $key = '')
    {
        if(is_array($txt))
        {
            $code = array();
            foreach($txt as $k => $v)
            {
                $code[$k] = $this->base64_Xor($v, $encrypt, $key);
            }
        }
        else
        {
            $code = '';
            $key  = empty($key) ? "ForeverGlory" : $key;
            $txt  = empty($encrypt) ? base64_decode($txt) : (string)$txt;
            $len  = strlen($key);
            for($i    = 0; $i < strlen($txt); $i++)
            {
                $k    = $i % $len;
                $code .= $txt[$i] ^ $key[$k];
            }
            $code = empty($encrypt) ? $code : base64_encode($code);
        }
        return $code;
    }

    /**
     * MD5+hash 加密
     * */
    public function md5hash($str, $key = '')
    {
        return md5($str . $key);
    }

}
?>