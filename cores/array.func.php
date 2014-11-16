<?php
/**
 * array.func.php   数组相关函数
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/cores/array.func.php
 * @version         $
 */

/**
 * 数组合并，重写 array_merge
 * @param   array       $array1     第一个数组
 * @param   array       $array2     第二个数组，覆盖第一个数组
 * @param   str/array   $keep       保留字段，不被替换
 * @return  array
 */
function arrayMerge($array1 = array(), $array2 = array(), $keep = null){
    $array1 = (array)$array1;
    $array2 = (array)$array2;
    $keep = strToArray(",", $keep);
    foreach($array1 as $k => $v)
    {
        //存在，并不在保留字段
        if(array_key_exists($k, $array2) && !in_array($k, $keep)){
            if(is_array($array2[$k])){
                $array1[$k] = arrayMerge($array1[$k], $array2[$k]);
            }else{
                $array1[$k] = $array2[$k];
            }
        }
        unset($array2[$k]);
    }
    foreach($array2 as $k => $v)
    {
        $array1[$k] = $v;
    }
    return $array1;
}

/**
 * StringToArray    重写    explode
 * @param   string  $delimiter  规定在哪里分割字符串
 * @param   string  $string     要分割的字符串
 * @param   int     $limit      规定所返回的数组元素的最大数目
 * @return  array
 */
function strToArray($delimiter, $string, $limit = NULL){
    if(!is_array($string)){
        $array = array();
        $string = trim(strval($string));
        if($string){
            if(empty($limit)){
                $array = explode($delimiter, $string);
            }else{
                $array = explode($delimiter, $string, $limit);
            }
        }
    }else{
        $array = $string;
    }
    return $array;
}

/**
 * ArrayToString    重写implode
 * @param   string  $glue
 * @param   string  $pieces
 * @return  string
 */
function arrayToStr($glue, $pieces){
    if(is_array($pieces)){
        if(empty($pieces)){
            $string = "";
        }else{
            $string = implode($glue, $pieces);
        }
    }else{
        $string = strval($pieces);
    }
    return $string;
}

/**
 * 取数组第一个值 防止传递指针
 * @param   array   $array
 * @return  mixed
 */
function arrayFirst($array){
    return array_shift($array);
}

/**
 * 取数组第一个值的 Key
 * @param   array   $array
 * @return  string
 */
function arrayFirstKey($array){
    foreach($array as $k => $v)
    {
        return $k;
    }
}

/**
 * 取数组最后一个值 防止传递指针
 * @param   array   $array
 * @return  mixed
 */
function arrayEnd($array){
    return array_pop($array);
}

/**
 * 取数组的某一个值
 * @param   array   $array
 * @param   int     $offset     位置
 * @return  mixed
 */
function arrayGet($array, $offset){
    if($offset >= 0){
        $return = array_slice($array, $offset, 1);
        return $return[0];
    }
}

/**
 * 验证Key是否存在，多key
 * @param   string/array    $key
 * @param   array           $search
 * @return  bool
 */
function arrayKeyExists($key, $search){
    $exists = true;
    $key = strToArray(",", $key);
    foreach($key as $k)
    {
        if(!array_key_exists($k, $search)){
            $exists = false;
            break;
        }
    }
    return $exists;
}

/**
 * 数组递归取值
 * @param   array               $array
 * @param   null/string/array   $key
 * @return  mixed
 */
function arrayGetRecursion($array, $key = null){
    $return = $array;
    if(!is_null($key)){
        if(is_array($array)){
            if(is_array($key)){
                foreach($key as $v)
                {
                    $return = empty($v) ? arrayFirst($return) : $return[$v];
                }
            }else{
                $return = empty($key) ? arrayFirst($return) : $return[$key];
            }
        }else{
            $return = null;
        }
    }
    return $return;
}

/**
 * 移除相同的数据
 * 数组2跟数组1比较，如果键值相同，数组2中移除，返回数组2
 * 用于修改数据，拿数据库里数据跟页面提交的数据比较，只剩下改动过的数据
 * @param   array       $array1     对比数组
 * @param   array       $array2     保留数组
 * @param   str/array   $keep       需要保留字段
 * @return  array
 */
function arrayDelSame($array1, $array2, $keep = null){
    if(!is_array($array1) || !is_array($array2)){
        return $array2;
    }
    $keep = strToArray(",", $keep);
    foreach($array2 as $key => $val)
    {
        if($val === $array1[$key]){
            if(empty($keep) || !in_array($key, $keep)){
                unset($array2[$key]);
            }
        }
    }
    return $array2;
}

/**
 * 数组添加数组到集合中
 * @param   array   $array
 * @param   array   $addArray
 * @return  array
 */
function arrayAddArray($array, $addArray){
    foreach($addArray as $one)
    {
        $array[] = $one;
    }
    return $array;
}

/**
 * 验证数据，移除多余列
 * @param   array           $data       数据
 * @param   array           $check      限制列
 * @param   array/string    $isset      检查列是否存在 isset 有值，但$data里没该列，则返回false
 * @return  array/false
 */
function arrayCheckKey($data, $check, $isset = null){
    if(empty($data) || !is_array($data) || empty($check)){
        return false;
    }
    $array = array();
    $check = strToArray(",", $check);
    $isset = strToArray(",", $isset);
    foreach($isset as $one)
    {
        if(!array_key_exists($one, $data)){
            return false;
        }
    }
    foreach($data as $k => $v)
    {
        if(in_array($k, $check)){
            $array[$k] = $v;
        }
    }
    return $array;
}

/**
 * 删除数组指定的列
 * @param   array           $data   数组
 * @param   array/string    $check  指定要删除的列
 * @return  array           删除后的数组
 */
function arrayCheckDel($data, $check){
    if(empty($data) || !is_array($data) || empty($check)){
        return false;
    }
    $check = strToArray(",", $check);
    foreach($check as $key)
    {
        unset($data[$key]);
    }
    return $data;
}

/**
 * 获取数据中，其中一个值
 * @param   array   $data   原数据
 * @param   array   $check  获取其中的一个值
 * @return  array
 */
function arrayGetOne($data, $check){
    $return = array();
    foreach($check as $v)
    {
        if(!empty($data[$v])){
            $return[$v] = $data[$v];
            break;
        }
    }
    return $return;
}

/**
 * 数组去空去重复
 */
function arrayUnique($array = array()){
    if(!is_array($array)){
        if($array){
            return array($array);
        }else{
            return array();
        }
    }else{
        return array_unique(array_filter($array));
    }
}

/**
 * 将字符串转换为数组 此函数有风险，使用需谨慎
 *
 * @param	string	$data	字符串
 * @return	array	返回数组格式，如果，data为空，则返回空数组
 */
function string2array($data){
    if(empty($data)){
        return array();
    }
    eval("\$array = $data;");
    return $array;
}

/**
 * 将数组转换为字符串
 *
 * @param	array	$data		数组
 * @param	bool	$isformdata	如果为0，则不使用new_stripslashes处理，可选参数，默认为1
 * @return	string	返回字符串，如果，data为空，则返回空
 */
function array2string($data, $isformdata = 1){
    if(empty($data)){
        return '';
    }
    if($isformdata){
        $data = new_stripslashes($data);
    }
    return addslashes(var_export($data, true));
}

/**
 * 多级key赋值
 * @param   array   $data   $data[$k1][$2][$3]
 * @param   array   $keys   array($k1,$k2,$k3...)
 * @param   type    $val
 * @return  array
 */
function arrayKeysAssign($data, $keys, $val){
    $array = array();
    $count = count($keys);
    for($i = $count - 1; $i >= 0; $i--)
    {
        $array = array(trim($keys[$i]) => empty($array) ? trim($val) : $array);
    }
    return empty($data) ? $array : arrayMerge($data, $array);
}

/**
 * 将二维转换一维
 * @param   array   $array  二维或多维数组
 * @param   string  $key    取下标值
 */
function arrayConvertOne($array, $key){
    $convert = array();
    foreach($array as $k => $v)
    {
        $convert[$k] = $v[$key];
    }
    return $convert;
}

/**
 * 数组转换ini配置字符串
 * @param   array   $array
 * @return  string
 */
function arrayToIni($array){
    $ini_str = "";
    foreach($array as $key => $val)
    {
        if(is_array($val)){
            $ini_str .= "[{$key}]\n\n";
            foreach($val as $k => $v)
            {
                $ini_str .= "{$k} = {$v}\n";
            }
        }else{
            $ini_str .= "{$key} = {$val}\n\n";
        }
    }
    return $ini_str;
}
?>