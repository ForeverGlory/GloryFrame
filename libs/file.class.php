<?php
/**
 * file.class.php   文件处理类
 * 文件读、写、删 目录
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/libs/file.class.php
 * @version         $
 */
class file{

    /**
     * 读取的文件变量缓存
     */
    private $filedata = array();

    public function __construct(){
        GloryFrame::Auto($this);
    }

    /**
     * 格式化路径
     * @param   string  $path   路径
     * @param   string  $cut    截取路径
     * @return  string  绝对路径
     */
    public function formatPath($path, $cut = ''){
        //使用历史，防止重复格式化路径
        static $history = array();
        if(!arrayKeyExists($key = md5($cut . DIRECTORY_SEPARATOR . $path), $history)){
            $formatPath = str_replace(array("\\", "/"), DIRECTORY_SEPARATOR, $path);
            if($cut){
                //判断截取路径是否存在，并在初始位置
                if(strpos($formatPath, $cut) === 0){
                    //截取保留目录
                    $formatPath = substr($formatPath, strlen($cut));
                }
                $paths = explode(DIRECTORY_SEPARATOR, $formatPath);
                foreach($paths as $k => $v)
                {
                    if($v == ".."){
                        $paths[$k] = "";
                        //父目录值
                        $pre = $k - 1;
                        do
                        {
                            if(empty($paths[$pre])){
                                $pre--;
                            }else{
                                $paths[$pre] = "";
                                break;
                            }
                        }while($pre > 0);
                    }elseif($v == "."){
                        $paths[$k] = "";
                    }
                }
                $formatPath = $cut . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $paths);
            }
            $formatPath = preg_replace("/([\\" . DIRECTORY_SEPARATOR . "]{2,})/", DIRECTORY_SEPARATOR, $formatPath);
            $history[$key] = $formatPath;
        }
        return $history[$key];
    }

    /**
     * 格式化权限
     * @staticvar   array       $chmod  权限列表
     * @param       int/string  $perms  权限「八进制」字符或数字
     * @return      int(8)      八进制
     */
    public function formatChmod($perms){
        static $chmod = array();
        if(!is_int($perms)){
            if(empty($chmod)){
                $chmod = $this->config->load("chmod");
            }
            $key = substr("0000", strlen($perms)) . $perms;
            $perms = $chmod[$key];
        }
        return $perms;
    }

    /**
     * 设置权限
     * @param   string      $path   路径
     * @param   int(8)      $perms  权限「八进制」
     * @return  bool
     */
    public function chmod($filepath, $perms = 0777){
        $filepath = $this->formatPath($filepath);
        $perms = $this->formatChmod($perms);
        return @chmod($filepath, $perms);
    }

    /**
     * 获取文件或文件夹权限
     * @param   string  $filepath
     * @return  string
     */
    public function getChmod($filepath){
        $perms = false;
        $filepath = $this->formatPath($filepath);
        if(file_exists($filepath)){
            $perms = substr(base_convert(@fileperms($filepath), 10, 8), -4);
        }
        return $perms;
    }

    /**
     * 判断权限是否可读
     * @param   string  $filepath   判断路径
     * @param   string  $perms      字符串八进制表示
     * @return  bool
     */
    public function isRead($filepath, &$perms = false){
        $isRead = false;
        $perms = $this->getChmod($filepath);
        if($perms !== false){
            $writePower = array(4, 5, 6, 7);
            $isRead = in_array(substr($perms, -3, 1), $writePower);
        }
        return $isRead;
    }

    /**
     * 判断权限是否可写
     * @param   string  $filepath   判断路径
     * @param   string  $perms      字符串八进制表示
     * @return  bool
     */
    public function isWrite($filepath, &$perms = false){
        $isWrite = false;
        $perms = $this->getChmod($filepath);
        if($perms !== false){
            $writePower = array(2, 3, 6, 7);
            $isWrite = in_array(substr($perms, -3, 1), $writePower);
        }
        return $isWrite;
    }

    /**
     * 判断权限是否可执行
     * @param   string  $filepath   判断路径
     * @param   string  $perms      字符串八进制表示
     * @return  bool
     */
    public function isExec($filepath, &$perms = false){
        $isExec = false;
        $perms = $this->getChmod($filepath);
        if($perms !== false){
            $execPower = array(1, 3, 5, 6, 7);
            $isExec = in_array(substr($perms, -3, 1), $execPower);
        }
        return $isExec;
    }

    /**
     * 读取文件
     * @param   string  $file       文件地址
     * @param   string  $type       类型
     *                  null        文本
     *                  array       include方法
     *                  json
     *                  ini         parse_ini_file配置文件
     *                  serialize   文本再unserialize
     *                  table       字典    $option[cut] 参数生效
     * @param   bool    $reload     强制读取
     * @param   type    $option     其它参数
     * @return  string/array    读取的文件
     */
    public function read($file, $type = null, $reload = false, $option = null){
        $file = $this->formatPath($file);
        $isRead = $isChmod = false;
        if(!array_key_exists($md5 = md5($file), $this->filedata) || $reload){
            if(file_exists($file)){
                if(!$this->isRead($file, $perms)){
                    //文件不可读时，试图设置权限可读
                    $isRead = $isChmod = $this->chmod($file, strval(intval($perms) + 400));
                }else{
                    $isRead = true;
                }
                if($isRead){
                    if($type == "array"){
                        $data = include($file);
                    }elseif($type == "ini"){
                        $data = parse_ini_file($file, (bool)$option);
                    }else{
                        $txt = file_get_contents($file);
                        switch($type)
                        {
                            case "serialize":
                                $data = unserialize($txt);
                                break;
                            case "json":
                                $data = json_decode($txt, true);
                                break;
                            case "table":
                                $option = (array)$option;
                                $data = array();
                                $cut = empty($option["cut"]) ? " " : $option["cut"];
                                $turn = array_key_exists("turn", $option) ? $option["turn"] : 0;
                                $txt = file_get_contents($file);
                                $txt = preg_replace("/#.*/", "", $txt);
                                $array = explode("\n", $txt);
                                foreach($array as $val)
                                {
                                    list($val) = explode("#", $val, 2);
                                    $val = trim($val);
                                    if(!empty($val)){
                                        if(empty($turn)){//是否反转
                                            list($k, $v) = explode($cut, $val, 2);
                                        }else{
                                            list($v, $k) = explode($cut, $val, 2);
                                        }
                                        if(!empty($k)){
                                            $data = arrayKeysAssign($data, explode("|", $k), ltrim($v));
                                        }
                                    }
                                }
                                break;
                            default:
                                $data = $txt;
                        }
                    }
                    if($isChmod){
                        //如果重置了权限，则将权限还原
                        $this->chmod($file, $perms);
                    }
                    $this->filedata[$md5] = $data;
                }else{
                    $this->filedata[$md5] = false;
                    trigger_error($this->lang->text("FileNoReadPerms", $file));
                }
            }else{
                $this->filedata[$md5] = false;
                //trigger_error($this->lang->text("FileNoExists", $file));
            }
        }
        return $this->filedata[$md5];
    }

    /**
     * 写入文件
     * @param   string  $file       文件地址
     * @param   type    $data       写入的内容
     * @param   string  $type       写入类型 null,array,json,serialize,ini
     * @param   bool    $lock_ex    锁定
     * @return  size    返回写入的字符数
     */
    public function write($file, $data = null, $type = null, $lock_ex = true){
        $file = $this->formatPath($file);
        $isWrite = $chmodFile = $chmodPath = false;
        if(file_exists($file)){
            if(is_file($file)){
                if(!$this->isWrite($file, $perms)){
                    //文件不可写时，试图设置文件权限可写
                    $isWrite = $chmodFile = $this->chmod($file, strval(intval($perms) + 200));
                    if(!$isWrite){
                        trigger_error($this->lang->text("FileNoWritePerms", $file));
                    }
                }else{
                    $isWrite = true;
                }
            }else{
                trigger_error($this->lang->text("FileIsDir", $file));
            }
        }else{
            $path = dirname($file);
            if(!is_dir($path)){
                $this->mkdir($path, 0777);
            }
            if(is_dir($path)){
                if(!$this->isWrite($path, $perms)){
                    //文件夹不可写时，试图设置文件夹权限可写
                    $isWrite = $chmodPath = $this->chmod($file, strval(intval($perms) + 200));
                    if(!$isWrite){
                        trigger_error($this->lang->text("DirNoWritePerms", $path));
                    }
                }else{
                    $isWrite = true;
                }
            }else{
                trigger_error($this->lang->text("DirNoWritePerms", $path));
            }
        }
        if($isWrite){
            switch($type)
            {
                case "array":
                    $data = "<?php\n/**\n * $file\n */\ndefined('IN_FR') or exit();\nreturn " . var_export($data, true) . ";\n?>";
                    break;
                case "json":
                    $data = json_encode($data);
                    break;
                case "serialize":
                    $data = serialize($data);
                    break;
                case "ini":
                    $data = arrayToIni($data);
                    break;
                default:
                    break;
            }
            if($lock_ex){
                $isWrite = file_put_contents($file, $data, LOCK_EX);
            }else{
                $isWrite = file_put_contents($file, $data);
            }
            if($isWrite === false){
                trigger_error($this->lang->text("WriteFileFail", $file));
            }else{
                //是否改变过文件/文件夹权限 权限还原
                if($chmodFile || $chmodPath){
                    if($chmodPath){
                        $this->chmod($path, $perms);
                    }
                    $this->chmod($file, $perms);
                }else{
                    $this->chmod($file, 0777);
                }
                $this->filedata[md5($file)] = $data;
            }
        }
        return $isWrite;
    }

    /**
     * 删除文件
     * @param   string  $path       文件所在的目录
     * @param   type    $file       true 删除$path文件/文件夹 false 删除目录里「保留目录」 string/array 删除目录里指定文件
     * @return  bool
     */
    public function del($path, $file = true){
        $isDel = false;
        $path = $this->formatPath($path);
        if(file_exists($path)){
            if(is_dir($path)){
                $files = array();
                if(is_bool($file)){
                    $files = $this->ls($path);
                }else{
                    $files = strToArray(",", $file, null, true);
                }
                if($files){
                    foreach($files as $val)
                    {
                        $isDel = $this->del($path . DIRECTORY_SEPARATOR . $val);
                    }
                }
                if($file === true){
                    $isDel = rmdir($path);
                    if(!$isDel){
                        if($this->chmod($path, 0777)){
                            $isDel = rmdir($path);
                        }
                    }
                    if(!$isDel){
                        trigger_error($this->lang->text("DirDelFail", $path));
                    }
                }else{
                    $isDel = true;
                }
            }else{
                $isDel = unlink($path);
                if(!$isDel){
                    //更改文件权限 再执行删除
                    if($this->chmod($path, 0777)){
                        $isDel = unlink($path);
                    }
                }
                if(!$isDel){
                    trigger_error($this->lang->text("FileDelFail", $path));
                }else{
                    unset($this->filedata[md5($path)]);
                }
            }
        }else{
            trigger_error($this->lang->text("FileDirNoExists", $path));
        }
        return $isDel;
    }

    /**
     * 获取文件信息
     * @param   string          $file   文件地址
     * @param   string/array    $option 文件属性选项
     * @return  array
     */
    public function info($file, $option = array()){
        $file = $this->formatPath($file);
        if(file_exists($file)){
            $res = @lstat($file);
            //$res['filename'] = $name . $this->suf;
            //$res['filepath'] = $filepath;
            if(!empty($option)){
                if(is_string($option)){
                    $return = $res[$option];
                }else{
                    $return = arrayCheckKey($res, $option);
                }
            }else{
                $return = $res;
            }
            return $return;
        }else{
            return false;
        }
    }

    /**
     * 列目录
     * @param   string  $path   目录
     * @param   type    $suf    后缀
     * @return  array   文件列表
     */
    public function ls($path, $suf = null){
        $path = $this->formatPath($path);
        $file = array();
        if(is_dir($path)){
            $suf = strToArray(",", $suf, null, true);
            if($suf){
                foreach($suf as $key => $val)
                {
                    $suf[$key] = "\\" . (substr($val, 0, 1) == "." ? "" : ".") . $val;
                }
                $preg = "/^.*?(" . implode("|", $suf) . ")$/";
            }
            $current_dir = opendir($path);
            while(false !== ($filename = readdir($current_dir)))
            {
                if($filename != "." && $filename != ".."){
                    if(empty($suf) || ($suf && preg_match($preg, $filename))){
                        $file[] = $filename;
                    }
                }
            }
            closedir($current_dir);
        }
        return $file;
    }

    /**
     * 创建目录
     * @param   string  $path       目录
     * @param   int(8)  $perms      权限 (八进制)
     * @return  true/false
     */
    public function mkdir($path, $perms = 0777){
        $path = $this->formatPath($path);
        $return = false;
        if(!file_exists($path)){
            $oldumask = umask(0);
            if(mkdir($path, $perms, true)){
                $default = $this->formatPath($path . DIRECTORY_SEPARATOR . "index.html");
                $return = $this->write($default, "<h1>404</h1>");
            }else{
                trigger_error($this->lang->text("DirMkFail", $path));
            }
            umask($oldumask);
        }elseif(is_dir($path)){
            $return = $this->chmod($path, $perms);
        }else{
            trigger_error($this->lang->text("DirIsFile", $path));
        }
        return $return;
    }
}
?>