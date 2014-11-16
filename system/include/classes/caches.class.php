<?php
/**
 *  caches.class.php 缓存类
 *
 * @author			foreverglory@qq.com
 * @license			http://ys20.cn
 * @lastmodify		2011-4-22
 **/
class caches
{

    /*缓存默认配置*/
    protected $suf = '.cache.php';
     /*缓存文件后缀*/


    /**
     * 构造函数
     * @param	array	$setting	缓存配置
     * @return  void
     */
    public function __construct($setting = '')
    {
    }

    /**
     * 写入缓存
     * @param	string	$name		缓存名称
     * @param	mixed	$data		缓存数据
     * @param	string	$type		缓存类型 array/serialize
     * @param	string	$filepath	自定义路径
     * @return  mixed				缓存路径/false
     */

    public function set($name, $data, $type = 'array', $filepath = '')
    {
        $filepath = CACHE_PATH . 'caches' . DIRECTORY_SEPARATOR . ($filepath ? $filepath .
            DIRECTORY_SEPARATOR : '');
        $filename = $name . $this->suf;
        if (!is_dir($filepath)) {
            mkdir($filepath, 0777, true);
        }
        if ($type == 'array') {
            $data = "<?php\nreturn " . var_export($data, true) . ";\n?>";
        } elseif ($type == 'serialize') {
            $data = serialize($data);
        }
        //记录到数据库里
        /*
        if ($module == 'commons' && substr($name, 0, 16) != 'category_content') {
        $db = ys_base::load_model('cache_model');
        $datas = new_addslashes($data);
        if ($db->get_one(array('filename'=>$filename, 'path'=>'caches_'.$module.'/caches_'.$type.'/'), '`filename`')) {
        $db->update(array('data'=>$datas), array('filename'=>$filename, 'path'=>'caches_'.$module.'/caches_'.$type.'/'));
        } else {
        $db->insert(array('filename'=>$filename, 'path'=>'caches_'.$module.'/caches_'.$type.'/', 'data'=>$datas));
        }
        }
        */
        //是否开启互斥锁
        if (ys_base::load_config('system', 'lock_ex')) {
            $file_size = file_put_contents($filepath . $filename, $data, LOCK_EX);
        } else {
            $file_size = file_put_contents($filepath . $filename, $data);
        }

        return $file_size ? $file_size : 'false';
    }

    /**
     * 获取缓存
     * @param	string	$name		缓存名称
     * @param	string	$type		缓存类型
     * @param	string	$filepath	自定义路径
     * @return  mixed	$data		缓存数据
     */
    public function get($name, $type = 'array', $filepath = '')
    {
        $filepath = CACHE_PATH . 'caches' . DIRECTORY_SEPARATOR . ($filepath ? $filepath .
            DIRECTORY_SEPARATOR : '');
        $filename = $name . $this->suf;
        if (!file_exists($filepath . $filename)) {
            return false;
        } else {
            if ($type == 'array') {
                $data = @require ($filepath . $filename);
            } elseif ($type == 'serialize') {
                $data = unserialize(file_get_contents($filepath . $filename));
            } else {
                $data = file_get_contents($filepath . $filename);
            }
            return $data;
        }
    }

    /**
     * 删除缓存
     * @param	string	$name		缓存名称
     * @param	string	$filepath	自定义路径
     * @return  bool
     */
    public function delete($name, $filepath = '')
    {
        $filepath = CACHE_PATH . 'caches' . DIRECTORY_SEPARATOR . ($filepath ? $filepath .
            DIRECTORY_SEPARATOR : '');
        $filename = $name . $this->suf;
        if (file_exists($filepath . $filename)) {
            return @unlink($filepath . $filename) ? true : false;
        } else {
            return false;
        }
    }


    public function cacheinfo($name, $filepath = '')
    {
        $filepath = CACHE_PATH . 'caches' . DIRECTORY_SEPARATOR . ($filepath ? $filepath .
            DIRECTORY_SEPARATOR : '');
        $filename = $name . $this->suf;
        if (file_exists($filepath . $filename)) {
            $res['filename'] = $name . $this->suf;
            $res['filepath'] = $filepath;
            $res['filectime'] = filectime($filepath . $filename);
            $res['filemtime'] = filemtime($filepath . $filename);
            $res['filesize'] = filesize($filepath . $filename);
            return $res;
        } else {
            return false;
        }
    }

}

?>