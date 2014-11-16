<?php
/**
 * 控制台、插件黑白名单 访问控制列表
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/configs/acl.config.php
 * @version         $
 */
defined("IN_FR") or exit();
return array(
    'default' => array(
        /**
         * 启用 白名单 / 黑名单
         * 权重 白名单 > 黑名单
         * @param   string
         *          empty       不启用
         *          white/black 白名单/黑名单
         *          all         都启用 权重 白名单 > 黑名单
         */
        'use'   => '',
        /**
         * 白名单
         */
        'white' => array(
            // m1,m2,m3,...
            'control' => '',
            'plugin'  => ''
        ),
        /**
         * 黑名单
         */
        'black'   => array(
            'control' => '',
            'plugin'  => ''
        )
    )
);
?>