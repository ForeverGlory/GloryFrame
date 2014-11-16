<?php
return array(
'errorlog' => 1, //1、保存错误日志到 cache/error/日期_error_log.php | 0、在页面直接显示
'charset' => 'utf-8', //网站字符集
'timezone' => 'Etc/GMT-8', //网站时区（只对php 5.1以上版本有效），Etc/GMT-8 实际表示的是 GMT+8
'version'=>'3.0',
'lock_ex' => '1',  //写入缓存时是否建立文件互斥锁定（如果使用nfs建议关闭）
'errorlog'=>'1', //是否启用错误显示 0 直接前台显示，1 记录在日志文件中
'errormysql'=>'1', //数据库错误信息显示  0 直接显示，1 不显示
//Cookie配置
'cookie_domain' => '', //Cookie 作用域
'cookie_path' => '', //Cookie 作用路径
'cookie_pre' => 'forever_', //Cookie 前缀，同一域名下安装多套系统时，请修改Cookie前缀
'cookie_ttl' => 3600, //Cookie 生命周期，0 表示随浏览器进程
'auth_key' => '0.123456789987654321.0',

//模板相关配置
'tpl_root' => 'templates/', //模板保存物理路径
'js_path' => 'http://cs/javascript/', //CDN JS
'css_path' => 'http://cs/themes/', //CDN CSS
'img_path' => 'http://cs/themes/images/', //CDN img

'debug' => 1, //是否显示调试信息
'admin_log' => 0, //是否记录后台操作日志
'gzip' => 1, //是否Gzip压缩后输出
'admin_founders' => '1', //网站创始人ID，多个ID逗号分隔
'regcheck'=>array("username","password","email","phone","groupid","power","regtime","regip"),
);
?>