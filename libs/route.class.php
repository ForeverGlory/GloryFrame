<?php
/**
 * route.class.php  路由类
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/libs/route.class.php
 * @version         $
 */
class route{

    /**
     * URL或者CLI传入的参数
     * @param   string  afferent        传入的值
     * @param   string  PATH_INFO       $_SERVER[PATH_INFO]
     * @param   string  route           $_GET/$_POST
     * @param   string  argv            $_SERVER[argv]
     *          array(
     *                  'afferent'      =>
     *                  'path_info'     =>  $_SERVER[PATH_INFO]
     *                  'route'         =>  $_GET/$_POST
     *                  'argv'          =>  $_SERVER[argv]
     *          )
     * 权重
     * $afferent > { [PATH_INFO > QUERY_STRING] == argv }
     */
    private $args = array();
    /**
     * 路由配置 m c a
     * @var type
     */
    private $routes = array();
    /*
     * 配置信息
     */
    private $setting = array();

    public function __construct(){
        static $isFirst = true;
        if($isFirst){
            GloryFrame::Auto($this);
            $isFirst = false;
        }
    }

    public function setRoutes(){
        list($key, $val) = func_get_args();
        $this->routes[$key] = $val;
    }

    /**
     * 初始化路由
     * @param   array   $setting    通过文件传递
     */
    public function initialize($setting = array()){
        static $isFirst = true;
        if($isFirst){
            /**
             * 处理传入值
             * 传入固定值时，URL重写将去掉
             */
            if($setting && is_array($setting)){
                $this->args["afferent"] = $setting;
            }
            $this->afferentArgs();
            if($this->input->isCli()){
                $key = array($this->_var["setting"]["route"], "cli");
            }else{
                $key = array($this->_var["setting"]["route"], "cgi");
            }
            $this->setting = $this->config->load("route", $key);
            $this->setRoute();
            $isFirst = false;
        }
    }

    private function setRoute(){
        if(!$this->input->isCli()){
            if($this->input->server("path_info")){
                $path_info = explode("/", $this->input->server("path_info"));
                $count = count($path_info);
                $count = $count > 4 ? 4 : $count;
                switch($count)
                {
                    case 4:
                        $this->args["path_info"]["a"] = $path_info[3];
                    case 3:
                        $this->args["path_info"]["c"] = $path_info[2];
                    case 2:
                        $this->args["path_info"]["m"] = $path_info[1];
                }
            }else{
                $this->args["route"]["m"] = compareArgs($this->input->post("m"), $this->input->get("m"));
                $this->args["route"]["c"] = compareArgs($this->input->post("c"), $this->input->get("c"));
                $this->args["route"]["a"] = compareArgs($this->input->post("a"), $this->input->get("a"));
            }
        }else{
            $argv = $this->input->server("argv");
            $argc = $this->input->server("argc");
            $argc = $argc > 4 ? 4 : $argc;
            switch($argc)
            {
                case 4:
                    $this->args["argv"]["a"] = $argv[3];
                case 3:
                    $this->args["argv"]["c"] = $argv[2];
                case 2:
                    $this->args["argv"]["m"] = $argv[1];
            }
        }
    }

    /**
     * 获取各各参数
     */
    private function afferentArgs(){
        if(is_array($this->args["afferent"])){
            foreach($this->args["afferent"] as $key => $val)
            {
                switch($key)
                {
                    case "get":
                        if(is_array($val)){
                            $this->input->setGet($val);
                        }
                        break;
                    case "post":
                        if(is_array($val)){
                            $this->input->setPost($val);
                        }
                        break;
                    case "args":
                        if(is_array($val)){
                            $this->input->setRequest($val);
                        }
                        break;
                    case "cookie":
                        if(is_array($val)){
                            foreach($val as $k => $v)
                            {
                                $this->cookie->set($k, $v);
                            }
                        }
                        break;
                    case "session":
                        if(is_array($val)){
                            foreach($val as $k => $v)
                            {
                                $this->session->set($k, $v);
                            }
                        }
                        break;
                    default:
                }
            }
        }
    }

    /**
     * 模型/插件
     */
    public function m(){
        if(empty($this->routes['m'])){
            $this->routes["m"] = compareArgs($this->args["afferent"]["m"], $this->args["path_info"]["m"], $this->args["route"]["m"], $this->args["argv"]["m"], $this->setting["m"]);
        }
        return $this->routes['m'];
    }

    /**
     * 控制器
     */
    public function c(){
        if(empty($this->routes['c'])){
            $this->routes["c"] = compareArgs($this->args["afferent"]["c"], $this->args["path_info"]["c"], $this->args["route"]["c"], $this->args["argv"]["c"], $this->setting["c"]);
        }
        return $this->routes['c'];
    }

    /**
     * 事件
     */
    public function a(){
        if(empty($this->routes['a'])){
            $this->routes["a"] = compareArgs($this->args["afferent"]["a"], $this->args["path_info"]["a"], $this->args["route"]["a"], $this->args["argv"]["a"], $this->setting["a"]);
        }
        return $this->routes['a'];
    }

    /**
     * 输出路由前的url
     * @param   string/array    $controller     控制台参数
     *              string      m,c,a / c,a / a
     *              array       [m] => m
     *                          [c] => c
     *                          [a] => a
     *              m           c=setting[c]
     *              empty(m)    c=route[c]
     *              m > c > a   当前值为空，根据前面判断，前面有值，当前值为默认setting 否则为当前值
     * @param   array           $data           其它参数
     *              array       [p] => page     固定值
     * @return  string          返回URL     通过 M 定义的规则，以模块为主
     */
    public function url($controller = '', $data = array()){
        //模块配置 读取于 configs/controllers/{m}.config.php
        static $setting = array();
        $url = $this->formatURL();
        if($controller){
            if(!is_array($controller)){
                $controller = explode(",", $controller);
                $controller = array_pad($controller, -3, "");
                list($m, $c, $a) = $controller;
            }else{
                $checkController = array("m", "c", "a");
                foreach($controller as $k => $v)
                {
                    if(in_array($k, $checkController)){
                        ${$k} = $v;
                    }
                }
            }
        }
        if(empty($m)){
            $m = $this->routes["m"];
            if(empty($c)){
                $c = $this->routes["c"];
                if(empty($a)){
                    $a = $this->routes["a"];
                }
            }else{
                if(empty($a)){
                    $a = $this->routes["a"];
                }
            }
        }else{
            if(empty($c)){
                $c = $this->setting["c"];
            }else{
                if(empty($a)){
                    $a = $this->setting["a"];
                }
            }
        }
        if(!isset($setting[$m])){
            $setting[$m] = $this->config->load($m, "route", false, "controllers");
        }
        if(empty($setting[$m])){
            $urlmodel = $setting[$m]["urlmodel"];
            $urlrewrite = $setting[$m]["urlrewrite"];
        }else{
            $urlmodel = $this->setting[$m]["urlmodel"];
            $urlrewrite = $this->setting[$m]["urlrewrite"];
        }
        switch($urlmodel)
        {
            case 1:
                //path_info
                //$url=
                break;
            case 2:
                //默认重写 {m}/{c}/{a}/
                break;
            case 3:
                //自定重写
                break;
            default:
            //不重写
        }
        if($m != $this->setting['m']){
            $url = $this->formatURL("m=$m", $url);
        }
        if($c != $this->setting['c']){
            $url = $this->formatURL("c=$c", $url);
        }
        if($a != $this->setting['a']){
            $url = $this->formatURL("a=$a", $url);
        }
        if($data){
            $url = $this->formatURL($data, $url);
        }
        //todo 判断当前路由是什么格式，输出格式
        return $url;
    }

    /**
     * 格式化URL
     * @param   type    $args   参数
     * @param   type    $url    URL 默认当前URL 可带目录
     * @return  type
     */
    public function formatURL($args = '', $url = ''){
        if(empty($url)){
            $url = $this->input->host_full() . $this->input->url_path() . $this->input->url_file();
        }
        if(empty($args)){
            return $url;
        }
        $mark = strpos($url, "?");
        if(is_numeric($mark)){
            if(strrchr($mark, "&") != "&"){
                $url .= "&";
            }
        }else{
            $url .= "?";
        }
        if(is_array($args)){
            $url .= http_build_query($args);
        }else{
            $url .= $args;
        }
        return $url;
    }
}
?>