<?php
/**
 * lang.class.php   应用程序类
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/libs/lang.class.php
 * @version         $
 */
class lang
{
    /**
     * 存储的语言包
     */
    private $langs;
    /**
     * 语言类配置信息
     */
    private $setting;

    /**
     * 构造函数
     * @param	string/array	$setting	缓存配置，或配置名
     *          string          读取 lang.config.php 里名称的配置
     *          array(
     *              'language'          语言各类
     *              'path'              应用语言包位置
     *              'suf'               语言包后缀
     *          )
     * @return  void
     */
    public function __construct($setting = '')
    {
        static $isFirst = true;
        if($isFirst)
        {
           GloryFrame::Auto($this);
            if(empty($setting) || is_string($setting))
            {
                $setting = $this->config->load("lang", array($setting));
            }
            $this->setting = $setting;
            $isFirst = false;
        }
    }

    /**
     * 加载语言包
     * @param   string  $file   文件名
     * @param   string  $path   目录
     */
    public function load($file, $language = '', $path = '', $suf = '')
    {
        if(empty($file))
        {
            return false;
        }
        $language = empty($language) ? $this->setting['language'] : $language;
        $suf = empty($suf) ? $this->setting['suf'] : $suf;
        //系统语言包
        $fr_file = $this->file->formatPath($language . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file . '.lang.php', $this->_var["syspath"]["lang"]);
        //应用自定义语言包
        $app_file = $this->file->formatPath($language . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $file . $suf, $this->_var["directory"]["lang"]);
        //已经加载过的语言包
        $lang = array();
        if(file_exists($fr_file))
        {
            $lang = $this->file->read($fr_file, "table");
        }
        if(file_exists($app_file))
        {
            $lang = arrayMerge($lang, $this->file->read($app_file, "table"));
        }
        //加载完变量后，还是等于原来的，表示未加载成功
        if(!empty($lang))
        {
            $this->langs[$language] = arrayMerge($this->langs[$language], $lang);
        }
        else
        {
            $this->debug->msg("Load lang $file false", 1);
            return false;
        }
    }

    /**
     * 生成语言包
     */
    public function make($file, $language = '', $path = '', $suf = '')
    {

    }

    /**
     * 获取语言包对应的语言
     * @param   string  $var        变量
     * @param   string  $code       字符集参数
     * @return  string/array
     */
    public function text($var, $code = null)
    {
        if(empty($var))
        {
            return;
        }
        $var = strToArray(",", $var);
        $lang = $this->langs[$this->setting['language']];
        $lang = arrayGetRecursion($lang, $var);
        $lang = str_replace("{\$code}", $code, $lang);
        return $lang;
    }

}
?>