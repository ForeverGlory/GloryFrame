<?php
/**
 * SESSION处理类
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/libs/session.class.php
 * @version         $
 */
class session
{

    /**
     * 构造函数
     */
    public function __construct($setting = '')
    {
        static $isFirst = true;
        if($isFirst)
        {
           GloryFrame::Auto($this);
            if(empty($setting) || is_string($setting))
            {
                $setting = $this->config->load("session", array($setting));
            }
            $setting["path"] = $this->_var["directory"]["cache"]["session"];
			if(!$this->input->isCli() && $setting["issession"])
			{
				session_name($setting["session_name"]);
				session_save_path($setting["path"]);
				session_start();
			}
            $this->setting = $setting;
            $isFirst = false;
        }
    }

    public function set($key, $val)
    {
        $_SESSION[$key] = $val;
    }

    public function get($key)
    {
        return $_SESSION[$key];
    }

    public function del($key)
    {
        unset($_SESSION[$key]);
    }

    public function clear()
    {
        session_destroy();
    }

}
?>