<?php
defined('IN_YS20') or exit('No permission resources.');
/**
 * 用户信息
 */
ys_base::load_sys_class('model', '', 0);
class member_model extends model
{
    public $table_name = '';
    public $category = '';
    public function __construct()
    {
        $this->db_config = ys_base::load_config('database');
        $this->db_setting = 'default';
        parent::__construct();
    }
    //返回用户组信息
    public function getGroup()
    {
        return parent::select("*", "group", "", "", "gid asc", "", "gid");
    }
    public function setGroupPower($powers, $gid)
    {
        parent::update("power='$powers'", "group", "gid='$gid'");
        $this->makecache("group");
    }
    //验证用户注册信息
    public function checkreg($data, &$checkresult)
    {
        $regcheck = ys_base::load_config("system", "regcheck");
        $checkresult = 1;
        $regdata = array();
        $regdata['regip'] = ip();
        $regdata['regtime'] = time();
        foreach ($data as $n => $v)
        {
            if (in_array($n, $regcheck))
            {
                if (!$v)
                {
                    $checkresult = -1;
                }
                $regdata[$n] = $v;
            }
        }
        if (!$regdata)
        {
            $checkresult = -1;
        }
        if ($this->isuser($regdata[username]))
        {
            $checkresult = 0;
        }
        return $regdata;
    }
    public function edituser($data)
    {
        $uid = $data[uid];
        $regcheck = ys_base::load_config("system", "regcheck");
        $data = checkkey($data, $regcheck);
        if ($data[password])
        {
            $data[password] = md5(md5(trim($data[password])) . md5(trim($data[password])));
        }else{
            unset($data[password]);
        }
        $uid = parent::update($data, "user", "uid='$uid'");
        return $uid;
    }
    //判断用户是否存在，返回0表示用户不存在
    public function isuser($username)
    {
        $same = parent::count("user", "username='$username'");
        return $same;
    }
    //注册用户
    public function register($data)
    {
        $regcheck = ys_base::load_config("system", "regcheck");
        foreach ($data as $n => $v)
        {
            if (in_array($n, $regcheck))
            {
                if ($n == "password")
                {
                    $v = md5(md5(trim($v)) . md5(trim($v)));
                }
                $regdata[$n] = $v;
            }
        }
        //如果赋值，则自动激活
        //$regdata[checked]=1;
        $uid = parent::insert($regdata, "user", true);
        if ($uid)
        {
            //如果$uid为-1则表示未通过审核
            $uid = $this->login($data[username], $data[password]);
        }
        return $uid;
    }
    //查询用户信息
    public function getuser($where, $row = "")
    {
        $user = parent::get_one("*", "user", $where);
        if ($row)
        {
            return $user[$row];
        }
        else
        {
            return $user;
        }
    }
    //通过id查询用户名
    public function getUsernameFromUid($uid)
    {
        return $this->getUserFromId($uid,"username");
    }
    public function getUserFromId($uid, $row = "")
    {
        $user = parent::get_one("*", "user", "uid='$uid'");
        if ($row)
        {
            return $user[$row];
        }
        return $user;
    }
    /**
     * 查找用户组所有用户
     **/
    public function getUserFromGroup($gid)
    {
        $users = parent::select("*", "user", "groupid='$gid'");
        return $users;
    }
    //登陆用户
    public function login($username, $password)
    {
        $where = array("username" => $username, "password" => md5(md5($password) . md5($password)));
        $user = $this->getuser($where);
        if ($user)
        {
            if (!$user['checked'])
            {
                //未验证用户
                return - 1;
            }
            $rndpwd = md5(random(32));
            $_SESSION['rndpwd'] = $rndpwd;
            $_SESSION['pwd'] = md5($rndpwd . $user[password]);
            $_SESSION['uid'] = $user['uid'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['checktype'] = "session";
            param::set_cookie("rndpwd", $rndpwd);
            param::set_cookie("uid", $user['uid']);
            param::set_cookie("username", $user['username']);
            param::set_cookie("pwd", md5($rndpwd . $user[password]));
            $updatelogin = array('rndpwd' => $rndpwd, 'lasttime' => time(), 'lastip' => ip(), 'logincount' => '+=1');
            parent::update($updatelogin, "user", "uid='$user[uid]'");
        }
        return $user[uid];
    }
    public function islogin()
    {
        //判断是否是会话登陆
        if ($_SESSION['checktype'] == "session")
        {
            $where = array();
            $where[rndpwd] = $_SESSION[rndpwd];
            $where[uid] = $_SESSION[uid];
            $where[username] = $_SESSION[username];
            $user = $this->getuser($where);
            if ($_SESSION['pwd'] == md5($user[rndpwd] . $user[password]))
            {
                if (param::get_cookie("rndpwd") != $_SESSION[rndpwd])
                    param::set_cookie("rndpwd", $_SESSION[rndpwd]);
                if (param::get_cookie("uid") != $_SESSION[uid])
                    param::set_cookie("uid", $_SESSION[uid]);
                if (param::get_cookie("username") != $_SESSION[username])
                    param::set_cookie("username", $_SESSION[username]);
                if (param::get_cookie("pwd") != $_SESSION[pwd])
                    param::set_cookie("pwd", $_SESSION['pwd']);
            }
            else
            {
                unset($user);
                unset($_SESSION['checktype']);
            }
        }
        //判断是否是Cookie登陆
        if ($_SESSION['checktype'] != "session")
        {
            $where = array();
            $where[rndpwd] = param::get_cookie("rndpwd");
            $where[uid] = param::get_cookie("uid");
            $where[username] = param::get_cookie("username");
            $user = $this->getuser($where);
            if (param::get_cookie('pwd') == md5($user[rndpwd] . $user[password]))
            {
                $_SESSION['checktype'] = "cookie";
            }
            else
            {
                $this->logout();
                unset($user);
                return false;
            }
        }
        if ($user[checked] != 1)
        {
            $this->logout();
        }
        $group = getcache("group");
        if ($user[groupid] == 10)
        {
            $gourppower = array("admin");
        }
        else
        {
            $gourppower = explode("|", $group[$user[groupid]][power]);
        }
        $userpower = explode("|", $user[power]);
        $user[power] = array_filter(array_merge($gourppower, $userpower));
        $user[groupname] = $group[$user[groupid]][groupname];
        return $user;
    }
    /**
     * 退出
     **/
    public function logout()
    {
        unset($_SESSION['rndpwd']);
        unset($_SESSION['pwd']);
        unset($_SESSION['uid']);
        unset($_SESSION['username']);
        unset($_SESSION['checktype']);
        param::set_cookie('rndpwd');
        param::set_cookie('pwd');
        param::set_cookie('uid');
        param::set_cookie('username');
    }
    public function userlist($where, $limit = '')
    {
        return parent::select("*", "user", $where, $limit, "uid asc", "", "uid");
    }
    public function getCountUsers($where)
    {
        return parent::count("user", $where);
    }
    /**
     * 判断操作权限
     **/
    public function checkpower($nowpower)
    {
        $user = $this->islogin();
        if ($user)
        {
            $userpower = $user[power];
            if ($userpower)
            {
                //判断是否是超级用户
                if (in_array("admin", $userpower))
                {
                    return true;
                }
                if (in_array($nowpower, $userpower))
                {
                    return true;
                }
            }
        }
        return false;
    }
    /**
     * 用户操作日志
     * @param $msg 消息
     * @param $operate 操作
     * @param $val 当前值
     * @param $row 列
     * @param $oldval 旧值
     * @param $table 更新的表
     **/
    public function memberlog($msg, $operate = '', $val = '', $row = '', $oldval = '', $table = '')
    {
        $user = $this->islogin();
        $data = array();
        $data[msg] = $msg;
        $date[operate] = $operate;
        $data[val] = $val;
        $data[row] = $row;
        $data[oldval] = $oldval;
        $data[table] = $table;
        $data[uid] = $user[uid];
        $data[group] = $user[groupname];
        $data[logtime] = time();
        parent::insert($data, "cslog", false);
    }
    public function log($msg,$operate="system"){
        $data = array();
        $data[msg] = $msg;
        $data[operate] = $operate;
        $data[uid] =0;
        $data[group] = 0;
        $data[logtime] = time();
        parent::insert($data, "cslog", false);
    }
    public function getlog($where, $limit, $order, $group = '', $key = '')
    {
        return parent::select("*", "cslog", $where, $limit, $order, $group, $key);
    }
    public function getcountlog($where)
    {
        return parent::count("cslog", $where);
    }
    /**
     * 发送消息
     * @param $title 标题
     * @param $message 内容
     * @param $recipient 收件人 0 表示所有人能收到 负数分别代表会员组
     * @param $sender 发件人 0 表示系统
     * @param $expiretime 到期时间
     * @param $type 消息类型
     * @param $reply 是否是回复消息，默认为0
     **/
    public function pm($title,$message,$recipient,$sender,$expiretime=0,$type='system',$reply=0,$config=''){
        $pm=array();
        $pm[recipient]=$recipient;
        $pm[type]=$type;
        $pm[title]=$title;
        $pm[message]=$message;
        $pm[reply]=$reply;
        $pm[sender]=intval($sender);
        $pm[sendtime]=SYS_TIME;
        $pm[expiretime]=$expiretime;
        $pm[recipient]=$recipient;
        $pm[isread]=0;
        $pm[config]=$config;
        return parent::insert($pm,"pms",false);
    }
    /**
     * 获取消息通知
     * @param $recipient 收件人，公告为0
     * @param $limit 取值
     * @param &$num 总数量
     **/
    public function getpm($recipient,$limit="",&$num){
        $where="recipient='$recipient'";
        if($sender==0){
            $where.=" and sender='0'";
        }elseif($sender=""){
        }else{
            $where.=" and sender>'0'";
        }
        $order="isread asc,sendtime desc";
        $num=parent::count("pms",$where);
        return parent::select("*","pms",$where,$limit,$order);
    }
    /**
     * 获取新的消息
     **/
    public function getNewpm($uid){
        $pms=parent::select("*","pms","isread=0 and recipient='$uid'","","pmid desc");
        return $pms;
    }
    /**
     * 获取消息数量
     **/
    public function getNewpmNum($uid){
        return parent::count("pms","isread=0 and recipient='$uid'");
    }
    public function getNotice_one(){
        $where="recipient=0 and sender=0";
        return parent::get_one("*","pms",$where,"pmid desc");
    }
    /**
     * 获取一条新闻
     **/
    public function getpm_one($pmid){
        return parent::get_one("*","pms","pmid='$pmid'");
    }
    /**
     * 修改为已读
     **/
    public function setpmread($pmid){
        parent::update("isread=1,readtime='".SYS_TIME."'","pms","pmid='$pmid' and isread=0");
    }
    /**
     * 通过标识，判断消息是否发送过
     **/
    public function isSend($config){
        return parent::count("pms","config='$config'");
    }
    /**
     * 发送邮件到单用户
     **/
    public function email_uid($uid,$subject,$message){
        $user=$this->getUserFromId($uid);
        $tomail=mailStr($user[email],$user[username]);
        sendmail($tomail,$subject,$message);
    }
    /**
     * 发送邮件到用户组
     **/
    public function email_group($groupid,$subject,$message){
        $group=$this->getUserFromGroup($groupid);
        $tomail=array();
        foreach($group as $user){
            $tomail[]=mailStr($user[email],$user[username]);
        }
        sendmail($tomail,$subject,$message);
    }
    /**
     * 设置主题
     **/
    public function setTheme($uid,$themename){
        parent::update("theme='$themename'","user","uid='$uid'");
    }
    function mailGroup($groupid,$title,$msg){
        $groups='';
        sendmail();
    }
    //生成会员相关缓存
    public function makecache($type)
    {
        $cache = array();
        switch ($type)
        {
            case "group":
                $group = $this->getGroup();
                setcache("group", $group);
                $cache = getcache("group");
                break;
        }
        return $cache;
    }
}

?>