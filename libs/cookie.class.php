<?php
/**
 * cookie.class.php COOKIE处理类
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/libs/cookie.class.php
 * @version         $
 */
class cookie
{
    /**
     * COOKIE配置信息
     */
    private $setting;

    /**
     * 构造函数
     * @param	string/array	$setting	缓存配置，或配置名
     *          string          读取 cookie.config.php 里名称的配置
     *          array(
     *              'iscookie'          是否开启COOKIE
     *              'cookie_domain'     作用域
     *              'cookie_path'       作用路径
     *              'cookie_pre'        前缀，同一域名下安装多套系统时，请修改Cookie前缀
     *              'cookie_ttl'        生命周期，0 表示随浏览器进程
     *              'cookie_encrypt'    是否加密
     *              'cookie_key'        加密HASH
     *          )
     * @return  void
     */
    public function __construct($setting = '')
    {
        static $isFirst = true;
        if($isFirst)
        {
           GloryFrame::Auto($this);
            if(is_string($setting) || empty($setting))
            {
                if(empty($setting))
                {
                    $setting = $this->_var["setting"]["cookie"];
                }
                $setting = $this->config->load("cookie", array($setting));
            }
            $this->setting = $setting;
            $isFirst = false;
        }
    }

    /**
     * 设置 cookie
     * @param   string/array    $var        变量名 $_COOKIE[$var1][$var2]
     * @param   string          $value      变量值  null 表示删除
     * @param   array   $option             选项
     *          array(
     *              [pre]       string      前缀
     *              [time]      int         过期时间
     *              [path]      string      Cookie路径
     *              [domain]    string      Cookie域
     *              [encrypt]   int         加密
     *              [key]       string      加密HASH
     *          )
     */
    public function set($var, $value = '', $option = array())
    {
        $var = strToArray(",", $var);
        if(is_array($value))
        {
            foreach($value as $k => $v)
            {
                $name = $var;
                array_push($name, $k);
                $this->set($name, $v, $option);
            }
        }
        else
        {
            if($value != null)
            {
                if(array_key_exists("time", $option))
                {
                    $time = empty($option["time"]) ? 0 : $this->_var["systime"] + $option["time"];
                }
                else
                {
                    $time = empty($this->setting["cookie_ttl"]) ? 0 : $this->_var["systime"] + $this->setting["cookie_ttl"];
                }

                $encrypt = array_key_exists("encrypt", $option) ? $option["encrypt"] : $this->setting["cookie_encrypt"];
                if($encrypt)
                {
                    $key   = array_key_exists("key", $option) ? $option["key"] : $this->setting["cookie_key"];
                    $value = $this->encrypt->base64_Xor($value, 1, $key);
                }
                $name  = (array_key_exists("pre", $option) ? $option["pre"] : $this->setting['cookie_pre']) . $var[0];
                foreach($var as $k => $v)
                {
                    if($k)
                    {
                        $name.="[" . $v . "]";
                    }
                }
                $path     = array_key_exists("path", $option) ? $option["path"] : $this->setting['cookie_path'];
                $domain   = array_key_exists("domain", $option) ? $option["domain"] : $this->setting['cookie_domain'];
                $httponly = $this->input->server("server_port") == 443 ? 1 : 0;
                setcookie($name, $value, $time, $path, $domain, $httponly);
            }
            else
            {
                return $this->del($var, $option);
            }
        }
    }

    /**
     * 获取通过 set_cookie 设置的 cookie 变量
     * @param   string/array/null           $var        变量名 $_COOKIE[$var1][$var2]
     * @param   array                       $option     选项
     *          array(
     *              [pre]       string      前缀
     *              [encrypt]   int         加密
     *              [key]       string      加密HASH
     *          )
     * @return  mixed   返回cookie/false
     */
    public function get($var = null, $option = array())
    {
        $cookie = array();
        $pre    = array_key_exists("pre", $option) ? $option["pre"] : $this->setting['cookie_pre'];
        $length = strlen($pre);
        if($length > 0)
        {
            foreach($_COOKIE as $k => $v)
            {
                if(stripos($k, $pre) === 0)
                {
                    $key              = substr($k, $length);
                    $cookie["{$key}"] = $_COOKIE[$k];
                }
            }
        }
        else
        {
            $cookie = $_COOKIE;
        }
        if(!is_null($var))
        {
            $var = strToArray(",", $var);
            foreach($var as $v)
            {
                $cookie  = $cookie["{$v}"];
            }
        }
        $encrypt = array_key_exists("encrypt", $option) ? $option["encrypt"] : $this->setting["cookie_encrypt"];
        if($encrypt)
        {
            $key    = array_key_exists("key", $option) ? $option["key"] : $this->setting["cookie_key"];
            $cookie = $this->encrypt->base64_Xor($cookie, 0, $key);
        }
        return $cookie;
    }

    /**
     * Cookie详情
     * @param   string/array    $var        变量名 $_COOKIE[$var1][$var2]
     * @param   array           $option     选项
     */
    public function info($var, $option = array())
    {
        //todo 返回Cookie详情
    }

    /**
     * 删除COOKIE
     * @param   string/array/null           $var        变量名 $_COOKIE[$var1][$var2]
     * @param   array                       $option     选项
     *          array(
     *              [pre]       string      前缀
     *              [path]      string      Cookie路径
     *              [domain]    string      Cookie域
     *          )
     */
    public function del($var, $option = array())
    {
        if(!is_null($var))
        {
            $option["encrypt"] = 0;

            $cookie = $this->get($var, $option);
            $var    = strToArray(",", $var);
            if(is_array($cookie))
            {
                foreach($cookie as $k => $v)
                {
                    $name = $var;
                    array_push($name, $k);
                    $this->del($name, $option);
                }
            }
            else
            {
                $name = (array_key_exists("pre", $option) ? $option["pre"] : $this->setting['cookie_pre']) . $var[0];
                foreach($var as $k => $v)
                {
                    if($k)
                    {
                        $name.="[" . $v . "]";
                    }
                }
                $path     = array_key_exists("path", $option) ? $option["path"] : $this->setting['cookie_path'];
                $domain   = array_key_exists("domain", $option) ? $option["domain"] : $this->setting['cookie_domain'];
                $httponly = $this->input->server("server_port") == 443 ? 1 : 0;
                setcookie($name, null, 0, $path, $domain, $httponly);
            }
        }
        else
        {
            return $this->clear();
        }
    }

    /**
     * 清空COOKIE
     * @param   array                       $option     选项
     *          array(
     *              [pre]       string      前缀
     *              [path]      string      Cookie路径
     *              [domain]    string      Cookie域
     *          )
     */
    public function clear($option = array())
    {
        $cookie = $this->get(null, $option);
        foreach($cookie as $k => $v)
        {
            $this->del($k, $option);
        }
    }

}
?>