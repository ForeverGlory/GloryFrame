<?php
/**
 * 路由配置文件
 * m 为模型,c 为控制器,a 为事件
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/configs/route.config.php
 * @version         $
 */
defined("IN_FR") or exit();
return array(
    'default' => array(
        /**
         * web方式       get/post
         * demo: {PATH}{FILE}?m={m}&c={c}&a={a} == /index.php?m=main&c=index&a=init
         */
        'cgi' => array(
            'm'        => 'main',
            'c'        => 'index',
            'a'        => 'init',
            /**
             * URL重写模式
             *          0   {PATH}{FILE}?m={m}&c={c}&a={a}          不使用重写
             *          1   {PATH}{FILE}/{m}/{c}/{a}/{key}_{val}    $_SERVER[PATH_INFO]
             *          2   {PATH}{m}/{c}/{a}/{key}_{val}           默认重写规则
             *          3   自定义规则  后面 url 生效 并且使用URL重写
             */
            'urlmodel' => 0,
            /**
             * 自定义URL重写
             * @param   {m} {c} {a}
             *          "http://localhost/index.php?m={m}&c={c}&a={a}"
             * @return  空值时，直接输出demo样式
             */
            'url'      => ''
        ),
        /**
         * 命令行方式    $_SERVER[argv]
         * demo: php {PATH}{FILE} {m} {c} {a} == php /index.php main index cli
         */
        'cli'      => array(
            'm' => 'main',
            'c' => 'index',
            'a' => 'cli'
        )
    )
);
?>