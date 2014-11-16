<?php
/**
 * view.config.php  模板配置
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/configs/view.config.php
 * @version         $
 */
defined("IN_FR") or exit();
return array(
    "default" => array(
        /**
         * 模板主题
         */
        "style"       => "default",
        /**
         * 模板后缀
         */
        "suf"         => ".tpl.php",
        /**
         * 过期时间(更改模板均会更改)
         * 0    表示永不过期
         * <0   访问就更改
         * >0   单位秒
         */
        "expire"      => 0,
        /**
         * 模板输出格式
         */
        "contentType" => "text/html",
        /**
         * 模板输出编码
         */
        "charset"     => "utf-8",
        /**
         * 标签分界
         */
        "delimiter"   => array("{", "}"),
        /**
         * 模板禁用函数
         */
        "deny_func" => "",
        /**
         * 模板错误页
         */
        "error"     => "error",
    )
);
?>