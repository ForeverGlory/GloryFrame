<?php

/**
 *  model.class.php 数据模型基类
 *
 * @author			foreverglory@qq.com
 * @license			http://ys20.cn
 * @lastmodify		2011-4-22
 **/
ys_base::load_sys_class('db_factory', '', 0);
class model
{

    //数据库配置
    protected $db_config = '';
    //数据库连接
    protected $db = '';
    //调用数据库的配置项
    protected $db_setting = 'default';
    //数据表名
    protected $table_name = '';
    //表前缀
    public $db_tablepre = '';

    public function __construct()
    {
        if (!isset($this->db_config[$this->db_setting]))
        {
            $this->db_setting = 'default';
        }
        $this->db_tablepre = $this->db_config[$this->db_setting]['tablepre'];
        $this->db = db_factory::get_instance($this->db_config)->get_database($this->db_setting);
    }

    /**
     * 执行sql查询
     * @param $data 		需要查询的字段值[例`name`,`gender`,`birthday`]
     * @param $where 		查询条件[例`name`='$name']
     * @param $limit 		返回结果范围[例：10或10,10 默认为空]
     * @param $order 		排序方式	[默认按数据库默认方式排序]
     * @param $group 		分组方式	[默认为空]
     * @param $key          返回数组按键名排序
     * @return array		查询结果集数组
     */
    final public function select($data, $table, $where = '', $limit = '', $order = '', $group = '', $key = '')
    {
        if (is_array($where))
            $where = $this->sqls($where);
        if (is_array($table))
        {
            foreach ($table as $n => $v)
            {
                $table[$n] = $this->db_tablepre . $v;
            }
        }
        else
        {
            $table = $this->db_tablepre . $table;
        }
        return $this->db->select($data, $table, $where, $limit, $order, $group, $key);
    }

    /**
     * 获取单条记录查询
     * @param $where 		查询条件
     * @param $data 		需要查询的字段值[例`name`,`gender`,`birthday`]
     * @param $order 		排序方式	[默认按数据库默认方式排序]
     * @param $group 		分组方式	[默认为空]
     * @return array/null	数据查询结果集,如果不存在，则返回空
     */
    final public function get_one($data, $table, $where = '', $order = '', $group = '')
    {
        if (is_array($where))
            $where = $this->sqls($where);
        return $this->db->get_one($data, $this->db_tablepre . $table, $where, $order, $group);
    }

    /**
     * 直接执行sql查询
     * @param $sql							查询sql语句
     * @return	boolean/query resource		如果为查询语句，返回资源句柄，否则返回true/false
     */
    final public function query($sql, $key = '')
    {
        return $this->db->query($sql, $key);
        //return
    }

    /**
     * 执行添加记录操作
     * @param $data 		要增加的数据，参数为数组。数组key为字段值，数组值为数据取值
     * @param $table        插入的表
     * @param $return_insert_id 是否返回新建ID号
     * @param $replace 是否采用 replace into的方式添加数据
     * @return boolean
     */
    final public function insert($data, $table, $return_insert_id = true, $replace = false)
    {
        return $this->db->insert($data, $this->db_tablepre . $table, $return_insert_id, $replace);
    }

    /**
     * 获取最后一次添加记录的主键号
     * @return int
     */
    final public function insert_id()
    {
        return $this->db->insert_id();
    }

    /**
     * 执行更新记录操作
     * @param $data 		要更新的数据内容，参数可以为数组也可以为字符串，建议数组。
     * 						为数组时数组key为字段值，数组值为数据取值
     * 						为字符串时[例：`name`='phpcms',`hits`=`hits`+1]。
     *						为数组时[例: array('name'=>'phpcms','password'=>'123456')]
     *						数组的另一种使用array('name'=>'+=1', 'base'=>'-=1');程序会自动解析为`name` = `name` + 1, `base` = `base` - 1
     * @param $where 		更新数据时的条件,可为数组或字符串
     * @return boolean
     */
    final public function update($data, $table, $where = '')
    {
        if (is_array($where))
            $where = $this->sqls($where);
        return $this->db->update($data, $this->db_tablepre . $table, $where);
    }

    /**
     * 执行删除记录操作
     * @param $where 		删除数据条件,不充许为空。
     * @return boolean
     */
    final public function delete($table, $where)
    {
        if (is_array($where))
            $where = $this->sqls($where);
        return $this->db->delete($this->db_tablepre . $table, $where);
    }

    /**
     * 计算记录数
     * @param string/array $where 查询条件
     */
    final public function count($table, $where = '')
    {
        if (is_array($where))
            $where = $this->sqls($where);
        $r = $this->get_one("COUNT(*) AS num", $table, $where);
        return $r['num'];
    }
    /**
     * 获取首行首列
     **/
    final public function get_single($data,$table,$where,$order=''){
        $r=$this->get_one($data,$table,$where,$order);
        return $r[$data];
    }
    /**
     * 将数组转换为SQL语句
     * @param array $where 要生成的数组
     * @param string $font 连接串。
     */
    final public function sqls($where, $font = ' AND ')
    {
        if (is_array($where))
        {
            $sql = '';
            foreach ($where as $key => $val)
            {
                if (substr_count($val, "not in"))
                {
                    $sql .= $sql ? " $font `$key` $val " : " `$key` $val";
                } elseif (substr_count($val, "in("))
                {
                    $sql .= $sql ? " $font `$key` $val " : " `$key` $val";
                }
                else
                {
                    $sql .= $sql ? " $font `$key` = '$val' " : " `$key` = '$val'";
                }
            }
            return $sql;
        }
        else
        {
            return $where;
        }
    }

    /**
     * 获取最后数据库操作影响到的条数
     * @return int
     */
    final public function affected_rows()
    {
        return $this->db->affected_rows();
    }

    /**
     * 获取数据表主键
     * @return array
     */
    final public function get_primary($table)
    {
        return $this->db->get_primary($this->db_tablepre . $table);
    }

    /**
     * 获取表字段
     * @param string $table_name    表名
     * @return array
     */
    final public function get_fields($table)
    {
        if (empty($table_name))
        {
            return false;
        }
        return $this->db->get_fields($this->db_tablepre . $table);
    }

    /**
     * 检查表是否存在
     * @param $table 表名
     * @return boolean
     */
    final public function table_exists($table)
    {
        return $this->db->table_exists($this->db_tablepre . $table);
    }

    /**
     * 检查字段是否存在
     * @param $field 字段名
     * @return boolean
     */
    public function field_exists($field, $table)
    {
        $fields = $this->db->get_fields($table);
        return array_key_exists($field, $fields);
    }

    final public function list_tables()
    {
        return $this->db->list_tables();
    }
    /**
     * 返回数据结果集
     * @param $query （mysql_query返回值）
     * @return array
     */
    final public function fetch_array()
    {
        $data = array();
        while ($r = $this->db->fetch_next())
        {
            $data[] = $r;
        }
        return $data;
    }

    /**
     * 返回数据库版本号
     */
    final public function version()
    {
        return $this->db->version();
    }
    /**
     * 获取查询的次数
     **/
    final public function querycount(){
        return $this->db->querycount;
    }
}
