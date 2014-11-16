<?php

/**
 * http.class.php   浏览器模拟类
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/libs/http.class.php
 * @version         $
 */
class http {

    /**
     * Header请求
     */
    protected $RequestHeader = array();
    protected $HeaderName = array("Method", "Accept", "Referer", "Accept-Language", "User-Agent", "Host", "Content-Type", "Content-Length", "Connection", "Cache-Control", "Cookie", "Content");
    protected $mark = "\r\n";

    /**
     * 请求返回的数据
     * header   响应头信息
     * status   返回状态
     * cookie   返回Cookie信息
     * data     返回的内容
     */
    private $back = array("header" => "", "status" => "", "charset" => "", "cookie" => "", "type" => "", "data" => "");
    public $errMsg = "";
    public $errNo = 0;

    /**
     * 基本配置
     * Timeout          请求超时
     * Block            是否队列
     * Retry            重试次数
     * Accept           请求内容格式
     * Accept-Language  请求语种
     * User-Agent       浏览器、操作系统
     * Referer          来路
     * Connection       连接方式
     * Cache-Control    缓存
     * Request_Ip       伪IP请求
     */
    protected $setting = array();

    /**
     * 构造函数
     * @param	string/array	        $setting	浏览器模拟配置，或配置名
     *          string                  读取 http.config.php 里名称的配置
     *          array(
     *                  Timeout         请求超时
     *                  Block           是否队列
     *                  Retry           重试次数
     *                  Accept          请求内容格式
     *                  Accept-Language 请求语种
     *                  User-Agent      浏览器、操作系统
     *                  Referer         来路
     *                  Connection      连接方式
     *                  Cache-Control   缓存
     *                  Request_Ip      模拟请求伪IP
     *          )
     * @return  void
     */
    public function __construct($setting = '') {
        static $isFirst = true;
        if ($isFirst) {
            GloryFrame::Auto($this, 1);
            if (is_string($setting) || empty($setting)) {
                if (empty($setting)) {
                    $setting = $this->_var["setting"]["http"];
                }
                $setting = $this->config->load('http', array($setting));
            }
            $this->setting = $setting;
            $isFirst = false;
        }
    }

    /**
     * 远程GET请求
     * @param   string          $url    访问URL
     * @param   string/array    $arg    参数
     *                          string  k1=v1&k2=v2&...
     *                          array   array(k1=>v1,k2=>v2,...)
     * @param   array           $option 其它参数
     * @return  text
     */
    public function get($url, $arg = null, $option = array()) {
        $option["Method"] = "GET";
        if (!empty($arg)) {
            if (strpos($url, "?") === false) {
                $url.="?";
            } elseif (strrchr($url, "&") != "&") {
                $url.="&";
            }
            $url.=is_array($arg) ? http_build_query($arg) : $arg;
        }
        return $this->request($url, $option);
    }

    /**
     * 远程POST请求
     * @param   string          $url    访问URL 如有get请求,请用?加于URL后面
     * @param   string/array    $post   POST参数
     *                          string  k1=v1&k2=v2&...
     *                          array   array(k1=>v1,k2=>v2,...)
     * @param   array           $option 其它参数
     * @return  text
     */
    public function post($url, $post = null, $option = array()) {
        $option["Method"] = "POST";
        $option["Content-Type"] = "application/x-www-form-urlencoded";
        $option["Content"] = $post;
        return $this->request($url, $option);
    }

    /**
     * 远程上传请求
     * @param   string          $url    访问URL 如有get请求,请用?加于URL后面
     * @param   string/array    $post   POST参数
     *                          string  k1=v1&k2=v2&...
     *                          array   array(k1=>v1,k2=>v2,...)
     * @param   array           $files  上传的文件.服务器本地文件
     * @param   array           $option 其它参数
     * @return  text
     */
    public function upload($url, $post = null, $file = array(), $option = array()) {
        
    }

    /**
     * 设置请求头信息
     * @param   array   $header     头信息内容/empty时默认值
     */
    public function setHeader($header = array()) {
        if (empty($this->RequestHeader) || empty($header)) {
            $this->RequestHeader = $this->setting;
        }
        if (empty($header) || !is_array($header)) {
            return;
        }
        if (is_array($header["Content"])) {
            $header["Content"] = http_build_query($header["Content"]);
        }
        if ($this->RequestHeader["Content"] && $header["Content"]) {
            $header["Content"] = $this->RequestHeader["Content"] . "&" . $header["Content"];
        }
        if ($header["Cookie"]) {
            $this->setCookie($header["Cookie"]);
            unset($header["Cookie"]);
        }
        $this->RequestHeader = arrayMerge($this->RequestHeader, $header);
    }

    /**
     * 设置Cookie
     * @param
     */
    public function setCookie() {
        if (empty($this->RequestHeader)) {
            $this->RequestHeader = $this->setting;
        }
        $argv = func_get_args();
        $argc = count($argv);
        if ($argc == 1) {
            if (is_array($argv[0])) {
                foreach ($argv[0] as $key => $val) {
                    $this->setCookie($key, $val);
                }
            } else {
                $this->RequestHeader["Cookie"].=$argv[0] . ";";
            }
        } elseif ($argc > 1) {

            if (is_array($argv[1])) {
                foreach ($argv[1] as $key => $value) {
                    $this->setCookie($key, $value, $argv[0]);
                }
            } else {
                $name = $argv[2] ? $argv[2] . "[" . $argv[0] . "]" : $argv[0];
                $this->RequestHeader["Cookie"].=$name . "=" . $argv[1] . ";";
            }
        }
    }

    public function request($url, $option = array()) {
        $this->back = array();
        $urls = parse_url($url);
        $option["Host"] = $urls["host"];
        $path = ($urls['path'] ? $urls['path'] : "/") . ($urls['query'] ? '?' . $urls['query'] : '');
        $port = empty($urls["port"]) ? ($urls["scheme"] == "http" ? "80" : "443") : $urls["port"];
        $this->setHeader($option);
        $timeout = $this->RequestHeader["Timeout"];
        $retry = $this->RequestHeader["Retry"];
        $block = $this->RequestHeader["Block"];
        $this->RequestHeader = arrayCheckKey($this->RequestHeader, $this->HeaderName);
        $header = "";
        $content = null;
        foreach ($this->HeaderName as $val) {
            switch ($val) {
                case "Method":
                    $header.= $this->RequestHeader[$val] . " " . $path . " HTTP/1.1" . $this->mark;
                    break;
                case "Referer":
                    if ($this->RequestHeader[$val]) {
                        $header.=$val . ": " . $this->RequestHeader[$val] . $this->mark;
                    }
                    break;
                case "Content-Type":
                    if ($this->RequestHeader[$val]) {
                        $header.=$val . ": " . $this->RequestHeader[$val] . $this->mark;
                    }
                    break;
                case "Content-Length":
                    if ($this->RequestHeader["Content"]) {
                        $header.=$val . ": " . strlen($this->RequestHeader["Content"]) . $this->mark;
                    }
                    break;
                case "Content":
                    if ($this->RequestHeader["Content"]) {
                        $header.=$this->RequestHeader["Content"];
                    }
                    break;
                case "Cookie":
                    $header.=$val . ": " . $this->RequestHeader[$val] . $this->mark . $this->mark;
                    break;

                default:
                    $header.=$val . ": " . $this->RequestHeader[$val] . $this->mark;
            }
        }
        do {
            if (ini_get('max_execution_time')) {
                set_time_limit($timeout + 10);
            }
            if (function_exists('fsockopen')) {
                $fp = @fsockopen($option["Host"], $port, $this->errNo, $this->errMsg, $timeout);
            } elseif (function_exists('pfsockopen')) {
                $fp = @pfsockopen($option["Host"], $port, $this->errNo, $this->errMsg, $timeout);
            }
            if (!empty($fp)) {
                //设置程序是否队列
                stream_set_blocking($fp, $block);
                //设置超时
                stream_set_timeout($fp, $timeout);
                @fwrite($fp, $header);
                $status = stream_get_meta_data($fp);
                if (!$status['timed_out']) {
                    $this->back = array();
                    while (!feof($fp)) {
                        $line = @GloryFrameets($fp);
                        if ($line != "\r\n" && $line != "\n") {
                            if ($line !== FALSE) {
                                //取头文件，并存数组
                                if (preg_match("/^(.*?)\s*:\s*(.*?)$/", $line, $matches)) {
                                    //通过头信息 处理内容类型、编码
                                    if ($matches[1] == "Content-Type") {
                                        if (preg_match("/^(.*?)\s*;\s*charset=(.*)|(.*?);*$/", $matches[2], $Content_Type)) {
                                            if (array_key_exists("3", $Content_Type)) {
                                                $this->back["type"] = $Content_Type["3"];
                                            } else {
                                                $this->back["type"] = $Content_Type["1"];
                                                $this->back["charset"] = $Content_Type["2"];
                                            }
                                        }
                                    } elseif ($matches[1] == "Cookie") {
                                        
                                    }
                                    $this->back["header"][$matches[1]] = $matches[2];
                                } else {
                                    //获取请求状态
                                    if (preg_match("/^HTTP\/1.1\s*([0-9]{3})\s*(.*)/", $line, $m)) {
                                        $this->errNo = $m[1];
                                        $this->errMsg = $m[2];
                                        $this->back["status"] = $m[1];
                                    }
                                }
                            }
                        } else {
                            break;
                        }
                    }
                    $limit = intval($this->getHeader("Content-Length"));
                    $stop = false;
                    while (!feof($fp) && !$stop) {
//                        $data = stream_get_contents($fp);
//                        $this->back["data"] .= $data;

                        $data = fread($fp, ($limit == 0 || $limit > 8192 ? 8192 : $limit));
                        if ($limit) {
                            $limit -= strlen($data);
                            $stop = $limit <= 0;
                        }
                        $this->back["data"] .= $data;
                    }
                    //echo $this->back["data"];
//                    while(!feof($fp) && !array_key_exists("data", $this->back))
//                    {
//                        if($start){
//                            $Content_Length = $this->getHeader("Content-Length");
//                            $this->back["data"] = @fread($fp, $Content_Length ? $Content_Length : 18432);
//                        }else{
//                            $line = @GloryFrameets($fp);
//                            if($line == "\r\n" || $line == "\n"){
//                                $start = true;
//                            }elseif($line !== FALSE){
//                                //取头文件，并存数组
//                                if(preg_match("/^(.*?)\s*:\s*(.*?)$/", $line, $matches)){
//                                    //通过头信息 处理内容类型、编码
//                                    if($matches[1] == "Content-Type"){
//                                        if(preg_match("/^(.*?)\s*;\s*charset=(.*)|(.*?);*$/", $matches[2], $Content_Type)){
//                                            if(array_key_exists("3", $Content_Type)){
//                                                $this->back["type"] = $Content_Type["3"];
//                                            }else{
//                                                $this->back["type"] = $Content_Type["1"];
//                                                $this->back["charset"] = $Content_Type["2"];
//                                            }
//                                        }
//                                    }
//                                    $this->back["header"][$matches[1]] = $matches[2];
//                                }else{
//                                    //获取请求状态
//                                    if(preg_match("/^HTTP\/1.1\s*([0-9]{3})\s*(.*)/", $line, $m)){
//                                        $this->errNo = $m[1];
//                                        $this->errMsg = $m[2];
//                                        $this->back["status"] = $m[1];
//                                    }
//                                }
//                            }
//                        }
//                    }
                }
            }
            @fclose($fp);
            if ($this->errNo == 200) {
                $content = $this->getData();
                break;
            }
        } while ($retry--);
        return $content;
    }

    public function getBack($key = null) {
        $return = $this->back;
        if ($key != null) {
            $return = $return[$key];
        }
        return $return;
    }

    /**
     * 返回状态
     */
    public function getStatus() {
        return $this->getBack("status");
    }

    /**
     * 返回头信息
     */
    public function getHeader($key = null) {
        $return = $this->getBack("header");
        if ($key != null) {
            $return = $return[$key];
        }
        return $return;
    }

    /**
     * 获取数据
     * @param  boolean  $isFormat   是否格式化数据 根据头信息，判断数据格式、及编码转换
     * @return string/array
     */
    public function getData($isFormat = true) {
        $return = $this->getBack("data");
        if ($isFormat) {
            if ($this->getStatus() == 200) {
                //Todo: Content-Type 数据格式、编码格式
            } else {
                $return = FALSE;
            }
        }
        return $return;
    }

    /**
     * 获取COOKIE
     */
    public function getCookie($key = null) {
        if (!$this->back["cookie"]) {
            if (preg_match_all("|Set-Cookie: ([^;]*);|", $this->back['header'], $m)) {
                foreach ($m[1] as $c) {
                    list($k, $v) = explode('=', $c);
                    $this->back["cookie"][$k] = $v;
                }
            }
        }
        $return = $this->back["cookie"];
        if ($key != null) {
            $return = $return[$key];
        }
        return $return;
    }

}
