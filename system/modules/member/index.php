<?php
defined('IN_YS20') or exit('No permission resources.');
class index {
    public $user='';
    function __construct() {
		$this->member = ys_base::load_model('member_model');
        $this->ip=ys_base::load_model('ip_model');
        $this->user=$this->member->islogin();
        if(!$this->user){
            showmessage("未登陆",WEBPATH.SELF."?m=member&c=login");
        }
	}
    public function init(){
    }
}
?>