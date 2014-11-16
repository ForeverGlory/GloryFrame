<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>登陆-库存工单管理系统</title>
<link href="themes/css/login.css" rel="stylesheet" type="text/css" />
</head>

<body>
	<div id="login">
		<div id="login_header">
			<h1 class="login_logo">
				<a href="/" title="库存工单管理系统"><img src="themes/default/images/logo.gif" /></a>
			</h1>
			<div class="login_headerContent">
				<div class="navList">
					<ul>
						<li><a href="?m=member&c=login">登陆</a></li>
                        <li><a href="?m=member&c=register">注册</a></li>
						<li><a href="mailto:foreverglory@qq.com">反馈</a></li>
					</ul>
				</div>
				<h2 class="login_title"><img src="themes/default/images/login_title.gif"/></h2>
			</div>
		</div>
		<div id="login_content">
			<div class="loginForm">
				<form action="" method="post">
					<p>

						<label>用户：</label>
						<input type="text" class="login_input" name="username" />
					</p>
					<p>
						<label>密码：</label>
						<input type="password" size="20" class="login_input" name="password" />
					</p>
					<div class="login_bar">
						<input class="login" type="submit" value="" />
                        <input class="register" type="button" value="" onclick="window.location='?m=member&c=register'"/>
					</div>
				</form>
			</div>
			<div class="login_banner"><img src="themes/default/images/login_banner.jpg" /></div>
			<div class="login_main">
			</div>
		</div>
		<div id="login_footer">
			Copyright  2011-2012 &copy; <a href="http://www.zmcloud.com" target="_blank">zmcloud</a> Inc. All Rights Reserved.
		</div>
	</div>

</body>
</html>