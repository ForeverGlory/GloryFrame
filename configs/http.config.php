<?php
/**
 * http.config.php  HTTP模拟请求配置
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/configs/http.config.php
 * @version         $
 */
defined("IN_FR") or exit();
return array(
    'default' => array(
        /**
         * 请求超时
         */
        "Timeout"         => "30",
        /**
         * 队列
         * 0 非阻塞
         * 1 阻塞
         */
        "Block"           => 1,
        /**
         * 重试次数
         */
        "Retry"           => 1,
        /**
         * 请求内容格式
         */
        "Accept"          => "*/*",
        /**
         * 请求语种
         */
        "Accept-Language" => "zh-cn",
        /**
         * 浏览器、操作系统
         */
        "User-Agent"      => "GloryFrame",
        /**
         * 来路
         */
        "Referer"         => "",
        /**
         * 连接方式
         */
        "Connection"      => "Close",
        /**
         * 缓存
         */
        "Cache-Control"   => "no-cache",
        /**
         * 模拟请求伪IP
         * //todo 未实现
         */
        "Request_Ip"      => "",
    )
);
?>