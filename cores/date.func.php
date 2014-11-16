<?php

/**
 * date.func.php    时间相关函数
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/cores/date.func.php
 * @version         $
 */
function myDate($format, $timestamp = null)
{
    $date = "";
    if(is_null($timestamp))
    {
        $date = date($format);
    }
    elseif($timestamp > 0)
    {
        $date = date($format, $timestamp);
    }
    return $date;
}

/**
 * 获取时间戳,超过十位，使用微秒补
 * @param   int     $leng       时间戳长度
 * @return  long    $micro      补齐的时间戳
 */
function getMicro($leng = 13, $micro = 0)
{
    if($leng <= 10)
    {
        return time();
    }
    if(empty($micro))
    {
        $micro = microtime();
    }
    $time  = explode(' ', $micro);
    return floatval($time[1] . substr($time[0], 2, $leng - 10));
}

/**
 * 将微妙秒时间戳格式化
 * @param   string  $string     格式化类型 micro[1] 表示微妙的格式
 * @return  string  formatData
 */
function formatMicro($format = '', $micro = 0)
{
    if(empty($format))
    {
        $format = "Y-m-d H:i:s.micro[6]";
    }
    if(empty($micro))
    {
        $micro = microtime();
    }
    list($t2, $t1) = explode(" ", $micro);

    preg_match("/micro\[([0-9]+)\]/", $format, $matches);
    if(!empty($matches[1]))
    {
        $format = str_replace("micro[$matches[1]]", substr($t2, 2, $matches[1]), $format);
    }
    return date($format, $t1);
}

/**
 * 两个时间毫秒间隔
 * @param   microtime   $start      开始时间
 * @param   microtime   $end        结束时间，默认为当前时间
 * @return  float       返回时间
 */
function compareMicro($start, $end = 0)
{
    if(empty($start))
    {
        return 0;
    }
    if(empty($end))
    {
        $end   = microtime();
    }
    $stime = explode(" ", $start);
    $etime = explode(" ", $end);
    return number_format(($etime[0] + $etime[1] - $stime[0] - $stime[1]), 6);
}

/**
 * 两时间转换距离格式
 */
function dateDist($starttime, $endtime = 0)
{
    if(is_numeric($starttime) && $starttime > 0)
    {

    }
    else
    {
        $starttime = strtotime($starttime);
    }
    if(is_numeric($endtime) && $endtime > 0)
    {

    }
    else
    {
        $endtime  = strtotime($endtime);
    }
    $disttime = $endtime - $starttime;
    return formatDate($disttime);
}

/**
 * 将时间戳格式化
 */
function formatDate($disttime, $trad = false)
{
    $sec  = $disttime % 60;
    $min  = intval($disttime / 60 % 60);
    $hour = intval($disttime / 60 / 60 % 24);
    $day  = intval($disttime / 60 / 60 / 24);
    if($day)
    {
        if(!$min)
        {
            $str = $day . "天" . $hour . "时";
        }
        else
        {
            $str = $day . "天" . $hour . "时" . $min . "分";
        }
    }
    else
    {
        if($hour)
        {
            $str = $hour . "时";
            if(!$sec)
            {
                if($min)
                {
                    $str .= $min . "分";
                }
                elseif($trad)
                {
                    $str = $hour . "小时";
                }
            }
            else
            {
                $str .= $min . "分" . $sec . "秒";
            }
        }
        else
        {
            if($sec)
            {
                $str = $min . "分" . $sec . "秒";
            }
            else
            {
                if($trad)
                {
                    $str = $min . "分钟";
                }
                else
                {
                    $str = $min . "分";
                }
            }
        }
    }
    return $str;
}

function dateToStr($str)
{
    $search = array("H", "D", "W", "M", "Y");
    $replace = array("时", "天", "周", "月", "年");
    $str = str_replace($search, $replace, $str, $isReplace);
    if(!$isReplace)
    {
        $str.="时";
    }
    return $str;
}

/**
 * 计算时间 返回秒
 * @param   string  $str
 * @return  int
 */
function calcStrData($str)
{
    $time  = 0;
    $count = preg_match_all("/(\d+)([a-zA-Z]*)/", $str, $matches);
    for($i     = 0; $i < $count; $i++)
    {
        switch($matches[2][$i])
        {
            case "H":
                $time += $matches[1][$i] * 3600;
                break;
            case "D":
                $time += $matches[1][$i] * 3600 * 24;
                break;
            case "M":
                $time += $matches[1][$i] * 3600 * 24 * idate("t");
                break;
            case "Y":
                $time += $matches[1][$i] * 3600 * 24 * (idate("L") && idate("z") < 60 ? 366 : 365);
                break;
            default:
                $time += $matches[1][$i];
                break;
        }
    }
    return $time;
}

?>
