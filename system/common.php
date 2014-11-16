<?php

/**
 *  通用方法 入口
 *
 * @author			foreverglory@qq.com
 * @license			http://ys20.cn
 * @lastmodify		2011-4-22
 **/
session_start();
define('IN_YS20', true);

//YS20框架路径
define('FR_PATH', dirname(__file__) . DIRECTORY_SEPARATOR);
if (!defined('YS_PATH'))
    define('YS_PATH', FR_PATH . '..' . DIRECTORY_SEPARATOR);

//缓存文件夹地址
define('CACHE_PATH', YS_PATH . 'caches' . DIRECTORY_SEPARATOR);
//主机协议
define('SITE_PROTOCOL', isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://');
//当前访问的主机名
define('SITE_URL', (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''));
//来源
define('HTTP_REFERER', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
//当前脚本文件名
define('SELF', $_SERVER['PHP_SELF']);
//带参数问号后面的值
//define('QUERY_STRING', $_SERVER['QUERY_STRING'] ? "?" . $_SERVER['QUERY_STRING'] : '');
define('WEBPATH', SITE_PROTOCOL . SITE_URL);
//系统开始时间
define('SYS_START_TIME', microtime());

//加载公用函数库，执行include/functions/'global'
ys_base::load_sys_func('global');

//错误调用的函数，记录错误
set_error_handler('my_error_handler');
//设置本地时差
function_exists('date_default_timezone_set') && date_default_timezone_set(ys_base::load_config('system', 'timezone'));

//输出页面字符集
define('CHARSET', ys_base::load_config('system', 'charset'));
header('Content-type: text/html; charset=' . CHARSET);
define('SYS_TIME', time());
define('SYS_DATE', date("Y-m-d H:i:s"));
define('SYS_DATE_YMD', date("Y-m-d"));

//js 路径
define('JS_PATH', ys_base::load_config('system', 'js_path'));
//css 路径
define('CSS_PATH', ys_base::load_config('system', 'css_path'));
//img 路径
define('IMG_PATH', ys_base::load_config('system', 'img_path'));


if (ys_base::load_config('system', 'gzip') && function_exists('ob_gzhandler'))
{
    ob_start('ob_gzhandler');
}
else
{
    ob_start();
}
ys_base::load_sys_func('mail');
ys_base::load_sys_func('extention');

class ys_base
{
    /**
     * 初始化应用程序
     */
    public static function creat_app()
    {
        return self::load_sys_class('application');
    }
    /**
     * 加载系统类方法
     * @param string $classname 类名
     * @param string $path 扩展地址
     * @param intger $initialize 是否初始化
     */
    public static function load_sys_class($classname, $path = '', $initialize = 1)
    {
        return self::_load_class($classname, $path, $initialize);
    }

    /**
     * 加载模块类方法
     * @param string $classname 类名
     * @param string $m 模块
     * @param intger $initialize 是否初始化
     */
    public static function load_model_class($classname, $m = '', $initialize = 1)
    {
        $m = empty($m) && defined('ROUTE_M') ? ROUTE_M : $m;
        if (empty($m))
            return false;
        return self::_load_class($classname, $m . DIRECTORY_SEPARATOR . 'classes', $initialize);
    }

    /**
     * 加载数据模型
     * @param string $classname 类名
     */
    public static function load_model($classname)
    {
        return self::_load_class($classname, 'model');
    }
    /**
     * 加载类文件函数
     * @param string $classname 类名
     * @param string $path 扩展地址
     * @param intger $initialize 是否初始化
     */
    private static function _load_class($classname, $path = '', $initialize = 1)
    {
        static $classes = array();
        if (empty($path))
            $path = 'include' . DIRECTORY_SEPARATOR . 'classes';
        $key = md5($path . $classname);
        if (isset($classes[$key]))
        {
            if (!empty($classes[$key]))
            {
                return $classes[$key];
            }
            else
            {
                return true;
            }
        }
        if (file_exists(FR_PATH . $path . DIRECTORY_SEPARATOR . $classname . '.class.php'))
        {
            include FR_PATH . $path . DIRECTORY_SEPARATOR . $classname . '.class.php';
            $name = $classname;
            if ($initialize)
            {
                $classes[$key] = new $name;
            }
            else
            {
                $classes[$key] = true;
            }
            return $classes[$key];
        }
        else
        {
            return false;
        }
    }

    /**
     * 加载系统的函数库
     * @param string $func 函数库名
     * @param string $path 扩展地址
     */
    public static function load_sys_func($func, $path = '')
    {
        return self::_load_func($func, $path);
    }

    /**
     * 加载应用函数库
     * @param string $func 函数库名
     * @param string $m 模型名
     */
    public static function load_model_func($func, $m = '')
    {
        $m = empty($m) && defined('ROUTE_M') ? ROUTE_M : $m;
        if (empty($m))
            return false;
        return self::_load_func($func, $m . DIRECTORY_SEPARATOR . 'functions');
    }

    /**
     * 加载函数库
     * @param string $func 函数名
     * @param string $path 地址
     * 返回 false 表示加载失败
     */
    private static function _load_func($func, $path = '')
    {
        static $funcs = array();
        if (empty($path))
            $path = 'include' . DIRECTORY_SEPARATOR . 'functions';
        $path .= DIRECTORY_SEPARATOR . $func . '.func.php';
        $key = md5($path);
        if (isset($funcs[$key]))
            return true;
        if (file_exists(FR_PATH . $path))
        {
            include FR_PATH . $path;
        }
        else
        {
            $funcs[$key] = false;
            return false;
        }
        $funcs[$key] = true;
        return true;
    }

    /**
     * 加载配置文件
     * @param string $file 配置文件
     * @param string $key  要获取的配置荐
     * @param string $default  默认配置。当获取配置项目失败时该值发生作用。
     * @param boolean $reload 强制重新加载。
     */
    public static function load_config($file, $key = '', $default = '', $reload = false)
    {
        static $configs = array();
        if (!$reload && isset($configs[$file]))
        {
            if (empty($key))
            {
                return $configs[$file];
            } elseif (isset($configs[$file][$key]))
            {
                return $configs[$file][$key];
            }
            else
            {
                return $default;
            }
        }
        $path = CACHE_PATH . 'configs' . DIRECTORY_SEPARATOR . $file . '.php';
        if (file_exists($path))
        {
            $configs[$file] = include $path;
        }
        else
        {
            return false;
            //无法获取配置文件，则生成缓存文件
        }
        if (empty($key))
        {
            return $configs[$file];
        } elseif (isset($configs[$file][$key]))
        {
            return $configs[$file][$key];
        }
        else
        {
            return $default;
        }
    }
    /**
     * 写入配置文件
     * @param $file 文件名
     * @param $data 数据
     * @param $type 数据类型
     **/
    public static function set_config($file, $data, $type = 'array')
    {
        $filepath = CACHE_PATH . 'configs' . DIRECTORY_SEPARATOR;
        $filename = $file . '.php';
        if (!is_dir($filepath))
        {
            mkdir($filepath, 0777, true);
        }
        if ($type == 'array')
        {
            $data = "<?php\nreturn " . var_export($data, true) . ";\n?>";
        } elseif ($type == 'serialize')
        {
            $data = serialize($data);
        }
        //是否开启互斥锁
        if (ys_base::load_config('system', 'lock_ex'))
        {
            $file_size = file_put_contents($filepath . $filename, $data, LOCK_EX);
        }
        else
        {
            $file_size = file_put_contents($filepath . $filename, $data);
        }
        return $file_size ? $file_size : 'false';
    }
}
