<?php
/**
 * cache.class.php  缓存类
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/libs/cache.class.php
 * @version         $
 */
class cache
{
    /**
     * 配置信息
     */
    private $setting = array();

    /**
     * 构造函数
     * @param	string/array	$setting	缓存配置，或配置名
     *          string          读取 cache.config.php 里名称的配置
     *          array(
     *              'dir'       缓存目录
     *              'lock'      写入锁定
     *              'type'      缓存格式
     *              'suf'       缓存后缀
     *
     *          )
     * @return  void
     */
    public function __construct()
    {
        static $isFirest = false;
        {
           GloryFrame::Auto($this);
            $this->setting = $this->config->load("cache", array($this->_var["setting"]["cache"]));
            $this->setting["dir"] = $this->_var["directory"]["cache"]["cache"];
            $isFirest = true;
        }
    }

    /**
     * 写入缓存
     * @param	string	$name		缓存名称
     * @param	mixed	$data		缓存数据
     * @param	string	$filepath	自定义路径  APP_PATH/caches/{caches}/*
     * @param	string	$type		缓存类型    array / json / ini / serialize / txt
     * @return  mixed	缓存路径/false
     */
    public function set($name, $data, $filepath = '', $type = '')
    {
        $file = $this->setting['dir'] . $filepath . DIRECTORY_SEPARATOR . $name . $this->setting['suf'];
        $type = empty($type) ? $this->setting["type"] : $type;
        return $this->file->write($file, $data, $type, $this->setting['lock']);
    }

    /**
     * 获取缓存
     * @param	string	$name		缓存名称
     * @param	string	$filepath	自定义路径  APP_PATH/caches/{caches}/*
     * @param	string	$type		缓存类型    array / json / ini / serialize / txt
     * @return  mixed	缓存数据
     */
    public function get($name, $filepath = '', $type = '')
    {
        $file = $this->setting['dir'] . $filepath . DIRECTORY_SEPARATOR . $name . $this->setting['suf'];
        $type = empty($type) ? $this->setting["type"] : $type;
        return $this->file->read($file, $type, true);
    }

    /**
     * 删除缓存
     * @param	string	$name		缓存名称
     * @param	string	$filepath	自定义路径
     * @return  bool
     */
    public function del($name, $filepath = '')
    {
        $filepath = $this->setting['dir'] . $filepath . DIRECTORY_SEPARATOR;
        return $this->file->del($filepath, $name . $this->setting['suf']);
    }

    /**
     * 删除所有缓存
     * @param   string  $filepath   可选 缓存目录下子目录
     * @return  bool
     */
    public function clear($filepath = '')
    {
        $filepath = $this->setting['dir'] . $filepath . DIRECTORY_SEPARATOR;
        return $this->file->del($filepath,false);
    }

    /**
     * 获取缓存信息
     * @param   string  $name       缓存名称
     * @param   string  $filepath   自定义路径
     * @return  array
     */
    public function info($name, $filepath = '')
    {
        $file = $this->setting['dir'] . $filepath . DIRECTORY_SEPARATOR . $name . $this->setting['suf'];
        return $this->file->info($file);
    }

}
?>