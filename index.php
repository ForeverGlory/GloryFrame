<?php
/**
 *  index.php 文件入口 操作
 *
 * @author			foreverglory@qq.com
 * @license			http://ys20.cn
 * @lastmodify		2011-4-22
**/
//网站根目录
define('YS_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);
include YS_PATH.'system'.DIRECTORY_SEPARATOR.'common.php';
ys_base::creat_app();
?>