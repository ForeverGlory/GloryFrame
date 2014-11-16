<?php
/**
 * 系统配置
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/configs/system.config.php
 * @version         $
 */
defined("IN_FR") or exit();
return array(
    /**
     * 网站编码
     */
    "charset"   => "utf-8",
    /**
     * 网站时区
     */
    "timezone"  => "Etc/GMT-8",
    /**
     * 启用gzip压缩
     */
    "gzip"      => 0,
    /**
     * 是否调试模式
     * 0 关闭调试模式
     * 1 出错调试
     * 2 全调试
     */
    "debug"     => 1,
    /**
     * 错误输出
     * 关闭调试模式才生效
     * 0 没有任何记录
     * 1 简单日志记录 (只显示错误日志)
     * 2 详细日志记录
     */
    "error"     => 2,
    /**
     * 默认首页
     */
    "indexpage" => "index.html index.htm index.php",
    /**
     * 默认类配置信息
     */
    "setting"   => array(
        /**
         * 路由配置
         */
        "route"     => "default",
        /**
         * 黑白名单
         */
        "acl"       => "default",
        /**
         * 语言包
         */
        "lang"      => "default",
        /**
         * 缓存
         */
        "cache"     => "default",
        /**
         * Cookie
         */
        "cookie"    => "default",
        /**
         * Session
         */
        "session"   => "default",
        /**
         * 数据库配置
         */
        "database"  => "default",
        /**
         * 模板配置
         */
        "view"      => "default",
        /**
         * 邮件配置
         */
        "mail"      => "default",
        /**
         * 模拟请求配置
         */
        "http"      => "default",
        /**
         * FTP配置
         */
        "ftp"       => "default",
        /**
         * 验证码
         */
        "checkcode" => "default",
    ),
);
?>