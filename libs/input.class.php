<?php
/**
 * input.class.php   来访数据处理
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/libs/input.class.php
 * @version         $
 */
class input
{
    /**
     * 全局变量
     * @var     array()
     *          [HEADER]    _HEADER     //请求头信息
     *          [GET]       _GET        //Get请求
     *          [POST]      _POST       //Post请求
     *          [REQUEST]   _REQUEST    //Get+Post请求
     *          [SERVER]    _SERVER     //Server
     *          [FILES]     _FILES      //文件上传
     *          ...         ...
     */
    public $global = array();
    /**
     * 配置信息
     */
    private $setting = array(
        "globalname" => array(
            "HEADER", "GET", "POST", "REQUEST", "SERVER", "FILES"
        ),
        "os" => array(
            "Windows NT 6.1" => "Windows 7",
            "Windows NT 6.0" => "Windows Vista",
            "Windows NT 5.3" => "Windows 2008",
            "Windows NT 5.2" => "Windows 2003",
            "Windows NT 5.1" => "Windows XP",
            "Windows NT 5"   => "Windows 2000",
            "Windows NT 4.9" => "Windows ME",
            "Windows NT 4.0" => "Windows NT 4.0",
            "Windows 98"     => "Windows 98",
            "Windows 95"     => "Windows 95",
            "Windows"        => "Windows",
            "Mac"            => "Mac",
            "Linux"          => "Linux",
            "Unix"           => "Unix",
            "FreeBSD"        => "FreeBSD",
            "SunOS"          => "SunOS",
            "BeOS"           => "BeOS",
            "OS/2"           => "OS/2",
            "PC"             => "Macintosh",
            "AIX"            => "AIX",
        ),
        "browser"        => array(
            "MSIE 10"   => "IE10",
            "MSIE 9"    => "IE9",
            "MSIE 8"    => "IE8",
            "MSIE 7"    => "IE7",
            "MSIE 6"    => "IE6",
            "MSIE 5.5"  => "IE5.5",
            "MSIE 5"    => "IE5",
            "MSIE 4"    => "IE4",
            "MSIE"      => "IE",
            "Opera"     => "Opera",
            "Firefox"   => "Firefox",
            "Chrome"    => "Chrome",
            "Safari"    => "Safari",
            "Navigator" => "Navigator",
        )
    );

    public function __construct()
    {
        static $isFirst = true;
        if($isFirst)
        {
            GloryFrame::Auto($this);
            $this->global["SERVER"] = &$_SERVER;
            $this->global["GET"] = &$_GET;
            $this->global["POST"] = &$_POST;
            $this->global["REQUEST"] = &$_REQUEST;
//            $this->setServer($_SERVER);
//            $this->setGet($_GET);
//            $this->setPost($_POST);
            $this->setRequest($_REQUEST);
            $this->_formatHeader();
            $this->_formatUrl();
            $isFirst = false;
        }
    }

    /**
     * 调用不存在的函数    $this->setting->global 里面的
     * @param   string  $func       函数名称
     *                              调用 $this->global[$name] 值 post   /   get     /   cookie      /   files   /   ...
     *                              设置 $this->global[$name] 值 setpost/   setget  /   setcookie   /   setfiles/   ...
     * @param   array   $arguments  参数
     * @example         $this->post($k1,$k2,$k3,...) ==  $this->global[POST][$k1][$k2][$k3]...          $k 可为空
     *                  $this->setPost()             ==  unset($this->global[POST])                     清空
     *                  $this->setPost($k)           ==  unset($this->global[POST][$k])                 删除某个
     *                  $this->setPost(array("k1"=>$k1,"k2"=>$2,...))                                   1个参数，必须数组
     *                                               ==  $this->global[POST]["k1"]=$k1,$this->global[POST]["k2"]=$k2...
     *                  $this->setPost($k1,$k2)      ==  $this->global[POST][$k1]=$k2                   2个参数
     * @return
     */
    public function __call($func, $arguments)
    {
        $name = str_ireplace("set", "", $func, $count);
        $name = strtoupper($name);
        if(in_array($name, $this->setting["globalname"]))
        {
            if($count == 0)
            {
                $return = $this->global[$name];
                foreach($arguments as $val)
                {
                    if(empty($val))
                    {
                        $val = 0;
                    }
                    $return = $return[$val];
                }
                return $return;
            }
            else
            {
                $argc = count($arguments);
                switch($argc)
                {
                    case 0:
                        $this->global[$name] = array();
                        break;
                    case 1:
                        if(is_array($arguments[0]))
                        {
                            foreach($arguments[0] as $key => $val)
                            {
                                $this->$func($key, $val);
                            }
                        }
                        else
                        {
                            unset($this->global[$name][$arguments[0]]);
                        }
                        break;
                    case 2:
                        if($this->_checkArg($arguments, $name != "SERVER"))
                        {
                            $this->global[$name][$arguments[0]] = $arguments[1];
                        }
                        break;
                    default :
                }

            }
        }
        else
        {
            trigger_error("function \$this->input->$func() empty",E_USER_ERROR);
        }
    }

    /**
     * 是否是Post提交
     * @return  true/false
     */
    public function isPost()
    {
        return $this->global["SERVER"]["REQUEST_METHOD"] == "POST" || !empty($this->global["POST"]);
    }

    /**
     * 是否是上传文件
     * @return  true/false
     */
    public function isFile()
    {
        return array_key_exists("FILES", $this->global);
    }

    /**
     * 是否Ajax提交
     * @return  true/false
     */
    public function isAjax()
    {
        return ($this->global["SERVER"]["HTTP_X_REQUESTED_WITH"] === "XMLHttpRequest");
    }

    /**
     * 是否伪静态
     * @return  true/false
     */
    public function isRewrite()
    {
        if(!array_key_exists("URL_REWRITE", $this->global["SERVER"]))
        {
            if(empty($this->global["SERVER"]["REDIRECT_URL"]) && empty($this->global["SERVER"]["REDIRECT_QUERY_STRING"]))
            {
                $this->global["SERVER"]["URL_REWRITE"] = false;
            }
            else
            {
                $this->global["SERVER"]["URL_REWRITE"] = true;
            }
        }
        return $this->global["SERVER"]["URL_REWRITE"];
    }

    /**
     * 是否HTTPS请求
     * @return  true/false
     */
    public function isHttps()
    {
        return $this->global["SERVER"]["HTTPS"];
    }

    /**
     * 是否命令模式
     * @return  true/false
     */
    public function isCli()
    {
        return substr($this->sapi(), 0, 3) == "cli";
    }

    /**
     * 浏览模式   cli/cgi...
     * @return  cli 命令模式 cgi 浏览器模式
     */
    public function sapi()
    {
        if(!array_key_exists("SAPI", $this->global["SERVER"]))
        {
            $this->global["SERVER"]["SAPI"] = php_sapi_name();
        }
        return $this->global["SERVER"]["SAPI"];
    }

    /**
     * 操作系统
     * @return  string  操作系统
     */
    public function os()
    {
        if(!array_key_exists("OS", $this->global["SERVER"]))
        {
            foreach($this->setting["os"] as $k => $v)
            {
                if(stripos($this->global["SERVER"]["HTTP_USER_AGENT"], $k) !== false)
                {
                    $this->global["SERVER"]["OS"] = $v;
                    break;
                }
            }
            if(empty($this->global["SERVER"]["OS"]))
            {
                $this->global["SERVER"]["OS"] = "Other OS";
                $this->debug->msg("no OS:" . $this->global["SERVER"]["HTTP_USER_AGENT"], 1);
            }
        }
        return $this->global["SERVER"]["OS"];
    }

    /**
     * 浏览器
     * @return  string  浏览器
     */
    public function browser()
    {
        if(!array_key_exists("BROWSER", $this->global["SERVER"]))
        {
            foreach($this->setting["browser"] as $k => $v)
            {
                if(stripos($this->global["SERVER"]["HTTP_USER_AGENT"], $k) !== false)
                {
                    $this->global["SERVER"]["BROWSER"] = $v;
                    break;
                }
            }
            if(empty($this->global["SERVER"]["BROWSER"]))
            {
                $this->global["SERVER"]["BROWSER"] = "Other Browser";
                $this->debug->msg("no Browser:" . $this->global["SERVER"]["HTTP_USER_AGENT"], 1);
            }
        }
        return $this->global["SERVER"]["BROWSER"];
    }

    /**
     * 获取IP值
     * @param   int     $type
     *                  0           自动获取 真实IP地址优先 获取不到真实IP时，取第一个代理IP
     *                  1           真实IP地址
     *                  2           代理IP地址
     * @return  string  127.0.0.1 / 127.0.0.1,127.0.0.2 / null
     */
    public function ip($type = 0)
    {
        if(!array_key_exists("IP_TYPE", $this->global["SERVER"]))
        {
            $ip_remote = $this->global["SERVER"]["REMOTE_ADDR"];
            $ip_via = $this->global["SERVER"]["HTTP_VIA"];
            $ip_x = $this->global["SERVER"]["HTTP_X_FORWARDED_FOR"];
            if(empty($ip_via))
            {
                if(empty($ip_x))
                {
                    //真实IP  高匿名代理也有可能
                    $this->global["SERVER"]["IP_TYPE"] = 1;
                    $this->global["SERVER"]["IP"] = $ip_remote;
                }
                else
                {
                    //高匿名代理
                    $this->global["SERVER"]["IP_TYPE"] = 5;
                    $this->global["SERVER"]["IP"] = $ip_x;
                }
            }
            else
            {
                $ip_x_array = preg_match_all("/(\d{1,3}\.){3}[\d]{1,3}/", $ip_x, $matches) ? $matches[0] : array();
                //只能判断一级代理，如果多级代理，都将算成透明代理
                if(!empty($ip_x_array))
                {
                    $this->global["SERVER"]["IP_TYPE"] = 2;
                    $this->global["SERVER"]["IP"] = array_shift($ip_x_array);
                }
                else
                {
                    $this->global["SERVER"]["IP_TYPE"] = 3;
                }
                //todo 不知道这个值应该放前面，还是放后面
                array_unshift($ip_x_array, $ip_remote);
                $this->global["SERVER"]["IP_PROXY"] = implode(",", $ip_x_array);
            }
        }
        $type = intval($type);
        $ip = "";
        switch($type)
        {
            case 0:
                $ip = $this->ip(1);
                if(empty($ip))
                {
                    //没有真实IP地址，取第一个代理IP地址
                    $ip = preg_match("/(\d{1,3}\.){3}[\d]{1,3}/", $this->ip(2), $matches) ? $matches[0] : '';
                }
                break;
            case 1:
                $ip = $this->global["SERVER"]["IP"];
                break;
            case 2:
                $ip = $this->global["SERVER"]["IP_PROXY"];
                break;
            default :
        }
        return $ip;
    }

    /**
     * 获取请求方式 "http"/"https"
     * @return  string  "http"/"https"
     */
    public function url_scheme()
    {
        if(!array_key_exists("URL_SCHEME", $this->global["SERVER"]))
        {
            $this->global["SERVER"]["URL_SCHEME"] = $this->isHttps() ? "https" : "http";
        }
        return $this->global["SERVER"]["URL_SCHEME"];
    }

    /**
     * 完整的HOST
     * @return  http://user:pwd@localhost:88
     */
    public function host_full()
    {
        if(!array_key_exists("URL_HOST", $this->global["SERVER"]))
        {
            $host_url = $this->url_scheme() . "://" . $this->http_auth() . $this->global["SERVER"]["HTTP_HOST"];
            switch($this->url_scheme())
            {
                case "http":
                    if($this->global["SERVER"]["SERVER_PORT"] == 80)
                        break;
                case "https":
                    if($this->global["SERVER"]["SERVER_PORT"] == 443)
                        break;
                default :
                    $host_url.=":" . $this->global["SERVER"]["SERVER_PORT"];
            }
            $this->global["SERVER"]["URL_HOST"] = $host_url;
        }
        return $this->global["SERVER"]["URL_HOST"];
    }

    /**
     * 返回 URL 的目录
     * @param   bool    $isCur      伪静态时，参数有效，返回当前URL目录或真实URL目录
     * @return  URL目录  如：/path/path
     */
    public function url_path($isCur = true)
    {
        if($isCur && $this->isRewrite())
        {
            return $this->global["SERVER"]["URL_PATH_REWRITE"];
        }
        else
        {
            return $this->global["SERVER"]["URL_PATH"];
        }
    }

    /**
     * 返回 URL 执行的文件名
     * @param   bool    $isCur      伪静态时，参数有效，返回当前文件或真实文件
     * @return  string  如：index.html
     */
    public function url_file($isCur = true)
    {
        if($isCur && $this->isRewrite())
        {
            return $this->global["SERVER"]["URL_FILE_REWRITE"];
        }
        else
        {
            return $this->global["SERVER"]["URL_FILE"];
        }
    }

    /**
     * 返回 get 参数
     * @param   bool    $isCur      伪静态时，参数有效，返回当前GET参数或真实GET参数
     * @return  string  如：key=val&key1=val1
     */
    public function query_string($isCur = true)
    {
        if($isCur && $this->isRewrite())
        {
            return $this->global["SERVER"]["URL_FILE_REWRITE"];
        }
        else
        {
            return $this->global["SERVER"]["URL_FILE_REWRITE"];
        }
    }

    /**
     * 访问的URL地址
     * @param   bool    $isCur      伪静态时，参数有效，返回当前URL或真实URL
     * @return  string  如：http://user:pwd@localhost/path/file.php/path_info?query
     */
    public function url_full($isCur = true)
    {
        if($isCur && $this->isRewrite())
        {
            return $this->global["SERVER"]["URL_FULL_REWRITE"];
        }
        else
        {
            return $this->global["SERVER"]["URL_FULL"];
        }
    }

    /**
     * 检查参数
     * @param type $arg
     */
    private function _checkArg(&$arg, $filtr = true)
    {
        if($filtr)
        {
            $arg[1] = new_addslashes($arg[1]);
        }
        return $arg;
    }

    /**
     * 请求头信息 格式化
     */
    private function _formatHeader()
    {
        // Look at Apache go!
        if(function_exists('apache_request_headers'))
        {
            $headers = apache_request_headers();
        }
        else
        {
            $headers['Content-Type'] = (isset($_SERVER['CONTENT_TYPE'])) ? $_SERVER['CONTENT_TYPE'] : @getenv('CONTENT_TYPE');
            foreach($this->global["SERVER"] as $key => $val)
            {
                if(strncmp($key, 'HTTP_', 5) === 0)
                {
                    $headers[substr($key, 5)] = $this->_fetch_from_array($_SERVER, $key, $xss_clean);
                }
            }
        }
        //大写名称第一个字母
        foreach($headers as $key => $val)
        {
            $key = str_replace('-', ' ', strtolower($key));
            $key = str_replace(' ', '-', ucwords($key));
            $this->global["HEADER"][$key] = $val;
        }
        return $this->global["HEADER"];
    }

    /**
     * 设置HTTP URL 相关信息
     * @var     [URL_REWRITE]       是否伪静态
     * //真实变量 目录、文件、URL
     * @var     [URL_FILE]          实际文件
     * @var     [URL_PATH]          实际目录
     * @var     [URL_FULL]          实际完整URL
     * //伪静态时需要变量  当前
     * @var     [URL_QUERY_REWRITE] 伪静态当前参数
     * @var     [URL_FILE_REWRITE]  伪静态当前文件
     * @var     [URL_PATH_REWRITE]  伪静态当前目录
     * @var     [URL_FULL_REWRITE]  伪静态当前地址
     */
    private function _formatUrl()
    {
        $script_name = $this->global["SERVER"]["SCRIPT_NAME"];
        $path = explode("/", $script_name);
        $this->global["SERVER"]["URL_FILE"] = array_pop($path);
        $this->global["SERVER"]["URL_PATH"] = implode("/", $path) . "/";
        $this->global["SERVER"]["URL_FULL"] = $this->host_full() . $this->global["SERVER"]["PHP_SELF"] . (empty($this->global["SERVER"]["QUERY_STRING"]) ? "" : "?" . $this->global["SERVER"]["QUERY_STRING"]);
        if($this->isRewrite())
        {
            $request_uri = $this->global["SERVER"]["REQUEST_URI"];
            $url_path_cur = $url_file_cur = $url_query_cur = "";
            if($request_uri)
            {
                list($url_path_cur, $url_query_cur) = explode("?", $request_uri);
                $path = explode("/", $url_path_cur);
                if($path[count($path) - 1])
                {
                    $url_file_cur = array_pop($path);
                    $url_path_cur = implode("/", $path);
                }
            }
            $this->global["SERVER"]["URL_QUERY_REWRITE"] = $url_query_cur;
            $this->global["SERVER"]["URL_FILE_REWRITE"] = $url_file_cur;
            $this->global["SERVER"]["URL_PATH_REWRITE"] = $url_path_cur . "/";
            $this->global["SERVER"]["URL_FULL_REWRITE"] = $this->host_full() . $request_uri;
        }
    }

    /**
     * 返回网站验证数据 user:pwd@
     * @return  string
     */
    private function http_auth()
    {
        if(!array_key_exists("URL_AUTH", $this->global["SERVER"]))
        {
            $url_auth = "";
            if($this->global["SERVER"]["PHP_AUTH_USER"])
            {
                $url_auth = $this->global["SERVER"]["PHP_AUTH_USER"] . ":" . $this->global["SERVER"]["PHP_AUTH_PW"] . "@";
            }
            $this->global["SERVER"]["URL_AUTH"] = $url_auth;
        }
        return $this->global["SERVER"]["URL_AUTH"];
    }

}