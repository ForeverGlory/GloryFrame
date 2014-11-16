<?php
/**
 * Server服务器信息类 <server.class.php>
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/libs/server.class.php
 * @version         $
 */
class server{

    public function __construct(){
        static $isFirst = true;
        if($isFirst){
            GloryFrame::Auto($this);
            $isFirst = false;
        }
    }

    /**
     * 服务器操作系统
     * @param   string  $mode   模式 a all , s system name , n host name , r release name , v version , m machine type
     * @return  string  操作系统
     */
    public function os($mode = "a"){
        return php_uname($mode);
    }

    /**
     * 判断服务器是否是WIN系统
     */
    public function isWin(){
        $os = $this->os("s");
        return is_int(stripos($os, "win"));
    }

    /**
     * 判断服务器是否Linux
     */
    public function isLinux(){

    }

    /**
     * 判断服务器是否Unix
     */
    public function isUnix(){

    }

    /**
     * 当前WEB服务
     * @param   string  $mode 类型 a all , n name ,v version
     * @return  string
     */
    public function webServer($mode = "a"){
        $service = "";
        list($name, $version) = explode("/", strstr($_SERVER["SERVER_SOFTWARE"], " ", true));
        if($mode == "a" || $mode == "n"){
            $service = $name;
        }
        if($mode == "a" || $mode == "v"){
            $service ? $service.=" " . $version : $service = $version;
        }
        return $service;
    }

    /**
     * 判断当前WEB服务是否是IIS
     * @return  bool
     */
    public function isIIS(){
        return defined("IIS_WRITE") && defined("IIS_READ");
    }

    /**
     * 判断当前WEB服务是否是Apache
     * @return  bool
     */
    public function isApache(){
        return function_exists("apache_get_version");
    }

    /**
     * 判断当前WEB服务是否是Nginx
     * @return  bool
     */
    public function isNginx(){

    }

    /**
     * 服务器PHP版本
     * @return  string
     */
    public function phpVersion(){
        return PHP_VERSION;
    }

    /**
     * 服务器PHP执行用户
     * @return  string
     */
    public function phpUser(){
        return Get_Current_User();
    }

    /**
     * PHP加载过的模块
     */
    public function modules(){
        return get_loaded_extensions();
    }

    /**
     * 服务器Zend版本
     * @return  string
     */
    public function zendVersion(){
        return Zend_Version();
    }

    /**
     * 服务器是否开启伪静态
     * @return  true/false
     */
    public function isRewrite(){
        return extension_loaded("mod_rewrite");
    }

    /**
     * 服务器当前IP
     */
    public function ip(){
        return $_SERVER["SERVER_ADDR"];
    }

    /**
     * 服务器当前端口
     */
    public function port(){
        return $_SERVER["SERVER_PORT"];
    }

    /**
     * 服务器域名
     */
    public function web(){
        return $_SERVER["SERVER_NAME"];
    }
}