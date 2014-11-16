<?php
/**
 * application.class.php 应用程序类
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/libs/application.class.php
 * @version         $
 */
class application{

    /**
     * 构造函数
     * @param   array   $setting    入口文件传递参数
     */
    public function __construct($setting = array()){
        static $isFirst = true;
        if($isFirst){
            GloryFrame::Auto($this);
            $this->check();
            $this->autoload();
            $this->route->initialize($setting);
            $this->control();
            $isFirst = false;
        }
    }

    /**
     * 应用自动加载基础类
     */
    private function autoload(){
        //加载控制台类 (不实例化) 使用时需要继承
        $this->load->lib("controller", "", 0);
        //加载插件类 (不实例化) 使用时需要继承
        $this->load->lib("plugin", "", 0);
        $autoload = array("lang", "server", "iconv", "encrypt", "verify", "input", "route", "cache", "database");
        if($this->config->load("cookie", array($this->_var["setting"]["cookie"], "iscookie"))){
            array_push($autoload, "cookie");
        }
        if($this->config->load("session", array($this->_var["setting"]["session"], "issession"))){
            array_push($autoload, "session");
        }
        array_push($autoload, "view", "output", "html");
        foreach($autoload as $v)
        {
            $this->load->lib($v);
        }
        $this->lang->load("base");
        $this->lang->load("frame");
        //配置文件加载
        $autoConfig = $this->config->load("autoload");
        foreach($autoConfig as $key => $val)
        {
            switch($key)
            {
                //配置
                case "config":
                    foreach($val as $v)
                        $this->config->load($v);
                    break;
                //函数
                case "func":
                    $sysFunc = strToArray(",", $val["sys"]);
                    $appFunc = strToArray(",", $val["app"]);
                    foreach($sysFunc as $v)
                        $this->load->func($v);
                    foreach($appFunc as $v)
                        $this->load->funcApp($v);
                    break;
                //类
                case "class":
                    $sysClass = strToArray(",", $val["sys"]);
                    $appClass = strToArray(",", $val["app"]);
                    $newsysClass = strToArray(",", $val["newsys"]);
                    $newappClass = strToArray(",", $val["newapp"]);
                    foreach($sysClass as $v)
                        $this->load->lib($v, '', '', 0);
                    foreach($appClass as $v)
                        $this->load->libApp($v, '', '', 0);
                    foreach($newsysClass as $v)
                        $this->load->lib($v);
                    foreach($newappClass as $v)
                        $this->load->libApp($v);
                    break;
                //插件
                case "plugin":
                    foreach($val as $v)
                        $this->load->plugin($v);
                    break;
                //语言包
                case "lang":
                    foreach($val as $v)
                        $this->lang->load($v);
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * 检查应用
     */
    private function check(){
        //检查缓存目录
        foreach($this->_var["directory"]["cache"] as $k => $v)
        {
            $this->file->mkdir($v);
        }
        //todo 其它检查
    }

    /**
     * 控制台
     */
    private function control(){
        if($this->check_controller()){
            $this->load->execute(array($this->load->hook("initialize"), $this->route->m()));
            $controller = $this->load->control($this->route->m(), $this->route->c());
            if(!is_object($controller)){
                exit("Controller is empty");
            }
            $this->load->execute(array($controller, $this->route->a())) or exit("Action is empty");
            $this->load->execute(array($this->load->hook("shutdown"), $this->route->m()));
        }else{
            exit("Controller is Disabled");
        }
    }

    /**
     * 验证模块黑白名单
     * @return  bool
     */
    private function check_controller(){
        $acl = $this->config->load("acl", array($this->_var["setting"]["acl"]));
        $enable = false;
        switch($acl["use"])
        {
            case "all":
            case "black":
                $black = strToArray(",", $acl["black"]["control"]);
                $enable = !in_array($this->route->m(), $black);
                if($acl["use"] == "black")
                    break;
            case "white":
                $white = strToArray(",", $acl["white"]["control"]);
                $enable = in_array($this->route->m(), $white);
                break;
            default:
                $enable = true;
        }
        return $enable;
    }
}
