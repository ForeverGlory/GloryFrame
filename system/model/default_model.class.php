<?php
defined('IN_YS20') or exit('No permission resources.');

/**
 * 内容模型数据库操作类
 */
ys_base::load_sys_class('model', '', 0);
class default_model extends model
{
    public function __construct()
    {
        $this->db_config = ys_base::load_config('database');
        $this->db_setting = 'default'; //配置数据库
        parent::__construct();
    }
}
?>