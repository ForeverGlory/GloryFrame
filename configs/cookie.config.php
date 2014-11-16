<?php
/**
 * COOKIE配置
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/configs/cookie.config.php
 * @version         $
 */
defined("IN_FR") or exit();
return array(
    'default' => array(
        /**
         * 是否开启COOKIE
         */
        'iscookie'       => 1,
        /**
         * Cookie 作用域
         */
        'cookie_domain'  => '',
        /**
         * Cookie 作用路径
         */
        'cookie_path'    => '',
        /**
         * Cookie 前缀
         * 同一域名下多套系统，请使用不同Cookie前缀
         */
        'cookie_pre'     => '',
        /**
         * Cookie 生命周期
         * 0  表示随浏览器进程
         * >0 毫秒整数
         */
        'cookie_ttl'     => 0,
        /**
         * Cookie 是否加密
         * 0 不加密
         * 1 框架自带加密函数 encrypt::base64_Xor
         */
        'cookie_encrypt' => 1,
        /**
         * Cookie 加密字符串
         */
        'cookie_key'     => '',
    ),
);
?>