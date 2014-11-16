<?php
/**
 * 验证码配置
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/configs/checkcode.config.php
 * @version         $
 */
defined("IN_FR") or exit();
return array(
    "default" => array(
        /**
         * 画布长度
         */
        "width"      => 130,
        /**
         * 画布宽度
         */
        "height"     => 50,
        /**
         * 画布背景色
         */
        "backGround" => "#ffffFF",
        /**
         * 验证码长度
         */
        "codeLen"    => 4,
        /**
         * 字体大小
         */
        "fontSize"   => 24,
        /**
         * 字体颜色
         * 为空表示随机颜色
         */
        "fontColor"  => "",
        /**
         * 是否干扰
         */
        "isDisturb"  => 0,
    )
);
?>