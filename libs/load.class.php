<?php
/**
 * load.class.php 装载类
 * 装载函数(框架、应用、插件)、类(框架、应用、插件)、控制器、插件、勾子
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/libs/load.class.php
 * @version         $
 */
class load{

    public $setting = array();

    /**
     * 实例化装载类
     */
    public function __construct($setting = array()){
        static $isFirst = true;
        if($isFirst){
            $this->initFrame($setting);
            $isFirst = false;
        }
    }

    /**
     * 框架中，实例化操作
     */
    private function initFrame($setting = array()){
        if(defined("IN_FR")){
            GloryFrame::Auto($this);
            //初始化加载默认函数库
            $funcs = array("common", "global", "string", "array", "date");
            foreach($funcs as $func)
            {
                $this->func($func);
            }
        }else{
            $this->setting = array(
                "FramePath" => array(
                    "config"  => FR_PATH . "configs" . DIRECTORY_SEPARATOR,
                    "core"    => FR_PATH . "cores" . DIRECTORY_SEPARATOR,
                    "data"    => FR_PATH . "data" . DIRECTORY_SEPARATOR,
                    "hook"    => FR_PATH . "hooks" . DIRECTORY_SEPARATOR,
                    "lib"     => FR_PATH . "libs" . DIRECTORY_SEPARATOR
                ),
                "AppPath" => array(
                    "cache"      => APP_PATH . "caches" . DIRECTORY_SEPARATOR,
                    "config"     => APP_PATH . "configs" . DIRECTORY_SEPARATOR,
                    "controller" => APP_PATH . "controllers" . DIRECTORY_SEPARATOR,
                    "core"       => APP_PATH . "cores" . DIRECTORY_SEPARATOR,
                    "data"       => APP_PATH . "data" . DIRECTORY_SEPARATOR,
                    "lib"        => APP_PATH . "libs" . DIRECTORY_SEPARATOR,
                    "plugin"     => APP_PATH . "plugins" . DIRECTORY_SEPARATOR,
                )
            );
        }
    }

    /**
     * 执行函数 等同 call_user_func
     * @param   callback    $function   函数名称
     * @param   mixed       $parameter  参数
     * @return  true/false
     */
    public function execute(){
        $args = func_get_args();
        $func = array_shift($args);
        if(!$this->check($func)){
            return false;
        }else{
            call_user_func_array($func, $args);
            return true;
        }
    }

    /**
     * 判断函数是否存在
     * @param   callback    $function   函数名称
     * @return  true/false
     */
    public function check($func = array()){
        //判断是否数组
        //数组表示类函数
        if(is_array($func)){
            $methods = get_class_methods($func[0]);
            if(is_array($methods)){
                return in_array($func[1], $methods);
            }else{
                return false;
            }
        }else{
            return function_exists($func);
        }
    }

    /**
     * 加载PHP文件
     */
    public function load(){
        $files = func_get_args();
        foreach($files as $file)
        {

        }
    }

    /**
     * 加载一次PHP文件
     */
    public function load_once(){
        $files = func_get_args();
        foreach($files as $file)
        {

        }
    }

    /**
     * 加载系统函数
     * @param   string  $func   函数名
     * @param   string  $path   自定义目录 FR_PATH/cores/*
     * @param   string  $suf    文件后缀
     * @return  true/false
     */
    public function func($func, $path = '', $suf = '.func.php'){
        if(empty($func))
            return false;
        $filepath = (empty($path) ? "" : $path . DIRECTORY_SEPARATOR) . $func . $suf;
        if(isset($this->file)){
            $filepath = $this->file->formatPath($filepath, $this->_var["syspath"]["core"]);
        }else{
            $filepath = $this->_var["syspath"]["core"] . $filepath;
        }
        return $this->_func($filepath);
    }

    /**
     * 加载应用函数
     * @param   string  $func   函数名
     * @param   string  $path   自定义目录  APP_PATH/cores/*
     * @param   string  $suf    文件后缀
     * @return  true/false
     */
    public function funcApp($func, $path = '', $suf = '.func.php'){
        if(empty($func))
            return false;
        $filepath = $this->file->formatPath($path . DIRECTORY_SEPARATOR . $func . $suf, $this->_var["directory"]["core"]);
        return $this->_func($filepath);
    }

    /**
     * 加载插件函数
     * @param   string/array    $plugin     插件名，文件名
     *                          string      插件名 + core.php
     *                          array       插件名 + 文件名.php
     * @param   string          $path       自定义目录 APP_PATH/plugins/$plug/*
     * @param   string          $suf        文件后缀
     * @return  true/false
     */
    public function funcPlugin($plugin, $path = '', $suf = '.php'){
        if(empty($plugin))
            return false;
        if(!is_array($plugin)){
            $plugin = explode(",", $plugin);
        }
        if(empty($plugin[1])){
            $plugin[1] = "core";
        }
        $filepath = $plugin[0] . DIRECTORY_SEPARATOR . (empty($path) ? "" : $path . DIRECTORY_SEPARATOR) . $plugin[1] . $suf;
        $filepath = $this->file->formatPath($filepath, $this->_var["directory"]["plugin"]);
        return $this->_func($filepath);
    }

    /**
     * 加载自定义路径函数
     * @param   string  $file   文件名
     * @param   string  $path   自定义路径  APP_PATH/*
     * @param   string  $suf    文件名后缀
     * @return  true/false
     */
    public function funcDiy($file, $path = '', $suf = '.php'){
        if(empty($file)){
            return false;
        }
        $filepath = $this->file->formatPath($path . DIRECTORY_SEPARATOR . $file . $suf, APP_PATH);
        return $this->_func($filepath);
    }

    /**
     * 加载函数 私有
     * @param   string  $filepath   加载的函数
     * @return  true/false
     */
    private function _func($filepath){
        static $funcs = array();
        $key = md5($filepath);
        if(!empty($funcs[$key])){
            $this->debug->msg("load func $filepath isset", 1);
            return true;
        }
        if(file_exists($filepath)){
            include_once $filepath;
            $funcs[$key] = true;
        }else{
            $this->debug->msg("load func $filepath empty");
            $funcs[$key] = false;
        }
        return $funcs[$key];
    }

    /**
     * 加载系统类
     * @param   string/array    $class          类名
     *                          string          类名=文件名
     *                          array           array(文件名,类名)
     * @param   array           $setting        参数
     * @param   int             $initialize     是否实例化
     * @param   string          $path           自定义路径 FR_PATH/libs/*
     * @param   string          $suf            后缀名
     * @return  new class/true/false            实例化/成功/失败
     */
    public function lib($class, $setting = null, $initialize = 1, $path = '', $suf = '.class.php'){
        if(empty($class))
            return false;
        $class = strToArray(",", $class, 2);
        $classname = empty($class[1]) ? $class[0] : $class[1];
        $filepath = (empty($path) ? "" : $path . DIRECTORY_SEPARATOR) . $class[0] . $suf;
        if(isset($this->file)){
            $filepath = $this->file->formatPath($filepath, $this->_var["syspath"]["lib"]);
        }else{
            $filepath = $this->_var["syspath"]["lib"] . $filepath;
        }
        return $this->_lib($classname, $filepath, $setting, $initialize);
    }

    /**
     * 加载应用类
     * @param   string          $class          类名
     *                          string          类名=文件名
     *                          array           array(文件名,类名)
     * @param   array           $setting        参数
     * @param   int             $initialize     是否实例化
     * @param   string          $path           自定义路径 APP_PATH/libs/*
     * @param   string          $suf            后缀名
     * @return  new class/true/false            实例化/成功/失败
     */
    public function libApp($class, $setting = null, $initialize = 1, $path = '', $suf = '.class.php'){
        if(empty($class))
            return false;
        $class = strToArray(",", $class, 2);
        $classname = empty($class[1]) ? $class[0] : $class[1];
        $filepath = $this->file->formatPath($path . DIRECTORY_SEPARATOR . $class[0] . $suf, $this->_var["directory"]["lib"]);
        return $this->_lib($classname, $filepath, $setting, $initialize);
    }

    public function hook($hook, $setting = null, $path = "", $suf = ".hook.php"){
        if(empty($hook)){
            return false;
        }
        $filepath = $this->file->formatPath($path . DIRECTORY_SEPARATOR . $hook . $suf, $this->_var["directory"]["hook"]);
        return $this->_lib($hook, $filepath, $setting);
    }

    /**
     * 加载插件类
     * @param   string/array    $class
     *                          string          类名=文件名
     *                          array           array(文件名,类名)
     * @param   string          $plugin         插件名
     * @param   string/array    $setting        参数
     * @param   int             $initialize     是否实例化
     * @param   string          $path           自定义路径 APP_PATH/plugins/$api/*
     * @param   string          $suf            后缀名
     * @return  new class/true/false
     */
    public function libPlugin($class, $plugin = '', $setting = null, $initialize = 1, $path = '', $suf = '.class.php'){
        if(empty($class))
            return false;
        $class = strToArray(",", $class, 2);
        $classname = empty($class[1]) ? $class[0] : $class[1];
        if(empty($plugin)){
            $trace = debug_backtrace();
            $plugin = str_replace("_plugin", "", $trace[1]["class"], $count);
            if(empty($count)){
                return false;
            }
        }
        $filepath = $plugin . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $class[0] . $suf;
        $filepath = $this->file->formatPath($filepath, $this->_var["directory"]["plugin"]);
        return $this->_lib($classname, $filepath, $setting, $initialize);
    }

    /**
     * 加载控制台
     * @param   string          $m              控制台
     * @param   string          $c              操作
     * @param   array           $setting        参数
     * @param   string          $suf            后缀
     * @return  new class/true/false            实例化/成功/失败
     */
    public function control($m, $c, $setting = null, $suf = '.class.php'){
        if(empty($m) || empty($c))
            return false;
        $filepath = $m . DIRECTORY_SEPARATOR . $c . $suf;
        $filepath = $this->file->formatPath($filepath, $this->_var["directory"]["controller"]);
        $class = $c . "_controller";
        return $this->_lib($class, $filepath, $setting, 1);
    }

    /**
     * 加载插件
     * @param   string/array    $plugin         插件            类名=文件名."_plugin"
     *                          string          插件目录 = 文件名
     *                          array           array(插件目录,文件名)
     * @param   array           $setting        参数
     * @param   string          $suf            后缀
     * @return  new class/true/false            实例化/成功/失败
     */
    public function plugin($plugin, $setting = null, $suf = ".plugin.php"){
        if(empty($plugin))
            return false;
        $plugin = strToArray(",", $plugin, 2);
        $filename = empty($plugin[1]) ? $plugin[0] : $plugin[1];
        $class = $filename . "_plugin";
        $filepath = $plugin[0] . DIRECTORY_SEPARATOR . $filename . $suf;
        $filepath = $this->file->formatPath($filepath, $this->_var["directory"]["plugin"]);
        return $this->_lib($class, $filepath, $setting, 1);
    }

    /**
     * 加载自定义类
     * @param   string          $classname      类名 new $classname
     *                          string          类名=文件名
     *                          array           array(文件名,类名)
     * @param   string          $path           自定义目录
     * @param   array           $setting        配置信息
     * @param   int             $initialize     是否实例化
     * @param   string          $suf            后缀名
     * @return  new class/true/false            实例化/成功/失败
     */
    public function libDiy($class, $path = '', $setting = null, $initialize = 1, $suf = '.php'){
        if(empty($class))
            return false;
        $class = strToArray(",", $class, 2);
        $classname = empty($class[1]) ? $class[0] : $class[1];
        $filepath = $path . DIRECTORY_SEPARATOR . $class[0] . $suf;
        $filepath = $this->file->formatPath($filepath, APP_PATH);
        return $this->_lib($classname, $filepath, $setting, $initialize);
    }

    /**
     * 加载类 私有
     * @param   string          $classname      类名
     * @param   string          $filepath       类文件地址
     * @param   array           $setting        配置信息
     * @param   int             $initialize     是否实例化
     * @return new class/true/false             实例化/成功/失败
     */
    private function _lib($classname, $filepath, $setting = null, $initialize = 1){
        static $classes = array();
        $key = md5($filepath);
        //已经加载过
        if(!empty($classes[$key])){
            //需要实例化
            if($initialize){
                $classes[$key] = new $classname($setting);
            }
            return $classes[$key];
        }
        if(file_exists($filepath)){
            include $filepath;
            //类不存在
            if(!class_exists($classname)){
                trigger_error("");
                //$this->debug->msg("$filepath class $classname empty");
                return false;
            }
            //需要实例化
            if($initialize){
                $classes[$key] = new $classname($setting);
            }else{
                $this->execute(array($classname, '__init'), $setting);
                $classes[$key] = true;
            }
        }else{
            $classes[$key] = false;
            $this->debug->msg("$filepath file class empty");
        }
        return $classes[$key];
    }
}
?>