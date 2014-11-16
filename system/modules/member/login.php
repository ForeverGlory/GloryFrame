<?php
defined('IN_YS20') or exit('No permission resources.');
class login {
    function __construct() {
		$this->db = ys_base::load_model('member_model');
	}
    public function init(){
        if($_POST){
            $uid=$this->db->login($_POST['username'],$_POST['password']);
            if($uid==0){
                showmessage("登陆失败");
            }elseif($uid==-1){
                showmessage("用户未审核，请与管理员联系");
            }else{
                header("Location:".WEBPATH);
                showmessage("登陆成功",WEBPATH);
            }
        }else{
            $user=$this->db->islogin();
            if($user){
                //已经登陆
            }else{

            }
            include template("login");
        }
    }
}
?>