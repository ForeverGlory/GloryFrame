<?php
/**
 * mysql.class.php  mysql数据库实现类
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/libs/database/mysql.class.php
 * @version         $
 */
final class mysql{

    /**
     * 数据库配置信息
     */
    private $setting = null;
    /**
     * 数据库连接资源句柄
     */
    public $link = null;
    /**
     * 最近一次查询资源句柄
     */
    public $lastqueryid = null;
    /**
     * 最近一次查询语句
     */
    public $lastQuery = "";
    /**
     *  统计数据库查询次数
     */
    public $querycount = 0;

    public function __construct($setting = array()){
        $this->setting = $setting;
        if($setting["autoconnect"]){
            $this->connect();
        }
    }

    /**
     * 数据库连接
     * @param   array       $setting    数据库连接配置
     * @return  resource    连接资源
     */
    public function connect($setting = array()){
        if($setting){
            $this->setting = $setting;
        }
        $func = $this->setting["pconnect"] == 1 ? "mysql_pconnect" : "mysql_connect";
        if(!$this->link = @$func($this->setting["hostname"], $this->setting["username"], $this->setting["password"], 1)){
            $this->halt("Can't connect to MySQL Server");
            return false;
        }

        if($this->version() > "4.1"){
            $charset = isset($this->setting["charset"]) ? $this->setting["charset"] : "";
            $serverset = $charset ? "character_set_connection='$charset',character_set_results='$charset',character_set_client=binary" : "";
            $serverset .= $this->version() > "5.0.1" ? ((empty($serverset) ? "" : ",") . " sql_mode='' ") : "";
            $serverset && mysql_query("SET $serverset", $this->link);
        }
        if($this->setting["database"] && !$this->useDB($this->setting["database"], $this->setting["isCreate"])){
            $this->close();
        }
        return $this->link;
    }

    /**
     * 选择数据库
     * @param   string  $database   数据库名称
     * @param   bool    $isCreate   数据库不存在时，是否创建
     * @return  bool
     */
    public function useDB($database, $isCreate = false){
        $isUse = @mysql_select_db($database, $this->link);

        if($isUse){
            $this->database = $database;
        }elseif($isCreate){
            $isUse = $this->createDB($database, true);
        }else{
            $this->halt("Can't use database {$database}");
        }
        return $isUse;
    }

    /**
     * 检查参数，返回正确SQL语句
     * @param   string/array    $args   参数
     * @param   string          $type   判断的类型
     *                          field、intofield、table、where、limit、order、group
     * @return  string          SQL语句
     */
    private function check_args($args = "", $type = "data"){
        $return = "";
        switch($type)
        {
            case "field":
                if(!is_array($args)){
                    $args = explode(",", $args);
                }
                array_walk($args, array($this, "add_special_char"));
                $return = implode(",", $args);
                if(empty($return)){
                    $return = "*";
                }
                break;
            case "intofield":
                if(!is_array($args)){
                    $args = explode(",", $args);
                }
                array_walk($args, array($this, "add_special_char"));
                $return = implode(",", $args);
                $return = empty($return) ? "" : "(" . $return . ")";
                break;
            case "table":
                if(!empty($args)){
                    if(is_array($args)){
                        $return = array();
                        foreach($args as $k => $v)
                        {
                            if(is_numeric($k)){
                                $return[] = "`" . $this->setting["database"] . "`.`" . $this->setting["tablepre"] . $v . "` as " . $this->add_special_char($v);
                            }else{
                                $return[] = "`" . $this->setting["database"] . "`.`" . $this->setting["tablepre"] . $v . "` as " . $this->add_special_char($k);
                            }
                        }
                        $return = implode(",", $return);
                    }else{
                        $return = "`" . $this->setting["database"] . "`.`" . $this->setting["tablepre"] . $args . "`";
                    }
                }
                break;
            case "where":
                if(!empty($args)){
                    if(is_array($args)){
                        $return = " WHERE 1=1";
                        foreach($args as $field => $value)
                        {
                            if(is_array($value)){
                                switch(strval($field))
                                {
                                    case ">":
                                    case ">=":
                                    case "<":
                                    case "<=":
                                    case "!=":
                                        //array(">"=>array("a"=>"b"))         ==  AND `a` > 'b'
                                        foreach($value as $key => $val)
                                        {
                                            $return .=" AND " . $this->add_special_char($key) . " " . $field . " " . $this->escape_string($val);
                                        };
                                        break;
                                    case "%":
                                        foreach($value as $key => $val)
                                        {
                                            $val = "%{$val}%";
                                            $return.=" AND " . $this->add_special_char($key) . " LIKE " . $this->escape_string($val);
                                        }
                                        break;
                                    default:

                                        //没有下标 array(array("a=b","b=c"))    ==  AND (a=b or b=c)
                                        if(is_numeric($field)){
                                            $return .= " AND (" . implode(" OR ", $value) . ")";
                                        }
                                        //存在正常下标 array("a"=>array("b","c"))==  AND `a` IN ('b','c')
                                        else{
                                            if($value){
                                                array_walk($value, array($this, "escape_string"));
                                                $return .= " AND " . $this->add_special_char($field) . " IN (" . implode(",", $value) . ")";
                                            }else{
                                                $return .= " AND " . $this->add_special_char($field) . " <> " . $this->add_special_char($field);
                                            }
                                        }
                                }
                            }else{
                                $return .= " AND " . $this->add_special_char($field) . "=" . $this->escape_string($value);
                            }
                        }
                    }else{
                        $return = " WHERE " . $args;
                    }
                }
                break;
            case "limit":
                if(!empty($args)){
                    $return = " LIMIT " . $args;
                }
                break;
            case "order":
                if(!empty($args)){
                    if(is_array($args)){
                        $return = implode(",", $args);
                    }else{
                        $return = $args;
                    }
                    $return = " ORDER BY " . $return;
                }
                break;
            case "group":
                if(!empty($args)){
                    if(is_array($args)){
                        $return = implode(",", $args);
                    }else{
                        $return = $args;
                    }
                    $return = " GROUP BY " . $return;
                }
                break;
            case "update":
                if(!empty($args)){
                    if(is_array($args)){
                        $fields = array();
                        foreach($args as $field => $value)
                        {
                            switch($field)
                            {
                                case "+":
                                case "-":
                                case ".":
                                    if(is_array($value)){
                                        foreach($value as $key => $val)
                                        {
                                            if($field == "."){
                                                $fields[] = $this->add_special_char($key) . "=CONCAT(" . $this->add_special_char($key) . "," . $this->escape_string($val) . ")";
                                            }else{
                                                $fields[] = $this->add_special_char($key) . "=" . $this->add_special_char($key) . $field . $this->escape_string($val);
                                            }
                                        }
                                    }
                                    break;
                                default:
                                    $fields[] = $this->add_special_char($field) . "=" . $this->escape_string($value);
                                    break;
                            }
                        }
                        $return = implode(",", $fields);
                    }else{
                        $return = $args;
                    }
                }
                break;
            default:
        }
        return $return;
    }

    /**
     * 查询所有
     * @param   string/array    $field      需要查询的字段值[例`name`,`gender`,`birthday`]
     * @param   string          $table      查询的表
     * @param   string/array    $where      查询条件
     *                          string      `name`='$name'
     *                          array       `name`=>array('$name1','$name2')    ==  `name` in ('$name1','$name2')
     *                          array       array($where,$where)                ==  $where or $where
     * @param   string          $limit      返回结果范围[例：10或10,10 默认为空]
     * @param   string/array    $order      排序方式[默认按数据库默认方式排序]
     * @param   string/array    $group      分组方式[默认为空]
     * @param   string          $key        返回数组按键名排序
     * @return  array		查询结果集数组
     */
    public function getAll($field, $table, $where = "", $limit = "", $order = "", $group = "", $key = ""){
        $field = $this->check_args($field, "field");
        $table = $this->check_args($table, "table");
        $where = $this->check_args($where, "where");
        $limit = $this->check_args($limit, "limit");
        $order = $this->check_args($order, "order");
        $group = $this->check_args($group, "group");
        $sql = "SELECT " . $field . " FROM " . $table . $where . $group . $order . $limit;
        $this->execute($sql);
        $datalist = $this->fetch_array($key);
        $this->free_result();
        return $datalist;
    }

    /**
     * 获取单条记录查询
     * @param   string/array    $field      需要查询的字段值[例`name`,`gender`,`birthday`]
     * @param   string          $table      查询的表
     * @param   string/array    $where      查询条件
     *                          string      `name`='$name'
     *                          array       `name`=>array('$name1','$name2')    ==  `name` in ('$name1','$name2')
     *                          array       array($where,$where)                ==  $where or $where
     * @param   string/array    $order      排序方式[默认按数据库默认方式排序]
     * @param   string/array    $group      分组方式[默认为空]
     * @return  array/null	数据查询结果集,如果不存在，则返回空
     */
    public function getRow($field, $table, $where = "", $order = "", $group = ""){
        $field = $this->check_args($field, "field");
        $table = $this->check_args($table, "table");
        $where = $this->check_args($where, "where");
        $order = $this->check_args($order, "order");
        $group = $this->check_args($group, "group");
        $limit = " LIMIT 0,1";
        $sql = "SELECT " . $field . " FROM " . $table . $where . $group . $order . $limit;
        $this->execute($sql);
        $res = $this->fetch_next();
        $this->free_result();
        return $res;
    }

    /**
     * 获取首行首列
     * @param   string          $field      需要查询的字段值[例`name`,`gender`,`birthday`]
     * @param   string          $table      查询的表
     * @param   string/array    $where      查询条件
     *                          string      `name`='$name'
     *                          array       `name`=>array('$name1','$name2')    ==  `name` in ('$name1','$name2')
     *                          array       array($where,$where)                ==  $where or $where
     * @param   string/array    $order      排序方式[默认按数据库默认方式排序]
     * @param   string/array    $group      分组方式[默认为空]
     * @return  string          获取首行首列数据
     */
    public function getOne($field, $table, $where = "", $order = "", $group = ""){
        $res = $this->getRow($field, $table, $where, $order, $group);
        return @array_shift($res);
    }

    /**
     * 统计数量
     * @param   string          $table      查询的表
     * @param   string/array    $where      查询条件
     *                          string      `name`='$name'
     *                          array       `name`=>array('$name1','$name2')    ==  `name` in ('$name1','$name2')
     *                          array       array($where,$where)                ==  $where or $where
     * @return  int             统计数量
     */
    public function getCount($table, $where = ""){
        $res = $this->getRow("COUNT(*) AS num", $table, $where);
        return $res["num"];
    }

    /**
     * 执行sql查询
     * @param   string  $sql    执行SQL语句
     * @param   string  $key    指定下标
     * @return  array/null
     */
    public function select($sql, $key = ""){
        $this->execute($sql);
        $datalist = $this->fetch_array($key);
        $this->free_result();
        return $datalist;
    }

    /**
     * 执行添加记录操作
     * @param   string  $table              插入数据表
     * @param   array   $data               要增加的数据，参数为数组。数组key为字段值，数组值为数据取值
     * @param   bool    $return_insert_id   是否返回新建ID号
     * @param   bool    $replace            是否采用 replace into 的方式添加数据
     * @return  bool/insert_id              是否成功/插入ID号
     */
    public function insert($table, $data, $return_insert_id = true, $replace = false){
        if(empty($data) || !is_array($data) || empty($table)){
            return false;
        }
        $table = $this->check_args($table, "table");
        $field = $this->check_args(array_keys($data), "intofield");

        $valuedata = array_values($data);
        array_walk($valuedata, array($this, "escape_string"));

        $cmd = $replace ? "REPLACE INTO" : "INSERT INTO";
        $sql = $cmd . " " . $table . " " . $field . " VALUES (" . implode(",", $valuedata) . ")";
        $return = $this->execute($sql);
        $this->free_result();
        return $return_insert_id ? $this->insert_id() : $return;
    }

    /**
     * 执行添加多条记录
     * @param   string          $table      插入数据表
     * @param   string/array    $field      插入数据字段
     * @param   array           $data       要增加的数据，二维数组
     * @return  bool/int        返回影响的条数
     */
    public function inserts($table, $field, $data){
        if(!is_array($data)){
            return false;
        }
        $field = $this->check_args($field, "intofield");
        $table = $this->check_args($table, "table");
        $values = array();
        foreach($data as $row)
        {
            if($row){
                array_walk($row, array($this, "escape_string"));
                $values[] = "(" . implode(",", $row) . ")";
            }
        }
        $values = implode(",", $values);
        if(!$values){
            return false;
        }
        $sql = "INSERT INTO " . $table . " " . $field . " VALUES " . $values;
        $this->execute($sql);
        $this->free_result();
        return $this->affected_rows();
    }

    /**
     * 执行更新记录操作
     * @param   string          $table      更新数据表
     * @param   string/array    $data       要更新的数据内容，参数可以为数组也可以为字符串，建议数组。
     *                          string      `name`='',`hits`=`hits`+1
     *                          array       array('name'=>'','password'=>'123456')  ==  `name`='',`password`='123456'
     * 				array       array('name'=>'+=1', 'base'=>'-=1');    ==  `name`=`name`+1,`base`=`base`-`
     * @param   string/array    $where      更新数据条件 必须
     *                          string      `name`='$name'
     *                          array       `name`=>array('$name1','$name2')    ==  `name` in ('$name1','$name2')
     *                          array       array($where,$where)                ==  $where or $where
     * @return boolean
     */
    public function update($table, $data, $where){
        $table = $this->check_args($table, "table");
        $where = $this->check_args($where, "where");
        $update = $this->check_args($data, "update");
        if(empty($update)){
            return false;
        }
        $sql = "UPDATE " . $table . " SET " . $update . $where;
        $this->execute($sql);
        $this->free_result();
        return $this->affected_rows();
    }

    /**
     * 执行删除记录操作
     * @param   string          $table      删除数据表
     * @param   string/array    $where      删除数据条件 必须 如果要清空表，使用 $this->clear($table) 方法
     *                          string      `name`='$name'
     *                          array       `name`=>array('$name1','$name2')    ==  `name` in ('$name1','$name2')
     * @return  bool/int        删除状态/影响条数
     */
    public function delete($table, $where){
        $where = $this->check_args($where, "where");
        $table = $this->check_args($table, "table");
        $sql = "DELETE FROM " . $table . $where;
        $this->execute($sql);
        $this->free_result();
        return $this->affected_rows();
    }

    /**
     * 清空数据表
     * @param   string      $table      清空数据表
     * @return  bool/int    清空状态/影响条数
     */
    public function clear($table){
        $table = $this->check_args($table, "table");
        $sql = "TRUNCATE " . $table;
        $this->execute($sql);
        $this->free_result();
        return $this->affected_rows();
    }

    /**
     * 执行SQL操作
     * @param   string      $sql        执行SQL语句
     * @return  bool/int    影响条数
     */
    public function query($sql){
        $affected_rows = 0;
        if($sql){
            $this->execute($sql);
            $this->free_result();
            $affected_rows = $this->affected_rows();
        }
        return $affected_rows;
    }

    /**
     * 创建数据库
     * @param   string  $database   数据库名称
     * @param   string  $isUse      是否Use到当前数据库
     * @return  bool
     */
    public function createDB($database, $isUse = false){
        $sql = "CREATE DATABASE {$database}";
        $isCreate = $this->execute($sql);
        if($isCreate){
            if($isUse){
                $isCreate = $this->useDB($database);
            }
        }else{
            $this->halt("Can't create database {$database}");
        }
        return $isCreate;
    }

    /**
     * 数据库查询执行方法
     * @param   string      $sql        执行SQL语句
     * @return  resource    查询资源句柄
     */
    private function execute($sql){
        if(!is_resource($this->link)){
            $this->connect();
        }
        $this->lastQuery = $sql;
        $this->lastqueryid = mysql_query($sql, $this->link) or $this->halt(mysql_error(), $sql);
        $this->querycount++;
        return $this->lastqueryid;
    }

    /**
     * 获取最后一次添加记录的主键号
     * @return int
     */
    public function insert_id(){
        return mysql_insert_id($this->link);
    }

    /**
     * 遍历查询结果集
     * @param   $type       返回结果集类型
     *                      MYSQL_ASSOC，MYSQL_NUM 和 MYSQL_BOTH
     * @return  array       一维数组
     */
    public function fetch_next($type = MYSQL_ASSOC){
        return mysql_fetch_array($this->lastqueryid, $type);
    }

    /**
     * 返回查询结果集
     * @param   string  $key    下标
     * @return  array           二维数组
     */
    public function fetch_array($key = ""){
        if(!is_resource($this->lastqueryid)){
            return $this->lastqueryid;
        }
        $datalist = array();
        while($rs = $this->fetch_next())
        {
            if($key){
                $datalist[$rs[$key]] = $rs;
            }else{
                $datalist[] = $rs;
            }
        }
        return $datalist;
    }

    /**
     * 释放查询资源
     */
    public function free_result(){
        if(is_resource($this->lastqueryid)){
            mysql_free_result($this->lastqueryid);
            $this->lastqueryid = null;
        }
    }

    /**
     * 获取最后数据库操作影响到的条数
     * @return      int
     */
    public function affected_rows(){
        return mysql_affected_rows($this->link);
    }

    /**
     * 获取数据表主键
     * @param   string  $table  数据表
     * @return  array
     */
    public function get_primary($table){
        $primary = array();
        $table = $this->check_args($table, "table");
        $this->execute("SHOW COLUMNS FROM $table");
        while($r = $this->fetch_next())
        {
            if($r["Key"] == "PRI"){
                $primary[] = $r["Field"];
            }
        }
        return $primary;
    }

    /**
     * 获取表字段
     * @param   string  $table  数据表
     * @return  array
     */
    public function get_fields($table){
        $table = $this->check_args($table, "table");
        $fields = array();
        $this->execute("SHOW COLUMNS FROM $table");
        while($r = $this->fetch_next())
        {
            $fields[$r["Field"]] = $r["Type"];
        }
        return $fields;
    }

    /**
     * 检查不存在的字段
     * @param   string  $table  数据表
     * @param   array   $array
     * @return  array
     */
    public function check_fields($table, $array){
        $fields = $this->get_fields($table);
        $nofields = array();
        foreach($array as $v)
        {
            if(!array_key_exists($v, $fields)){
                $nofields[] = $v;
            }
        }
        return $nofields;
    }

    /**
     * 检查表是否存在
     * @param   string  $table  数据表
     * @return  boolean
     */
    public function table_exists($table){
        $tables = $this->list_tables();
        return in_array($table, $tables);
    }

    /**
     * 列出所有表
     */
    public function list_tables(){
        $tables = array();
        $this->execute("SHOW TABLES");
        while($r = $this->fetch_next())
        {
            $tables[] = $r["Tables_in_" . $this->setting["database"]];
        }
        return $tables;
    }

    /**
     * 检查字段是否存在
     * @param $table 表名
     * @return boolean
     */
    public function field_exists($table, $field){
        $fields = $this->get_fields($table);
        return array_key_exists($field, $fields);
    }

    //返回查询数据的行数，用于select
    //自我感觉，些函数没多大意义，即使是使用分页，速度也不行
    public function num_rows($sql){
        $this->lastqueryid = $this->execute($sql);
        return mysql_num_rows($this->lastqueryid);
    }

    //返回结果集，字段的数目
    public function num_fields($sql){
        $this->lastqueryid = $this->execute($sql);
        return mysql_num_fields($this->lastqueryid);
    }

    public function result($sql, $row){
        $this->lastqueryid = $this->execute($sql);
        return @mysql_result($this->lastqueryid, $row);
    }

    public function error(){
        return @mysql_error($this->link);
    }

    public function errno(){
        return intval(@mysql_errno($this->link));
    }

    public function version(){
        if(!is_resource($this->link)){
            $this->connect();
        }
        return mysql_get_server_info($this->link);
    }

    public function close(){
        if(is_resource($this->link)){
            @mysql_close($this->link);
            $this->link = null;
        }
    }

    public function halt($message = "", $sql = ""){
        $table = "`" . $this->setting["database"] . "`.`" . $this->setting["tablepre"];
        $replace = "`TABLE`.`PRE";
        $sql = str_replace($table, $replace, $sql);
        trigger_error($message . " Query:" . $sql, $this->errno());
        //throw new Exception($message . " Query:" . $sql, );
        return false;
    }

    /**
     * 对字段两边加反引号，以保证数据库安全
     * @param $value 数组值
     */
    public function add_special_char(&$value){
        if(preg_match("/^([a-zA-Z0-9_]*)$/", $value)){
            $q = "`";
        }
        $value = $q . new_addslashes($value) . $q;
        return $value;
    }

    /**
     * 对字段值两边加引号，以保证数据库安全
     * @param $value 数组值
     * @param $key 数组key
     * @param $quotation
     */
    public function escape_string(&$value){
        if(!is_numeric($value)){
            $q = "'";
        }
        $value = $q . new_addslashes($value) . $q;
        return $value;
    }
}
?>