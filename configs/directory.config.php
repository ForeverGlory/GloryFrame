<?php
/**
 * directory        目录配置
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/configs/directory.config.php
 * @version         $
 */
defined("IN_FR") or exit();
return array(
    /**
     * 配置目录 (修改无效)
     */
    'config' => "configs",
    /**
     * 缓存目录
     */
    'cache'  => array(
        "base"       => "caches",
        //临时目录
        "temp"       => "temp",
        //日志目录
        "log"        => "logs",
        //缓存目录  $this->cache->
        "cache"      => "caches",
        //会话目录
        "session"    => "sessions",
        //模板缓存目录
        "tpl"        => "tpls",
    ),
    /**
     * 用户函数库目录
     */
    'core'       => "cores",
    /**
     * 用户类库目录
     */
    'lib'        => "libs",
    /**
     * 钩子类目录
     */
    'hook'       => "hooks",
    /**
     * 用户语言包目录
     */
    'lang'       => "langs",
    /**
     * 数据目录
     */
    'data'       => "data",
    /**
     * 用户控制台目录
     */
    'controller' => "controllers",
    /**
     * 用户模板目录
     */
    'template'   => "templates",
    /**
     * 用户插件目录
     */
    'plugin'     => "plugins",
);
?>
