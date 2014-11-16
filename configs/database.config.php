<?php
/**
 * 数据库连接配置
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/configs/database.config.php
 * @version         $
 */
defined("IN_FR") or exit();
return array(
    'default' => array(
        /**
         * 数据库类型
         */
        'type'        => 'mysql',
        /**
         * 数据库服务器IP或域名
         */
        'hostname'    => 'localhost',
        /**
         * 数据库表
         */
        'database'    => 'frame',
        /**
         * 登陆用户
         */
        'username'    => 'root',
        /**
         * 登陆密码
         */
        'password'    => '',
        /**
         * 数据库表前缀
         * 同一数据库下多套系统，请使用不同表前缀
         */
        'tablepre'    => '',
        /**
         * 数据库编码
         */
        'charset'     => 'utf8',
        /**
         * 是否调试
         */
        'debug'       => false,
        /**
         * 是否持久连接
         */
        'pconnect'    => 0,
        /**
         * 是否自动连接
         */
        'autoconnect' => 0
    ),
);
?>