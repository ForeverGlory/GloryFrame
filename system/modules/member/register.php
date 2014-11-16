<?php
defined('IN_YS20') or exit('No permission resources.');
class register {
    function __construct() {
		$this->db = ys_base::load_model('member_model');
	}
    public function init(){
        if($_POST){
            $reginfo=$this->db->checkreg($_POST,$checkresult);
            if(!$checkresult){
                showmessage("该用户已经存在");
            }else{
                if($checkresult<0){
                    showmessage("请正确填写注册信息");
                }
            }
            $uid=$this->db->register($reginfo);
            if($uid){
                if($uid<0){
                    showmessage("用户正在审核，请与管理员联系",WEBPATH.SELF."?m=member&c=login");
                }else{
                    showmessage("注册成功",WEBPATH.SELF);
                }
            }else{
                showmessage("注册失败");
            }
        }else{
            $group=getcache("group");
            include template("register");
        }
    }
}
?>