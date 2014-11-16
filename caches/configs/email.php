<?php
//发送邮件格式 name <email> or array("name <email>");
return array(
'mail_server' => 'smtp.example.com',
'mail_port' => 25,//发信端口
'mail_auth' => 1,//是否验证
'mail_user' => 'send@example.com',//登陆帐号
'mail_password' => '',//登陆密码
'mail_from' => "Example <send@example.com>",//发件人
'charset'=>CHARSET,
'sitename'=>"",//邮件头标识
);
?>