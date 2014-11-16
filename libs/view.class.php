<?php
/**
 * view.class.php   模板解析缓存
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/libs/view.class.php
 * @version         $
 */
final class view
{
    /**
     * 配置信息
     * @var type
     */
    private $setting = array();
    /**
     * 如果变量在里面
     */
    private $var = array();
    private $firstTpl = true;

    /**
     * 构造函数
     * @param	string/array	$setting	模板配置，或配置名
     *          string          读取 cookie.config.php 里名称的配置
     *          array(
     *              'tplcache'          缓存目录
     *              'tplcachesuf'       缓存后缀
     *              'lock_ex'           写缓存是否加锁
     *              'path'              模板目录
     *              'suf'               模板后缀
     *              'style'             模板主题
     *              'error'             模板不存在默认模板
     *          )
     * @return  void
     */
    public function __construct($setting = array())
    {
        static $isFirst = true;
        if($isFirst)
        {
           GloryFrame::Auto($this);
            if(empty($setting) || is_string($setting))
            {
                if(empty($setting))
                {
                    $setting = $this->_var["setting"]["view"];
                }
                $setting = $this->config->load("view", array($setting));
                $setting["delimiter"][0] = formatPregStr($setting["delimiter"][0]);
                $setting["delimiter"][1] = formatPregStr($setting["delimiter"][1]);
            }

            $this->setting = $setting;
            $isFirst = false;
        }
    }

    /**
     * 传入变量
     * @param   string          $key    变量名
     * @param   string/array    $var    变量值
     */
    public function assign($key, $val)
    {
        $this->var[$key] = $val;
    }

    public function display($tplname = '', $style = '')
    {
        if(empty($tplname))
        {
            $tplname = $this->_var["place"]["m"] . "_" . $this->_var["place"]["c"] . "_" . $this->_var["place"]["a"];
        }
        if(empty($style))
        {
            $style     = $this->setting["style"];
        }
        $tplFile   = $this->file->formatPath($tplname . $this->setting["suf"], $this->_var["directory"]["template"] . $style);
        $cacheFile = $this->file->formatPath(md5($tplFile) . ".php", $this->_var["directory"]["cache"]["tpl"]);
        if(!$this->checkCache($tplFile, $cacheFile))
        {
            if(!$this->compile($tplFile, $cacheFile))
            {
                return $this->message("加载模板{$tplname}失败", null);
            }
        }
        if($this->firstTpl)
        {
            $this->output->setStatus(200);
            $this->output->setContentType($this->setting["contentType"], $this->setting["charset"]);
            $this->firstTpl = false;
        }
        include $cacheFile;
    }

    public function show($tplname = '', $style = '')
    {
        $this->display($tplname, $style);
    }

    public function message($msg = "加载模板失败", $url = "")
    {
        if($this->firstTpl)
        {
            $this->assign("msg", $msg);
            switch($url)
            {
                case "":
                case "back":
                    $js  = "history.go(-1);";
                    $url = "javascript:history.go(-1)";
                    break;
                case "null":
                    break;
                default:
                    $js  = "self.location.href=\"" . $url . "\";";
            }
            $this->assign("js", $js);
            $this->assign("url", $url);
            $this->display($this->setting["error"]);
            exit;
        }
        else
        {
            echo "<h3>" . $msg . "</h1>";
        }
    }

    /**
     * 清空模板缓存
     */
    public function clear()
    {
        return $this->file->del($this->_var["directory"]["cache"]["tpl"],false);
    }

    /**
     * 检查缓存是否过期
     * @param   string  $tpl    模板文件地址
     * @param   string  $cache  缓存文件
     * @param   int     $expire 到期时间
     * @return  true/false
     */
    public function checkCache($tpl, $cache, $expire = null)
    {
        $check = true;
        if(is_null($expire))
        {
            $expire = $this->setting["expire"];
        }
        if($expire < 0)
        {
            $check = false;
        }
        else
        {
            $cacheTime = $this->file->info($cache, "mtime");
            if($expire > 0)
            {
                //缓存文件时间+到期时间 < 当前时间 未过期
                $check = ($cacheTime + $expire) < $this->_var["systime"];
            }
            if($check)
            {
                $tplTime = $this->file->info($tpl, "mtime");
                //模板文件时间 < 缓存文件时间 未过期
                $check   = $tplTime < $cacheTime;
            }
        }
        return $check;
    }

    /**
     * 编译模板
     * @param   string  $tplFile    模板文件
     * @param   string  $cacheFile  缓存文件名/空自动生成
     */
    private function compile($tplFile, $cacheFile)
    {
        $text = $this->file->read($tplFile);
        if($text === false)
        {
            return false;
        }
        $text = $this->parse($text);
        $note = "<?php\n/**\n * cache file\n * tplFile    =  $tplFile\n * cacheFile  =  $cacheFile\n * createDate =  " . date("Y-m-d H:i:s") . "\n */\ndefined('IN_FR') or exit();\n?>\n";
        $text = $note . $text;
        if($this->file->write($cacheFile, $text))
        {
            return $cacheFile;
        }
        else
        {
            return false;
        }
    }

    /**
     * 解析模板 替换模板标签
     * @param   string  $text           模板内容
     * @param   array   $separatrix     模板标签分界
     * @return  string
     */
    private function parse($text, $separatrix = array())
    {
        $string    = "";
        /**
         * 替换短标签
         * <? ?>    ==  <?php ?>
         * <?= ?>   ==  <?php echo ;?>
         */
        $shortTags = array("/<\?\s*?=(.*?)\?>/", "/<\?(?!php)[ ]*/");
        $shortCode = array("<?php echo \\1;?>", "<?php ");
        $text = preg_replace($shortTags, $shortCode, $text);
        if(empty($separatrix))
        {
            $separatrix = $this->setting["separatrix"];
        }
        list($labBegin, $labEnd) = $separatrix;
        $labBegin   = "\\" . $labBegin;
        $labEnd     = "\\" . $labEnd;
        //$separatrixSearch = array("/^" . $this->setting["delimiter"][0] . "\s*/", "/\s*" . $this->setting["delimiter"][0] . "$/");
        //$separatrixReplace = array("<?php ", " ? >");
        $text.="<?php?>";
        $pattern    = "/(.*)((" . $this->setting["delimiter"][0] . ".*" . $this->setting["delimiter"][1] . ")|(<\?php.*\?>))+/Usx";
        preg_match_all($pattern, $text, $matches);
        list($libTags, $libCode) = $this->tag();
        array_pop($matches[4]);
        foreach($matches[3] as $key => $val)
        {
            if(!empty($val))
            {
                $val    = preg_replace($libTags, $libCode, $val);
                //$val    = preg_replace($separatrixSearch, $separatrixReplace, $val);
            }
            $string.= $matches[1][$key] . $val . $matches[4][$key];
        }
        $string = preg_replace("/\s*\?>\s*<\?php\s*/", " ", $string);
        $string = preg_replace("/\n+\s*/", "\n", $string);
        $string = $this->deny_func($string);
        return $string;
    }

    private function tag()
    {
        static $lib = array();
        if(empty($lib))
        {
            $tags = array();
            $code = array();
            //类标签
            /*
              $labClass = array("debug", "load", "file", "config", "cookie", "database", "cache", "route");
              foreach($labClass as $class)
              {
              $labSearch[]  = "/([\s\{\}\(\)=&\|;])" . strtoupper($class) . "([\s\{\}\(\)-=&\|;])/";
              $labReplace[] = "\\1\$this->" . $class . "\\2";
              }
             */
            /**
             * {show tpl=name} == <?php $this->show("name"); ?>
             */
            $tags[] = "/" . $this->setting["delimiter"][0] . "tpl\s+file=(.*?)" . $this->setting["delimiter"][1] . "/";
            $code[] = "<?php \$this->display(\"\\1\"); ?>";

            /**
             * {$var}                   ==  <?php echo $this->var["var"]; ?>
             * {$var[key][key]...}      ==  <?php echo $this->var["var"][key][key]...; ?>
             */
            $tags[] = "/" . $this->setting["delimiter"][0] . "\\$([a-zA-Z]+)((\[{1}['|\"]?\w*['|\"]?\]{1})*)" . $this->setting["delimiter"][1] . "/";
            $code[] = "<?php echo \$this->var[\"\\1\"]\\2; ?>";
            /**
             * {$var++/--}              ==  <?php $this->var["var"]++; ?>
             * {$var[key][key]++/--}    ==
             * {++/--$var}              ==  <?php ++$this->var["var"]; ?>
             * {++/--$var[key][key]}    ==
             */
            $tags[] = "/" . $this->setting["delimiter"][0] . "\\$((?!this)[a-zA-Z]+)((\[{1}['|\"]?\w*['|\"]?\]{1})*)\s*(\+\+|\-\-)" . $this->setting["delimiter"][1] . "/";
            $code[] = "<?php \$this->var[\"\\1\"]\\2\\4; ?>";
            $tags[] = "/" . $this->setting["delimiter"][0] . "(\+\+|\-\-)\s*\\$((?!this)[a-zA-Z]+)((\[{1}['|\"]?\w*['|\"]?\]{1})*)" . $this->setting["delimiter"][1] . "/";
            $code[] = "<?php \\1\$this->var[\"\\2\"]\\3; ?>";
            /**
             * $var                     ==  $this->var[var]
             * $var[key]                ==  $this->var[var][key]
             */
            $tags[] = "/\\$((?!this)[a-zA-Z]+)((\[{1}['|\"]?\w*['|\"]?\]{1})*)(\W)/";
            $code[] = "\$this->var[\"\\1\"]\\2\\4";
            /**
             * php Code代码
             * {php code}               ==  <?php code ?>
             */
            $tags[] = "/" . $this->setting["delimiter"][0] . "code\s+(.*?)" . $this->setting["delimiter"][1] . "/";
            $code[] = "<?php \\1 ?>";
            /**
             * {if bool}                ==  <?php if(bool){ ?>
             * {else}                   ==  <?php }else{ ?>
             * {elseif bool}            ==  <?php }elseif(bool){ ?>
             * {/if}                    ==  <?php } ?>
             */
            $tags[] = "/" . $this->setting["delimiter"][0] . "if\s+(.+?)" . $this->setting["delimiter"][1] . "/";
            $code[] = "<?php if(\\1){ ?>";
            $tags[] = "/" . $this->setting["delimiter"][0] . "else\}/";
            $code[] = "<?php }else{ ?>";
            $tags[] = "/" . $this->setting["delimiter"][0] . "elseif\s+(.+?)" . $this->setting["delimiter"][1] . "/";
            $code[] = "<?php }elseif(\\1){ ?>";
            $tags[] = "/" . $this->setting["delimiter"][0] . "\/if\}/";
            $code[] = "<?php } ?>";
            /**
             * {for int;bool;int++}     ==  <?php for(int;bool;int++){ ?>
             * {/for}                   ==  <?php } ?>
             */
            $tags[] = "/" . $this->setting["delimiter"][0] . "for\s+(.+?)" . $this->setting["delimiter"][1] . "/";
            $code[] = "<?php for(\\1){ ?>";
            $tags[] = "/" . $this->setting["delimiter"][0] . "\/for" . $this->setting["delimiter"][1] . "/";
            $code[] = "<?php } ?>";
            /**
             * {loop $data $key $val}   ==
             * <?php if(is_array(\\1)){\$this->var[\"n\"]=0;foreach(\\1 as \\2=>\\3){\$this->var[\"n\"]++; ?>
             * {loop $data $val}        ==
             * <?php if(is_array(\\1)){\$this->var[\"n\"]=0;foreach(\\1 as \\2){\$this->var[\"n\"]++; ?>
             * {/loop}                  ==  <?php }} ?>
             */
            $tags[] = "/" . $this->setting["delimiter"][0] . "loop\s+(\S+)\s+(\S+)\s+(\S+)" . $this->setting["delimiter"][1] . "/";
            $code[] = "<?php if(is_array(\\1)){\$this->var[\"n\"]=0;foreach(\\1 as \\2=>\\3){\$this->var[\"n\"]++; ?>";
            $tags[] = "/" . $this->setting["delimiter"][0] . "loop\s+(\S+)\s+(\S+)" . $this->setting["delimiter"][1] . "/";
            $code[] = "<?php if(is_array(\\1)){\$this->var[\"n\"]=0;foreach(\\1 as \\2){\$this->var[\"n\"]++; ?>";
            $tags[] = "/" . $this->setting["delimiter"][0] . "\/loop\}/";
            $code[] = "<?php }} ?>";
            /**
             * {switch bool}                ==  <?php switch(bool){ ?>
             * {default}                    ==  <?php default: ?>
             * {case option}                ==  <?php case option: ?>
             * {/case}                      ==  <?php break; ?>
             * {/switch}                    ==  <?php } ?>
             */
            $tags[] = "/" . $this->setting["delimiter"][0] . "switch\s+(.+?)" . $this->setting["delimiter"][1] . "/";
            $code[] = "<?php switch(\\1){ ?>";
            $tags[] = "/" . $this->setting["delimiter"][0] . "default" . $this->setting["delimiter"][1] . "/";
            $code[] = "<?php default: ?>";
            $tags[] = "/" . $this->setting["delimiter"][0] . "case\s+['|\"]?(.*?)['|\"]?" . $this->setting["delimiter"][1] . "/";
            $code[] = "<?php case \"\\1\": ?>";
            $tags[] = "/" . $this->setting["delimiter"][0] . "\/case" . $this->setting["delimiter"][1] . "/";
            $code[] = "<?php break; ?>";
            $tags[] = "/" . $this->setting["delimiter"][0] . "\/switch" . $this->setting["delimiter"][1] . "/";
            $code[] = "<?php } ?>";
            /**
             * {URL}                    ==  <?php echo $this->route->url(); ?>
             */
            $tags[] = "/" . $this->setting["delimiter"][0] . "URL" . $this->setting["delimiter"][1] . "/";
            $code[] = "<?php echo \$this->route->url(\\1); ?>";
            /**
             * {URL()}                  ==  <?php echo $this->route->url(); ?>
             * {URL('')}                ==  <?php echo $this->route->url(''); ?>
             */
            $tags[] = "/" . $this->setting["delimiter"][0] . "URL\((.*?)\)" . $this->setting["delimiter"][1] . "/";
            $code[] = "<?php echo \$this->route->url(\\1); ?>";
            /**
             * {COOKIE[key][key]}       ==  <?php echo $this->cookie->get(array(key,key)); ?>
             */
            $tags[] = "/" . $this->setting["delimiter"][0] . "COOKIE(\[(.*?)\])+" . $this->setting["delimiter"][1] . "/";
            $code[] = "<?php echo \$this->cookie->get(array(\"\\2\",)); ?>";

            //html标签
            $tags[] = "/" . $this->setting["delimiter"][0] . "HIDDEN\((.*?)\)" . $this->setting["delimiter"][1] . "/";
            $code[] = "<?php echo \$this->html->hidden(\\1); ?>";
            $tags[] = "/" . $this->setting["delimiter"][0] . "CHECKBOX\((.*?)\)" . $this->setting["delimiter"][1] . "/";
            $code[] = "<?php echo \$this->html->checkbox(\\1); ?>";
            $tags[] = "/" . $this->setting["delimiter"][0] . "SELECT\((.*?)\)" . $this->setting["delimiter"][1] . "/";
            $code[] = "<?php echo \$this->html->select(\\1); ?>";

            /**
             * {string}                 ==  <?php echo string; ?>
             */
            $tags[] = "/" . $this->setting["delimiter"][0] . "((?!\s).*?)" . $this->setting["delimiter"][1] . "/";
            $code[] = "<?php echo \\1; ?>";

            $lib = array($tags, $code);
        }
        return $lib;
    }

    /**
     * 禁用模板里某函数
     * @param   string  $text
     * @return  string
     */
    private function deny_func($text)
    {
        if($this->setting["deny_func"])
        {
            $funcs = strToArray(",", $this->setting["deny_func"]);
            foreach($funcs as $func)
            {

            }
        }
        return $text;
    }

}
?>