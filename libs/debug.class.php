<?php
/**
 * 错误处理类
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/libs/debug.class.php
 * @version         $
 */
class debug
{
    /**
     * 所有错误、异常及消息信息存储数组
     * @var     array
     */
    private $allError = array();
    /**
     * 错误代号映射的错误文本
     * @link    http://docs.php.net/manual/zh/errorfunc.constants.php
     * @var     array
     */
    private $errorText = array(
        '0'      => 'USER-DEFINED',
        '1'      => 'E_ERROR',
        '2'      => 'E_WARNING',
        '4'      => 'E_PARSE',
        '8'      => 'E_NOTICE',
        '16'     => 'E_CORE_ERROR',
        '32'     => 'E_CORE_WARNING',
        '64'     => 'E_COMPILE_ERROR',
        '128'    => 'E_COMPILE_WARNING',
        '256'    => 'E_USER_ERROR',
        '512'    => 'E_USER_WARNING',
        '1024'   => 'E_USER_NOTICE',
        '2048'   => 'E_STRICT',
        '4096'   => 'E_RECOVERABLE_ERROR',
        '8192'   => 'E_DEPRECATED',
        '16384'  => 'E_USER_DEPRECATED'
    );
    /**
     * 配置信息
     */
    private $setting = array(
        //调试模式 0 关闭调试模式 1 出错调试 2 全调试
        'debug'       => 2,
        //关闭调试模式生效 0 没有任何记录 1 简单日志记录 2 详细日志记录
        'error'       => 1,
        //不显示的错误
        'ignoreERROR' => array(E_NOTICE),
        /**
         * 替换输出字符 (防止爆服务器地址)
         */
        //替换目标值 string/array
        'search' => array(),
        //替换值 string/array
        'replace' => array(),
        /**
         * 日志保存方式
         * 目录+自定义目录+文件名+后缀
         */
        //日志目录
        'path'    => '',
        //错误日志
        'logFile' => 'Ymd',
        //日志后缀
        'logsuf'  => '.log.php',
    );

    /**
     * 初始化配置
     * @param   array   $setting    配置信息，参照 $this->setting
     */
    public function __construct($setting = array())
    {
        static $isFirst = true;
        if($isFirst)
        {
            error_reporting(0);
            if(empty($setting))
            {
               GloryFrame::Auto($this);
                $setting['debug'] = $this->_var["debug"];
                $setting['error'] = $this->_var["error"];
                $setting["search"] = array(APP_PATH, FR_PATH, APP_ROOT);
                $setting["replace"] = array("{APP_PATH}", "{FR_PATH}", "{APP_ROOT}");
                $setting['path'] = $this->_var["directory"]["cache"]["log"];
            }
            $this->setting = arrayMerge($this->setting, $setting);
            set_error_handler(array($this, "error"));
            set_exception_handler(array($this, "exception"));
            register_shutdown_function(array($this, "internal_error"));
            register_shutdown_function(array($this, "shutdown"));
            ini_set("error_log", $this->setting["path"] . date($this->setting["logFile"]) . $this->setting["logsuf"]);
            $isFirst = false;
        }
    }

    /**
     * 自定义通知
     * @param   string  $message    错误消息
     * @param   int     $level      消息级别 0 调试 1 警告 2 错误
     */
    public function msg($message, $level = 2)
    {
        //消息级别
        static $levelMsg = array(0 => "DEBUG", 1 => "WARNING", 2 => "ERROR");
        //调试模式或错误记录
        if($level || ($this->setting['debug'] == 2 || (empty($this->setting['debug']) && $this->setting['error'] == 2)))
        {
            $trace = debug_backtrace();
            $msg = array();
            $msg['time'] = microtime();
            $msg['type'] = 'USER-DEFINED';
            $msg['name'] = $levelMsg[$level];
            $msg['code'] = 0;
            $msg['message'] = $message;
            $msg['file'] = $trace[1]['file'];
            $msg['line'] = $trace[1]['line'];
            unset($trace[0]);
            //调试模式或日志详情模式，记录详情
            if($level)
            {
                $msg['trace'] = $this->_format_trace($trace);
            }
            $this->allError[] = $msg;
        }
    }

    /**
     * 抛出异常
     * @param   exception   $e
     */
    public function exception(exception $e)
    {
        $errorInfo = array();
        $errorInfo['time'] = microtime();
        $errorInfo['type'] = 'EXCEPTION';
        $errorInfo['name'] = get_class($e);
        $errorInfo['code'] = $e->getCode();
        $errorInfo['message'] = $e->getMessage();
        $errorInfo['file'] = $e->getFile();
        $errorInfo['line'] = $e->getLine();
        $errorInfo['trace'] = $this->_format_trace($e->getTrace());
        $this->allError[] = $errorInfo;
    }

    /**
     * 处理错误
     * @param   type    $errno
     * @param   type    $errstr
     * @param   type    $errfile
     * @param   type    $errline
     */
    public function error($errno, $errstr, $errfile, $errline)
    {
        if(!in_array($errno, (array)$this->setting['ignoreERROR']))
        {
            $errorInfo = array();
            $errorInfo['time'] = microtime();
            $errorInfo['type'] = 'ERROR';
            $errorInfo['name'] = $this->errorText[$errno] ? $this->errorText[$errno] : "_UNKNOWN_";
            $errorInfo['code'] = $errno;
            $errorInfo['message'] = $errstr;
            $errorInfo['file'] = $errfile;
            $errorInfo['line'] = $errline;
            $trace = debug_backtrace();
            unset($trace[0]); //调用该类自身的error方法所产生的trace，故删除
            if($errno == 1024)
            {
                unset($trace[1]);
            }
            $errorInfo['trace'] = $this->_format_trace($trace);
            $this->allError[] = $errorInfo;
        }
    }

    /**
     * 检测是否存在一个严重的错误php内部错误
     * 必要条件：PHP >= 5.2
     */
    public function internal_error()
    {
        $last_error = error_get_last();
        if(empty($last_error))
        {
            return false;
        }
        //检测本类是否已经检测到最后一次错误
        if(!empty($this->allError))
        {
            $log_last_error = end($this->allError);
            if($log_last_error['code'] == $last_error['type'] && $log_last_error['file'] == $last_error['file'] && $log_last_error['line'] == $last_error['line'])
            {
                return false;
            }
        }
        if(!in_array($last_error['type'], (array)$this->setting['ignoreERROR']))
        {
            $errorInfo = array();
            $errorInfo['time'] = microtime();
            $errorInfo['type'] = 'ERROR_GET_LAST';
            if(!empty($this->errorText[$last_error['type']]))
            {
                $errorInfo['name'] = $this->errorText[$last_error['type']];
            }
            else
            {
                $errorInfo['name'] = '_UNKNOWN_';
            }
            $errorInfo['code'] = $last_error['type'];
            $errorInfo['message'] = $last_error['message'];
            $errorInfo['file'] = $last_error['file'];
            $errorInfo['line'] = $last_error['line'];
            $errorInfo['trace'] = array();
            $this->allError[] = $errorInfo;
        }
    }

    /**
     * 程序结束执行~~
     */
    public function shutdown()
    {
        $this->_var["endtime"] = microtime();
        $executetime = compareMicro($this->_var['starttime'], $this->_var["endtime"]);
        if($this->setting['debug'])
        {
            //出错调试
            if($this->setting['debug'] == 1 && empty($this->allError))
            {
                return;
            }
            $htmlText = '<table border="0" cellpadding="0" cellspacing="0" bgcolor="#999000" width="100%">';
            if($this->allError)
            {
                $htmlText.='<tr><td width=120>Code</td><td width=110>Time</td><td>Path</td><td>Msg</td></tr>';
            }
            foreach($this->allError as $key => $errorInfo)
            {
                $htmlText .= '
                    <tr>
                    <td><font color="red">' . strtoupper($errorInfo['name']) . ' ' . $errorInfo['code'] . '</font></td>
                    <td>' . formatMicro('i:s.micro[6]', $errorInfo['time']) . '</td>
                    <td>' . $errorInfo['file'] . '(' . $errorInfo['line'] . ') </td>
                    <td><font color="red">' . $errorInfo['message'] . '</font></td>
                    </tr>
                ';
                //trace显示区
                if(!empty($errorInfo['trace']))
                {
                    $htmlText .= '<tr><td colspan="4"><table width="100%"  bgcolor="#D4D0C8" border="1" cellpadding="0" cellspacing="0"><tr><th>#</th><th>File</th><th>Line</th><th>Class::Method(Args)</th></tr>';
                    foreach($errorInfo['trace'] as $stack => $trace)
                    {
                        $htmlText .= '<tr><td align="center">' . $stack . '</td><td>' . $trace['file'] . '</td><td align="center">' . $trace['line'] . '</td><td>' . $trace['class'] . $trace['type'] . htmlspecialchars($trace['function']) . '</td></tr>';
                    }
                    $htmlText .= '</table></td></tr>';
                }
                $htmlText .= '</td></tr>';
            }
            $htmlText .= '<tr bgcolor="#FFCfff"><td><font color="red">Begin:</font></td><td colspan="3"><font color="red"><b>' . formatMicro("H:i:s.micro[6]", $this->_var["starttime"]) . '</b></font></td></tr>';
            $htmlText .= '<tr bgcolor="#FFCfff"><td><font color="red">Shutdown:</font></td><td colspan="3"><font color="red"><b>' . formatMicro("H:i:s.micro[6]", $this->_var["endtime"]) . '</b></font></td></tr>';
            $htmlText .= '<tr bgcolor="#FFCfff"><td><font color="red">Execute:</font></td><td colspan="3"><font color="red"><b>' . $executetime . '</b></font></td></tr>';
            $htmlText .= '</table>';
            die(str_replace($this->setting['search'], $this->setting['replace'], $htmlText));
        }
        else
        {
            if(!empty($this->setting['error']))
            {
                $logText = "";
                foreach($this->allError as $key => $errorInfo)
                {
                    $logText .= formatMicro('H:i:s.micro[6]', $errorInfo['time']) . "\t" . $errorInfo['type'] . "\t" . $errorInfo['name'] . " " . $errorInfo['code'] . "\t" . $errorInfo['message'] . "\t" . $errorInfo['file'] . " (" . $errorInfo['line'] . ")\n";
                    if(!empty($errorInfo['trace']) && $this->setting['error'] == 2)
                    {
                        $prefix = "\tTRACE\t#";
                        foreach($errorInfo['trace'] as $stack => $trace)
                        {
                            $logText .= $prefix . $stack . "\t" . $trace['file'] . "\t" . $trace['line'] . "\t" . $trace['class'] . $trace['type'] . $trace['function'] . "\n";
                        }
                    }
                }
                if(!empty($logText))
                {
                    error_log("\n" . $logText);
                }
            }
        }
    }

    /**
     * (私有)对错误回溯追踪信息进行格式化输出处理。
     *
     * @param array $trace 错误回溯追踪信息数组
     * @return array $trace 错误回溯追踪信息数组
     */
    private function _format_trace($trace)
    {
        //逐条追踪记录处理
        foreach($trace as $stack => $detail)
        {
            $args_string = "";
            if(!empty($detail['args']))
            {
                $args_string = $this->_args_to_string($detail['args']);
            }
            $trace[$stack]['function'] = $detail['function'] . '(' . $args_string . ')';
        }
        return $trace;
    }

    /**
     * (私有)将参数转变为可读的字符串
     * 力求做到$e->getTraceAsString()的效果
     * @param array $args
     * @return string
     */
    private function _args_to_string($args)
    {
        $string = '';
        $argsAll = array();
        foreach($args as $value)
        {
            $argsAll[] = argToStr($value);
        }
        $string = implode(',', $argsAll);
        return $string;
    }

    /**
     * 指定变量名检测和显示
     */
    private function show_variables()
    {
        $variables_link = 'Variables: ';
        $variables_content = '';
        foreach(self::$setting['variables'] as $key)
        {
            $variables_link .= '<b>$' . $key . '</b>&nbsp;';
            $variables_content .= '<tr bgcolor="#FFCfff"><td><font color="red"><b>$' . $key . '</b></font></td><tr><td>';
            if(!isset($GLOBALS[$key]))
            {
                $variables_content .= '$' . $key . ' IS NOT SET.';
            }
            else
            {
                $variables_content .= "<p><pre>" . var_export($GLOBALS[$key], true) . "</pre></p>";
            }
            $variables_content .= '</td></tr>';
        }
        echo '<table border="0" cellpadding="0" cellspacing="0" bgcolor="#D4D0C8"><tr><td bgcolor="#999000">' . $variables_link . '</td></tr>' . $variables_content . '</table>';
    }

}
