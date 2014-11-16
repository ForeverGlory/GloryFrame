<?php
/**
 * plugin.clas.php  插件控制类
 * 需要继承
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/libs/plugin.class.php
 * @version         $
 */
abstract class plugin
{
    /**
     * 配置信息
     * @var     array
     */
    protected $setting = array();
    protected $pluginName = null;

    /**
     * 实例化控件，不可继承
     */
    final function __construct($setting = null)
    {
       GloryFrame::Auto($this, 1);
        $this->pluginName = str_replace("_plugin", "", get_class($this));
        if(!is_array($setting))
        {
            //传递配置文件
            $this->setting = $this->config->plugin($this->pluginName, array($setting));
        }
        else
        {
            $this->setting = $setting;
        }
        $this->initialize();
    }

    /**
     * 通用函数，执行操作时，将先执行该函数
     */
    public function initialize()
    {
        //此方法需要继承
    }

}
?>