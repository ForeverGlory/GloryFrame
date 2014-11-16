<?php

defined('IN_YS20') or exit('No permission resources.');

class index {

    public $user = '';

    function __construct() {
        $this->member = ys_base::load_model('member_model');
        $this->user = $this->member->islogin();
        if (!$this->user) {
            header("Location:?m=member&c=login");
        }
    }

    function init() {
        include template("index");
    }

}

?>