<?php

/**
 *  param.class.php	参数处理类
 *
 * @author			foreverglory@qq.com
 * @license			http://ys20.cn
 * @lastmodify		2011-4-22
 * */
class param {

    //路由配置
    private $route_config = '';
    public static $pageNum;
    public static $limit;
    public static $limitstart;
    public static $isdialog = false;

    public function __construct() {
        if (!get_magic_quotes_gpc()) {
            $_POST = new_addslashes($_POST);
            $_GET = new_addslashes($_GET);
            $_REQUEST = new_addslashes($_REQUEST);
            $_COOKIE = new_addslashes($_COOKIE);
        }

        $this->route_config = ys_base::load_config('route', SITE_URL) ? ys_base::
                load_config('route', SITE_URL) : ys_base::load_config('route', 'default');

        if (isset($this->route_config['data']['POST']) && is_array($this->route_config['data']['POST'])) {
            foreach ($this->route_config['data']['POST'] as $_key => $_value) {
                if (!isset($_POST[$_key]))
                    $_POST[$_key] = $_value;
            }
        }
        if (isset($this->route_config['data']['GET']) && is_array($this->route_config['data']['GET'])) {
            foreach ($this->route_config['data']['GET'] as $_key => $_value) {
                if (!isset($_GET[$_key]))
                    $_GET[$_key] = $_value;
            }
        }
        $_GET['page'] = max(intval($_GET['page']), 1);
        param::$pageNum = max(intval($_GET[pageNum]), 1);
        param::$limit = 20;
        param::$limitstart = (param::$pageNum - 1) * param::$limit;
        //判断是否是弹出的窗口，修改了js提交，如果 target="dialog" ，则会在url自动加上 dialog
        param::$isdialog = isset($_GET[dialog]) ? true : false;
        return true;
    }

    /**
     * 获取模型
     */
    public function route_m() {
        $m = defined("API") ? "api" : ( isset($_GET['m']) && !empty($_GET['m']) ? $_GET['m'] : (isset($_POST['m']) &&
                        !empty($_POST['m']) ? $_POST['m'] : ''));
        if (empty($m)) {
            return $this->route_config['m'];
        } else {
            return $m;
        }
    }

    /**
     * 获取控制器
     */
    public function route_c() {
        $c = defined("API") ? API : ( isset($_GET['c']) && !empty($_GET['c']) ? $_GET['c'] : (isset($_POST['c']) &&
                        !empty($_POST['c']) ? $_POST['c'] : ''));
        if (empty($c)) {
            return $this->route_config['c'];
        } else {
            return $c;
        }
    }

    /**
     * 获取事件
     */
    public function route_a() {
        $a = defined("API")?"cli":(isset($_GET['a']) && !empty($_GET['a']) ? $_GET['a'] : (isset($_POST['a']) &&
                !empty($_POST['a']) ? $_POST['a'] : ''));
        if (empty($a)) {
            return $this->route_config['a'];
        } else {
            return $a;
        }
    }

    /**
     * 设置 cookie
     * @param string $var     变量名
     * @param string $value   变量值
     * @param int $time    过期时间
     */
    public static function set_cookie($var, $value = '', $time = 0) {
        $time = $time > 0 ? $time : ($value == '' ? SYS_TIME - 3600 : SYS_TIME + ys_base::
                        load_config('system', 'cookie_ttl'));
        $s = $_SERVER['SERVER_PORT'] == '443' ? 1 : 0;
        $var = ys_base::load_config('system', 'cookie_pre') . $var;
        $_COOKIE[$var] = $value;
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                setcookie($var . '[' . $k . ']', sys_auth($v, 'ENCODE'), $time, ys_base::
                        load_config('system', 'cookie_path'), ys_base::load_config('system', 'cookie_domain'), $s);
            }
        } else {
            setcookie($var, sys_auth($value, 'ENCODE'), $time, ys_base::load_config('system', 'cookie_path'), ys_base::load_config('system', 'cookie_domain'), $s);
        }
    }

    /**
     * 获取通过 set_cookie 设置的 cookie 变量
     * @param string $var 变量名
     * @param string $default 默认值
     * @return mixed 成功则返回cookie 值，否则返回 false
     */
    public static function get_cookie($var, $default = '') {
        $var = ys_base::load_config('system', 'cookie_pre') . $var;
        return isset($_COOKIE[$var]) ? sys_auth($_COOKIE[$var], 'DECODE') : $default;
    }

}

?>