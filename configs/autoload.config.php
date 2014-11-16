<?php
/**
 * autoload.config.php  自动加载的文件
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/configs/autoload.config.php
 * @version         $
 */
defined("IN_FR") or exit();
return array(
    /**
     * 自动加载的配置文件
     * array($config1,$config2,$config3...)
     */
    'config' => array(),
    /**
     * 自动加载的函数
     * array(
     *      "sys"=>"$func1,$func2,$func3...",
     *      "app"=>"$func1,$func2,$func3..."
     * )
     */
    'func' => array(),
    /**
     * 自动加载类
     * array(
     *      //加载系统类
     *      "sys"=>"$class1,$class2,$class3...",
     *      //加载应用类
     *      "app"=>"$class1,$class2,$class3...",
     *      //加载系统类,并实例化
     *      "newsys"=>"$class1,$class2,$clss3...",
     *      //加载应用类,并实例化
     *      "newapp"=>"$class1,$class2,$clss3...",
     * )
     */
    'class' => array(),
    /**
     * 自动加载插件
     * array(
     *      $plugin1,$plugin2,$plguin3...
     * )
     */
    'plugin' => array(),
    /**
     * 自动加载语言包
     * array(
     *      $lang1,$lang2,$lang3...
     * )
     */
    'lang' => array()
);
?>