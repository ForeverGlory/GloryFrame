<?php
/**
 * cache.config.php 缓存配置
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/configs/cache.config.php
 * @version         $
 */
defined("IN_FR") or exit();
return array(
    'default' => array(
        /**
         * 写入缓存时是否建立文件互斥锁定 (如果使用nfs建立关闭)
         */
        'lock' => 1,
        /**
         * 缓存格式
         * array json ini(未实现) serialize txt
         */
        'type' => 'array',
        /**
         * 缓存后缀
         */
        'suf'  => '.cache.php',
    ),
);
?>