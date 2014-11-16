<?php
/**
 * output.class.php 输出类
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/libs/output.class.php
 * @version         $
 */
class output
{
    /**
     * 配置信息
     * @var type
     */
    private $setting = array(
        "status" => array(
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        ),
    );

    /**
     * 构造函数
     */
    public function __construct()
    {
        static $isFirst = true;
        if($isFirst)
        {
           GloryFrame::Auto($this);
            if($this->_var['gzip'] && function_exists('ob_gzhandler'))
            {
                ob_start('ob_gzhandler');
            }
            else
            {
                ob_start();
            }
            $this->setContentType();
            $isFirst = false;
        }
    }

    /**
     * header("$type: $str");
     * @param   string  $type   header类型
     * @param   string  $str    header值
     */
    public function header($type, $str)
    {
        $type = ucwords($type);
        header("$type: $str");
        if($type == "Location")
        {
            exit;
        }
    }

    /**
     * 设置响应状态 同时设置 请求状态 及 响应头状态
     * @param   int     $code
     * @param   string  $text
     */
    public function setStatus($code = 200, $text = '')
    {
        $this->setRequestStatus($code);
        $this->setHeaderStatus($code, $text);
    }

    /**
     * 设置请求状态
     * @param   int     $code   返回代码
     */
    public function setRequestStatus($code = 200)
    {
        if(!array_key_exists($code, $this->setting["status"]))
        {
            $code = 500;
        }
        header($this->input->server("SERVER_PROTOCOL") . " {$code} {$text}", true, $code);
    }

    /**
     * 设置响应头状态
     * @param   int     $code   响应代码
     * @param   string  $text   响应文字
     */
    public function setHeaderStatus($code = 200, $text = '')
    {
        if(!array_key_exists($code, $this->setting["status"]))
        {
            $code = 500;
        }
        if(empty($text))
        {
            $text = $this->setting["status"][$code];
        }
        header("Status: {$code} {$text}", true);
    }

    /**
     * 设置响应页面格式、编码
     * @param   string  $type       页面格式
     * @param   string  $charset    页面编码
     */
    public function setContentType($type = "", $charset = null)
    {
        if(empty($type))
        {
            $type = "text/html";
        }
        if(is_null($charset))
        {
            $charset     = $this->_var["charset"];
        }
        $contentType = $type;
        if(!empty($charset))
        {
            $contentType.="; charset=" . $charset;
        }
        $this->header("Content-type", $contentType);
    }

    /**
     * 输出js Alert对话框
     * @param   string  $str    对话框内容
     * @param   string  $url    跳转URL
     */
    public function jsAlert($str, $url = null)
    {
        $this->setStatus();
        $html = "<script type=\"text/javascript\" charset=\"".$this->_var["charset"]."\">\n";
        $html.="alert(\"$str\");\n";
        if(!is_null($url))
        {
            if(empty($url))
            {
                $html.="history.back();";
            }
            else
            {
                $html.="window.location.href=\"$url\";";
            }
        }
        else
        {
            $html.="location.replace(location.href);";
        }
        $html.="\n</script>";
        exit($html);
    }

    /**
     * 输出JSON
     * @param   string/array    $json   JSON字符串/JSON数组
     */
    public function json($json)
    {
        $this->setStatus();
        $this->setContentType("application/json");
        if(is_array($json))
        {
            $json = json_encode($json);
        }
        exit($json);
    }

    /**
     * 输出XML
     * @param   string      $xml    XML字符串
     */
    public function xml($xml)
    {
        $this->setStatus();
        $this->setContentType("application/xml");
        exit($xml);
    }

}