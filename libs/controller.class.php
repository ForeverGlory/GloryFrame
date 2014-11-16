<?php
/**
 * controller.clas.php	控制台类
 * 需要继承
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/libs/controller.class.php
 * @version         $
 */
abstract class controller{

    /**
     * 配置信息
     * @var type
     */
    protected $setting = array();

    /**
     * 实例化控制台 不可继承
     */
    final function __construct(){
        static $isFirst = true;
        if($isFirst){
            GloryFrame::Auto($this);
            $this->_var["place"]["m"] = $this->route->m();
            $this->_var["place"]["c"] = $this->route->c();
            $this->_var["place"]["a"] = $this->route->a();
            //获取配置文件
            $this->setting = $this->config->load($this->_var["place"]["m"], "", "", "controllers");
            //实例化
            $this->initialize();
            $isFirst = false;
        }
    }

    /**
     * 通用函数，执行操作时，将先执行该函数
     */
    public function initialize(){
        //此方法需要继承
    }
}
?>