<?php
defined('IN_YS20') or exit('No permission resources.');
class logout {
    function __construct() {
		$this->db = ys_base::load_model('member_model');
	}
    public function init(){
        $this->db->logout();
        header("Location:".WEBPATH);
        showmessage("正在退出登陆",WEBPATH.SELF."?m=member&c=login");
    }
}
?>