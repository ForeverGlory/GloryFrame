<?php
/**
 * common.func.php  基础函数
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/cores/common.func.php
 * @version         $
 */

/**
 * 返回数字的二进制集合
 * @param   int     $int
 * @return  array
 */
function binMuster($int){
    $array = array();
    $bin = array(
        0  => 1, 1  => 2, 2  => 4, 3  => 8, 4  => 16, 5  => 32, 6  => 64, 7  => 128, 8  => 256, 9  => 512, 10 => 1024, 11 => 2048, 12 => 4096, 13 => 8192, 14 => 16384, 15 => 32768, 16 => 65536
    );
    for($i = 0; $i <= 16; $i++)
    {
        if($int < $bin[$i]){
            for($j = $i - 1; $j >= 0; $j--)
            {
                if($int >= $bin[$j]){
                    $int -= $bin[$j];
                    $array[] = $bin[$j];
                }
            }
            break;
        }
    }
    return $array;
}

function is_json($str, &$json = null){
    $isJson = false;
    if(strlen($str)){
        $json = json_decode($str, true);
        if(is_array($json)){
            $isJson = true;
        }
    }
    return $isJson;
}
?>