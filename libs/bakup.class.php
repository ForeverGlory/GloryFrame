<?php
/**
 * bakup.class      数据库备份类
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/libs/bakup.class.php
 * @version         $
 */
final class bakup extends database
{
    protected $db        = '';
    protected $sizelimit = 2048;
    protected $offset    = 100;

    public function __construct($setting = '')
    {
       GloryFrame::Auto($this);
        $this->openDB($setting);
    }

    /**
     * 备份数据库，返回备份语句
     */
    public function dumptable($table, $startfrom = 0)
    {
        $offset = 100;
        if(!$startfrom)
        {
            $tabledump   = "DROP TABLE IF EXISTS $table;\n";
            $createtable = $this->db->query("SHOW CREATE TABLE $table");
            $create      = $db->fetch_row($createtable);
            $tabledump .= $create[1] . ";\n\n";
        }
        $tabledumped = 0;
        $numrows     = $offset;
        while($currsize + strlen($tabledump) < $sizelimit * 1000 && $numrows == $offset)
        {
            $tabledumped = 1;
            $rows        = $db->query("SELECT * FROM $table LIMIT $startfrom, $offset");
            $numfields   = $db->num_fields($rows);
            $numrows     = $db->num_rows($rows);
            while($row         = $db->fetch_row($rows))
            {
                $comma = "";
                $tabledump .= "INSERT INTO $table VALUES(";
                for($i     = 0; $i < $numfields; $i++)
                {
                    $tabledump .= $comma . "'" . mysql_escape_string($row[$i]) . "'";
                    $comma    = ",";
                }
                $tabledump .= ");\n";
            }
            $startfrom += $offset;
        }
        $startrow = $startfrom;
        $tabledump .= "\n";
        return $tabledump;
    }

}
