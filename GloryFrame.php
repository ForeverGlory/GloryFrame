<?php

/**
 * 框架入口类
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/base.php
 * @version         $
 */
/**
 * IN_FR 是否基于框架
 */
define("IN_FR", true);
/* 应用主目录 */
defined("APP_ROOT") || define("APP_ROOT", dirname(__DIR__) . DIRECTORY_SEPARATOR);
/* 应用目录 */
defined("APP_PATH") || define("APP_PATH", APP_ROOT . "application" . DIRECTORY_SEPARATOR);
/* 框架目录 */
defined("FR_PATH") || define("FR_PATH", __DIR__ . DIRECTORY_SEPARATOR);

/**
 * 基础类 加载的类、基本变量都在
 */
class GloryFrame {

    /**
     * 初始化应用程序
     * @param   array   $setting    模块 (应用/插件) 权重大于 $_GET $_POST
     *          array(
     *              ['controller']  =>  array       模块操作 array( m => '模块' , c => '控制' , a=> '操作' )
     *              ['get']     =>  array           GET数据
     *              ['post']    =>  array           POST数据
     *              ['cookie']  =>  array           COOKIE数据
     *              ['session'] =>  array           SESSION数据
     *              ['server']  =>  array           SERVER数据
     *              ['file']    =>  array           FILE数据
     *              ['arg']     =>  array           cli参数
     *              ['path']    =>  array           FramePath => array( key => val ),AppPath => array( key => val )
     *              ['config']  =>  array           配置信息 file => array( key => val )
     *          )
     */
    public static function createApp($setting = array()) {
        static $isFirst = true;
        if ($isFirst) {
            //GloryFrame::Auto("");
            //全局目录变量
            $path = array(
                "FramePath" => array(
                    "config" => FR_PATH . "configs" . DIRECTORY_SEPARATOR,
                    "core" => FR_PATH . "cores" . DIRECTORY_SEPARATOR,
                    "data" => FR_PATH . "data" . DIRECTORY_SEPARATOR,
                    "hook" => FR_PATH . "hooks" . DIRECTORY_SEPARATOR,
                    "lang" => FR_PATH . "langs" . DIRECTORY_SEPARATOR,
                    "lib" => FR_PATH . "libs" . DIRECTORY_SEPARATOR
                ),
                "AppPath" => array(
                    "cache" => APP_PATH . "caches" . DIRECTORY_SEPARATOR,
                    "config" => APP_PATH . "configs" . DIRECTORY_SEPARATOR,
                    "controller" => APP_PATH . "controllers" . DIRECTORY_SEPARATOR,
                    "core" => APP_PATH . "cores" . DIRECTORY_SEPARATOR,
                    "data" => APP_PATH . "data" . DIRECTORY_SEPARATOR,
                    "lang" => APP_PATH . "langs" . DIRECTORY_SEPARATOR,
                    "lib" => APP_PATH . "libs" . DIRECTORY_SEPARATOR,
                    "plugin" => APP_PATH . "plugins" . DIRECTORY_SEPARATOR,
                    "template" => APP_PATH . "templates" . DIRECTORY_SEPARATOR
                )
            );
            include FR_PATH . 'libs' . DIRECTORY_SEPARATOR . 'load.class.php';
            $load = new load(empty($setting["path"]) ? array() : $setting["path"]);
            //加载基本类
            $load->lib("file");
            $load->lib("config");
            $load->lib("debug");
            //启用应用类
            $load->lib("application", $setting);
            $isFirst = false;
        }
    }

    /**
     * 将系统类、变量自动加载到类里
     * @param   object/array/string     $class      传入当前类 / 类名，返回类
     *                                  object      默认类 类名使用 get_class($class) 获取
     *                                  array       array(classname,class)
     *                                  string      classname 返回类
     * @param   int                     $option     传入类选项
     *                                  0           当前类存储到集合里  ,当前类可引用集合里类
     *                                  1           当前类不存储到集合里,当前类可引用集合里类
     *                                  2           当前类存储到集合里  ,当前类不可引用集合里类
     */
    public static function Auto($class, $option = 0) {
        static $classes = array();
        static $variable = array();
        static $isFirst = true;
        if ($isFirst) {
            //第一次加载时，取部分变量
            $variable["starttime"] = microtime();
            $variable["systime"] = time();
            //系统目录
            $variable["syspath"] = array(
                "config" => FR_PATH . "configs" . DIRECTORY_SEPARATOR,
                "core" => FR_PATH . "cores" . DIRECTORY_SEPARATOR,
                "lib" => FR_PATH . "libs" . DIRECTORY_SEPARATOR,
                "lang" => FR_PATH . "langs" . DIRECTORY_SEPARATOR,
                "data" => FR_PATH . "data" . DIRECTORY_SEPARATOR,
            );
            $isFirst = false;
        }
        if (is_string($class)) {
            return $classes[$class];
        } else {
            if (is_array($class)) {
                $classname = $class[0];
                $class = &$class[1];
            } else {
                $classname = get_class($class);
            }
            if (!empty($var) && is_array($var)) {
                $variable = arrayMerge($variable, $var, array("starttime", "systime", "syspath"));
            }
            if ($option != 2) {
                $class->_var = &$variable;
            }
            if ($option != 1) {
                $classes[$classname] = $class;
            }
        }
        foreach ($classes as $key => $val) {
            if ($key != $classname) {
                if ($option != 1) {
                    $classes[$key]->$classname = $class;
                }
                if ($option != 2) {
                    $class->$key = $classes[$key];
                }
            }
        }
    }

}