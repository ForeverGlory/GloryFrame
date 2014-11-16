<?php
/**
 * 配置类
 * 获取配置、插件配置 初始化目录配置
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/libs/config.class.php
 * @version         $
 */
class config{

    /**
     * 配置文件数据
     * @var array   通过 md5(路径) 获取
     */
    private $configs = array();

    public function __construct(){
        static $isFirst = true;
        if($isFirst){
            GloryFrame::Auto($this);
            $this->_var["directory"]["config"] = APP_PATH . "configs" . DIRECTORY_SEPARATOR;
            //全局变量
            $var = $this->load("system");
            //应用目录
            $var["directory"] = $this->absoluePath($this->load("directory"));
            $this->_var = arrayMerge($var, $this->_var);
            $this->initConfig();
            $isFirst = false;
        }
    }

    /**
     * 初始化配置
     */
    private function initConfig(){
        date_default_timezone_set($this->_var["timezone"]);
    }

    /**
     * 返回绝对路径
     * @param   string/array    $directory
     * @param   strin           $add        追加目录
     * @return  string/array
     */
    private function absoluePath($directory, $add = ""){
        if(!is_array($directory)){
            $path = $add . DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR;
            $directory = $this->file->formatPath($path, APP_PATH);
        }else{
            $base = empty($directory["base"]) ? "" : $directory["base"] . DIRECTORY_SEPARATOR;
            foreach($directory as $k => $v)
            {
                if($k == "base"){
                    $directory[$k] = $this->absoluePath($v);
                }else{
                    $directory[$k] = $this->absoluePath($v, $base);
                }
            }
        }
        return $directory;
    }

    /**
     * 设置获取php.ini配置
     * @param   string  $varname    参数名
     * @param   string  $newvalue   参数值[为空表示获取]
     * @return  string/bool
     */
    public function ini($varname = null, $newvalue = null){
        $ini = null;
        if($varname){
            if(is_null($newvalue)){
                $ini = ini_get($varname) ? ini_get($varname) : ini_get_all($varname, false);
            }else{
                $ini = ini_set($varname, $newvalue);
            }
        }else{
            $ini = ini_get_all(null, false);
        }
        return $ini;
    }

    /**
     * 加载配置文件
     * @param   string          $file       配置文件
     * @param   string/array    $key        配置内容下标
     * @param   bool            $reload     是否强制刷新
     * @param   string          $path       自定义目录 APP_PATH/configs/*
     * @param   string          $suf        配置文件后缀
     * @return  array()
     */
    public function load($file, $key = null, $reload = false, $path = '', $suf = '.config.php'){
        $fr_path = $this->file->formatPath($file . '.config.php', $this->_var["syspath"]["config"]);
        $app_path = $this->file->formatPath($path . DIRECTORY_SEPARATOR . $file . $suf, $this->_var["directory"]['config']);
        if(!array_key_exists($md5 = md5($fr_path . $app_path), $this->configs) || $reload){
            $fr_config = $app_config = array();
            if(is_file($fr_path)){
                $fr_config = $this->file->read($fr_path, "array");
            }
            if(is_file($app_path)){
                $app_config = $this->file->read($app_path, "array", $reload);
            }
            $this->configs[$md5] = arrayMerge($fr_config, $app_config);
        }
        if(array_key_exists($md5, $this->configs)){
            $return = $this->configs[$md5];
            $return = arrayGetRecursion($return, $key);
        }
        return $return;
    }

    /**
     * 写入配置文件
     * @param   string  $file   配置文件
     * @param   array   $data   配置文件内容
     * @param   string  $path   自定义目录 APP_PATH/configs/*
     * @param   string  $suf    配置文件后缀
     * @return  true/false
     */
    public function make($file, $data = array(), $path = '', $suf = '.config.php'){
        $app_path = $this->file->formatPath($path . DIRECTORY_SEPARATOR . $file . $suf, $this->_var["directory"]['config']);
        $write = $this->file->write($app_path, $data, 'array', true);
        if($write){
            $md5 = md5($app_path);
            $this->configs[$md5] = $data;
        }
        return $write;
    }

    /**
     * 加载插件配置
     * @param   string/array    $plugin     插件名，配置名
     *                          string      插件名 + config.php
     *                          array       插件名 + 文件名.config.php
     * @param   string/array    $key        配置下标
     * @param   bool            $reload     是否强制读取
     * @param   string          $path       自定义目录  APP_PATH/"plugin"/API/*
     */
    public function plugin($plugin, $key = null, $reload = false, $path = ''){
        $plugin = strToArray(",", $plugin);
        $filePath = $this->file->formatPath($plugin[0] . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . (empty($plugin[1]) ? "" : $plugin[1] . ".") . "config.php", $this->_var["directory"]["plugin"]);
        $md5 = md5($filePath);
        if(!array_key_exists($md5 = md5($filePath), $this->configs) || $reload){
            if(file_exists($filePath)){
                $this->configs[$md5] = include $filePath;
            }
        }
        if(isset($this->configs[$md5])){
            $return = $this->configs[$md5];
            $return = arrayGetRecursion($return, $key);
        }
        return $return;
    }

    /**
     * //todo 写入插件配置文件
     * @param   string/array    $plugin     插件名，配置名
     *                          string      插件名 + config.php
     *                          array       插件名 + 文件名.config.php
     * @param   array           $data       配置文件内容
     * @param   string          $path       自定义目录  APP_PATH/"plugin"/API/*
     */
    public function makePlugin($plugin, $data = array(), $path = ""){
        if(!is_array($plugin)){
            $plugin = explode(",", $plugin);
        }
        $filePath = $this->file->formatPath($plugin[0] . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . (empty($plugin[1]) ? "" : $plugin[1] . ".") . "config.php", $this->_var["directory"]["plugin"]);
        $write = $this->load->lib('file')->write($filePath, $data, 'array', true);
        if($write){
            $this->configs[md5($filePath)] = $data;
        }
        return $write;
    }
}
?>