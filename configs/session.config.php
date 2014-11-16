<?php
/**
 * Session配置
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/configs/session.config.php
 * @version         $
 */
defined("IN_FR") or exit();
return array(
    'default' => array(
        /**
         * 是否开启会话模式
         * 0 关闭SESSION 1 系统SESSION 2 使用COOKIE 代替SESSION
         */
        'issession'          => 1,
        /**
         * Session 名称 系统SESSION此项无效
         */
        'session_name'       => 'SESSION',
        /**
         * Session 生命周期
         */
        'session_ttl'        => 0,
        /**
         * Session 是否加密
         */
        'session_encrypt'    => 0,
        /**
         * Session 加密字符串
         */
        'session_key'        => '',
        /**
         * Session 保存方式
         * 会话模式为COOKIE时有效
         * 0 文件
         * 1 数据库
         */
        'session_savetype'   => 0,
        /**
         * Session 保存数据库名称
         * 保存方式为数据库时有效
         */
        'session_table'      => 'sessions',
        /**
         * Session 多久更新会话记录
         */
        'session_updatetime' => 0,
    )
);
?>