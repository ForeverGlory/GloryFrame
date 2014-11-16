<?php
defined('IN_YS20') or exit('No permission resources.');
/**
 * 用户信息更改
 **/
class edit {
    public $user='';
    function __construct() {
        $this->member = ys_base::load_model('member_model');
		$this->work = ys_base::load_model('work_model');
        $this->user=$this->member->islogin();
        if(!$this->user){
            showmessage("未登陆",WEBPATH.SELF."?m=member&c=login");
        }
	}
    public function init(){
        if($_POST){
            $_POST[uid]=$this->user[uid];
            $_POST[username]=$this->user[username];
            $uid=$this->member->edituser($_POST);
            if($uid){
                $this->member->memberlog($this->user[username]."修改个人信息","edit","","user");
                ajaxDone(200,"修改成功");
            }else{
                ajaxDone(300,"修改失败");
            }
        }
        include template("member_edit");
    }
    /**
     * 更改主题
     **/
    public function theme(){
        if($themename=$_GET[theme]){
            $this->member->setTheme($this->user[uid],$themename);
        }else{
            $themename=$this->user[theme];
        }
        echo $themename;
    }
}
?>