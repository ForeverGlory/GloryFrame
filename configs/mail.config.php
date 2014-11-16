<?php
/**
 * mail.config.php  邮件配置信息
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/configs/mail.config.php
 * @version         $
 */
defined("IN_FR") or exit();
return array(
    'default' => array(
        /**
         * 是否验证
         */
        "Auth"     => 1,
        /**
         * 发件服务器
         */
        "Host"     => "smtp.exmail.com",
        /**
         * 发信端口
         */
        'Port'     => 25,
        /**
         * 登陆帐号
         */
        'Username' => 'root',
        /**
         * 登陆密码
         */
        'Password' => '',
        /**
         * 发件地址
         */
        'From'     => "root@localhost",
        /**
         * 发件人
         */
        "FromName" => "root",
        /**
         * 回复地址
         * name <name@localhost>;name1 <name1@localhost>
         */
        "ReplyTo"  => "",
        /**
         * 邮箱编码
         */
        "CharSet"  => "utf-8",
        /**
         * 网页格式
         */
        "IsHTML"   => 1,
    )
);
?>