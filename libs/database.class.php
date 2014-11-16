<?php
/**
 * database.class.php    数据模型基类
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/libs/database.class.php
 * @version         $
 */
class database{

    /**
     * 数据库连接
     */
    protected $db = "";

    public function __construct($setting = ""){
        GloryFrame::Auto($this);
        $this->openDB($setting);
    }

    /**
     * 判断没有的函数，自定转向到db里
     */
    final public function __call($method, $arg_array){
        $method = array($this->db, $method);
        return call_user_func_array($method, $arg_array);
    }

    /**
     * 数据库连接
     * @param $config 	数据库配置 string 配置文件里 array 直接配置
     * @return object
     */
    final public function openDB($setting = ""){
        if(is_string($setting) || empty($setting)){
            $setting = $this->config->load("database", array($setting));
        }
        return $this->db = $this->load->lib($setting["type"], $setting, 1, "database");
    }

    public function connect($setting = array()){
        return $this->db->connect($setting);
    }

    /**
     * 选择数据库
     * @param   string  $database   数据库名称
     */
    final public function useDB($database, $isCreate = false){
        return $this->db->useDB($database, $isCreate);
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
    final public function getAll($field, $table, $where = "", $limit = "", $order = "", $group = "", $key = ""){
        return $this->db->getAll($field, $table, $where, $limit, $order, $group, $key);
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
    final public function getRow($field, $table, $where = "", $order = "", $group = ""){
        return $this->db->getRow($field, $table, $where, $order, $group);
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
    final public function getOne($field, $table, $where, $order = ""){
        return $this->db->getOne($field, $table, $where, $order);
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
    final public function getCount($table, $where = ""){
        return $this->db->getCount($table, $where);
    }

    /**
     * 执行sql查询
     * @param   string  $sql    执行SQL语句
     * @param   string  $key    指定下标
     * @return  array/null
     */
    final public function select($sql, $key = ""){
        return $this->db->select($sql, $key);
    }

    /**
     * 执行添加记录操作
     * @param   string  $table              插入数据表
     * @param   array   $data               要增加的数据，参数为数组。数组key为字段值，数组值为数据取值
     * @param   bool    $return_insert_id   是否返回新建ID号
     * @param   bool    $replace            是否采用 replace into 的方式添加数据
     * @return  bool/insert_id              是否成功/插入ID号
     */
    final public function insert($table, $data, $return_insert_id = true, $replace = false){
        return $this->db->insert($table, $data, $return_insert_id, $replace);
    }

    /**
     * 执行添加多条记录
     * @param   string          $table      插入数据表
     * @param   string/array    $field      插入数据字段
     * @param   array           $data       要增加的数据，二维数组
     * @return  bool/int        返回影响的条数
     */
    final public function inserts($table, $field, $data){
        return $this->db->inserts($table, $field, $data);
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
    final public function update($table, $data, $where = ""){
        return $this->db->update($table, $data, $where);
    }

    /**
     * 执行删除记录操作
     * @param   string          $table      删除数据表
     * @param   string/array    $where      删除数据条件 必须 如果要清空表，使用 $this->clear($table) 方法
     *                          string      `name`='$name'
     *                          array       `name`=>array('$name1','$name2')    ==  `name` in ('$name1','$name2')
     * @return  bool/int        删除状态/影响条数
     */
    final public function delete($table, $where){
        return $this->db->delete($table, $where);
    }

    /**
     * 清空数据表
     * @param   string      $table      清空数据表
     * @return  bool/int    清空状态/影响条数
     */
    final public function clear($table){
        return $this->db->clear($table);
    }

    /**
     * 执行SQL操作
     * @param   string      $sql        执行SQL语句
     * @return  bool/int    影响条数
     */
    final public function query($sql){
        return $this->db->query($sql);
    }

    /**
     * 执行SQL文件
     * @param   string  $sqlFile    SQL文件
     * @return  bool/int    影响条数
     */
    final public function file($sqlFile){
        $affected_rows = 0;
        if(defined("IN_FR")){
            $data = $this->file->read($sqlFile);
        }else{
            $data = file_get_contents($sqlFile);
        }
        $sqls = explode(";", $data);
        foreach($sqls as $sql)
        {
            $affected_rows += intval($this->query(trim($sql)));
        }
        return $affected_rows;
    }

    /**
     * 获取最后一次添加记录的主键号
     * @return int
     */
    final public function insert_id(){
        return $this->db->insert_id();
    }

    /**
     * 获取最后数据库操作影响到的条数
     * @return      int
     */
    final public function affected_rows(){
        return $this->db->affected_rows();
    }

    /**
     * 获取数据表主键
     * @param   string  $table  数据表
     * @return  array
     */
    final public function get_primary($table){
        return $this->db->get_primary($table);
    }

    /**
     * 获取表字段
     * @param   string  $table  数据表
     * @return  array
     */
    final public function get_fields($table){
        return $this->db->get_fields($table);
    }

    /**
     * 检查表是否存在
     * @param   string  $table  数据表
     * @return  boolean
     */
    final public function table_exists($table){
        return $this->db->table_exists($table);
    }

    /**
     * 检查字段是否存在
     * @param $field 字段名
     * @return boolean
     */
    public function field_exists($field, $table){
        $fields = $this->get_fields($table);
        return array_key_exists($field, $fields);
    }

    /**
     * 列出所有表
     */
    final public function list_tables(){
        return $this->db->list_tables();
    }

    /**
     * 返回数据结果集
     * @return array/null
     */
    final public function fetch_array(){
        return $this->db->fetch_array();
    }

    /**
     * 返回数据库版本号
     */
    final public function version(){
        return $this->db->version();
    }

    /**
     * 最近一次执行的语句
     * @return  string
     */
    final public function lastQuery(){
        return $this->db->lastQuery;
    }

    /**
     * 获取查询的次数
     */
    final public function querycount(){
        return $this->db->querycount;
    }
}
