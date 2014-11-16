<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=7" />
<meta name="ROBOTS" content="NOINDEX, NOFOLLOW" />
<title>库存工单管理系统-兆民云计算有限公司</title>
<link href="themes/default/style.css" rel="stylesheet" type="text/css" />
<link href="themes/css/core.css?version=<?=$jsversion?>" rel="stylesheet" type="text/css" />
<!--[if IE]>
<link href="themes/css/ieHack.css?version=<?=$jsversion?>" rel="stylesheet" type="text/css" />
<![endif]-->

<script src="javascripts/jquery-1.7.min.js" type="text/javascript"></script>
<!--script src="javascripts/jquery.cookie.js" type="text/javascript"></script-->
<script src="javascripts/jquery.validate.js" type="text/javascript"></script>
<script src="javascripts/jquery.bgiframe.js" type="text/javascript"></script>

<script src="xheditor/xheditor-1.1.10-zh-cn.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.core.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.util.date.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.validate.method.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.regional.zh.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.barDrag.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.drag.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.tree.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.accordion.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.ui.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.theme.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.switchEnv.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.alertMsg.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.contextmenu.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.navTab.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.tab.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.resize.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.jDialog.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.dialogDrag.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.cssTable.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.stable.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.taskBar.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.ajax.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.pagination.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.database.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.datepicker.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.effects.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.panel.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.checkbox.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.history.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.combox.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/dwz.regional.zh.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script src="javascripts/jquery.messager.js" type="text/javascript"></script>
<script src="javascripts/function.js?version=<?=$jsversion?>" type="text/javascript"></script>
<script type="text/javascript">
$(function(){
	DWZ.init("zm.frag.xml", {
		statusCode:{ok:200, error:300, timeout:301}, //【可选】
		pageInfo:{pageNum:"pageNum", numPerPage:"numPerPage", orderField:"orderField", orderDirection:"orderDirection"},
		debug:false,	// 调试模式 【true|false】
		callback:function(){
			initEnv();
			$("#themeList").theme({themeBase:"themes"});
                        sd = new FUI.soundComponent({src:'themes/sound/pm_1.wav',altSrc:'themes/sound/pm_1.mp3'});
		}
	});
    setTimeout("setTree()",200);
    setTimeout("message()",2000);
});
</script>
</head>

<body scroll="no">
	<div id="layout">
		<div id="header">
			<div class="headerNav">
                <ul class="welcome">
                    <li>欢迎使用</li>
                    <li><?=$this->user[username]?></li>
                    <!--li><a href="#">2011</a></li-->
                </ul>
				<ul class="nav">
                    <li><a href="#" onclick="showmessage()">消息提示</a></li>
                    <li><a href="?m=member&c=edit" rel="member_edit" target="dialog" height="280" width="400">个人信息</a></li>
                    <li><a href="#" onclick="reloadnavtab(99)">刷新所有标签</a></li>
                    <li><a href="#" onclick="setTree()">刷新菜单栏</a></li>
                    <li><a href="?m=admin&c=cache&a=all" target="ajaxTodo">更新缓存</a></li>
					<li><a href="?m=member&c=logout">退出</a></li>
				</ul>
				<ul class="themeList" id="themeList">
					<li theme="default"><div class="selected">蓝色</div></li>
					<li theme="green"><div>绿色</div></li>
					<li theme="purple"><div>紫色</div></li>
					<li theme="silver"><div>银色</div></li>
					<li theme="azure"><div>天蓝</div></li>
				</ul>
			</div>
		</div>

		<div id="leftside">
			<div id="sidebar_s">
				<div class="collapse">
					<div class="toggleCollapse"><div></div></div>
				</div>
			</div>
			<div id="sidebar">
				<div class="toggleCollapse"><h2>菜单栏</h2><div>收缩</div></div>
                <div class="accordion" fillSpace="sideBar">
	               <div class="accordionHeader">
						<h2><span>Folder</span>菜单</h2>
					</div>
					<div class="accordionContent">
						<ul class="tree treeFolder">
							<li><a href="javascript:void();">菜单</a></li>
						</ul>
					</div>
                </div>
			</div>
		</div>
		<div id="container">
			<div id="navTab" class="tabsPage">
				<div class="tabsPageHeader">
					<div class="tabsPageHeaderContent"><!-- 显示左右控制时添加 class="tabsPageHeaderMargin" -->
						<ul class="navTab-tab">
							<li tabid="main" class="main"><a href="javascript:;"><span><span class="home_icon">系统首页</span></span></a></li>
						</ul>
					</div>
					<div class="tabsLeft">left</div><!-- 禁用只需要添加一个样式 class="tabsLeft tabsLeftDisabled" -->
					<div class="tabsRight">right</div><!-- 禁用只需要添加一个样式 class="tabsRight tabsRightDisabled" -->
					<div class="tabsMore">more</div>
				</div>
				<ul class="tabsMoreList">
					<li><a href="javascript:;">系统首页</a></li>
				</ul>
				
			</div>
		</div>

		<div id="taskbar" style="left:0px; display:none;">
			<div class="taskbarContent">
				<ul></ul>
			</div>
			<div class="taskbarLeft taskbarLeftDisabled" style="display:none;">taskbarLeft</div>
			<div class="taskbarRight" style="display:none;">taskbarRight</div>
		</div>
		<div id="splitBar"></div>
		<div id="splitBarProxy"></div>
	</div>

	<div id="footer">Copyright &copy; foreverglory@qq.com version <?=$version?></div>

<!--拖动效果-->
	<div class="resizable"></div>
<!--阴影-->
	<div class="shadow" style="width:508px; top:148px; left:296px;">
		<div class="shadow_h">
			<div class="shadow_h_l"></div>
			<div class="shadow_h_r"></div>
			<div class="shadow_h_c"></div>
		</div>
		<div class="shadow_c">
			<div class="shadow_c_l" style="height:296px;"></div>
			<div class="shadow_c_r" style="height:296px;"></div>
			<div class="shadow_c_c" style="height:296px;"></div>
		</div>
		<div class="shadow_f">
			<div class="shadow_f_l"></div>
			<div class="shadow_f_r"></div>
			<div class="shadow_f_c"></div>
		</div>
	</div>
	<!--遮盖屏幕-->
	<div id="alertBackground" class="alertBackground"></div>
	<div id="dialogBackground" class="dialogBackground"></div>

	<div id='background' class='background'></div>
	<div id='progressBar' class='progressBar'>数据加载中，请稍等...</div>

</body>
</html>