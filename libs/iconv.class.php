<?php
/**
 * iconv.class.php  字符转换类
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/libs/iconv.class.php
 * @version         $
 */
class iconv
{

    /**
     * 初始化字符转换类
     */
    public function __construct()
    {
        static $isFirst = true;
        if($isFirst)
        {
           GloryFrame::Auto($this);
            if(function_exists("iconv_set_encoding"))
            {
                iconv_set_encoding("input_encoding", $this->_var["charset"]);
                iconv_set_encoding("internal_encoding", $this->_var["charset"]);
                iconv_set_encoding("output_encoding", $this->_var["charset"]);
            }
            $isFirst = false;
        }
    }

    /**
     * 字符转换
     * @param   string/array    $str                转换字符/数组
     * @param   string          $to_encoding        转换后字符
     * @param   string          $from_encoding      转换前字符
     * @return  string/array
     */
    public function iconv($str, $to_encoding, $from_encoding = null)
    {
        if(!is_array($str))
        {
            if(function_exists("mb_convert_encoding"))
            {
                $str = mb_convert_encoding($str, $to_encoding, $from_encoding);
            }
            else
            {
                $str = iconv($from_encoding, $to_encoding, $str);
            }
        }
        else
        {
            foreach($str as $k => $v)
            {
                $str[$k] = $this->iconv($v, $to_encoding, $from_encoding);
            }
        }
        return $str;
    }

    ////////////////////////////////////////////////////////////////////////////////////////
    /*  To  utf-8                                                                         */
    ////////////////////////////////////////////////////////////////////////////////////////
    /**
     * 任意字符转utf8
     * @param   string  $str
     */
    public function to_utf8($str)
    {
        //todo 任意字符转utf8
    }

    /**
     * gbk转utf8
     * @param   string  $gbstr
     */
    public function gbk_to_utf8($gbstr)
    {
        return $this->iconv($gbstr, "utf-8", "gbk");
    }

    /**
     * big5转utf8
     * @param   string  $str
     */
    public function big5_to_utf8($str)
    {
        return $this->iconv($str, "utf-8", "big5");
    }

    /**
     * unicode转utf8
     * @param  string   $c
     */
    public function unicode_to_utf8($c)
    {
        $str = '';
        if($c < 0x80)
        {
            $str .= $c;
        }
        elseif($c < 0x800)
        {
            $str .= (0xC0 | $c >> 6);
            $str .= (0x80 | $c & 0x3F);
        }
        elseif($c < 0x10000)
        {
            $str .= (0xE0 | $c >> 12);
            $str .= (0x80 | $c >> 6 & 0x3F);
            $str .= (0x80 | $c & 0x3F);
        }
        elseif($c < 0x200000)
        {
            $str .= (0xF0 | $c >> 18);
            $str .= (0x80 | $c >> 12 & 0x3F);
            $str .= (0x80 | $c >> 6 & 0x3F);
            $str .= (0x80 | $c & 0x3F);
        }
        return $str;
    }

    ////////////////////////////////////////////////////////////////////////////////////////
    /*  To  gbk                                                                           */
    ////////////////////////////////////////////////////////////////////////////////////////
    /**
     * 任意字符转gbk
     * @param   string  $str
     */
    public function to_gbk($str)
    {
        //todo 任意字符转gbk
    }

    /**
     * utf8转gbk
     * @param   $utfstr
     */
    public function utf8_to_gbk($utfstr)
    {
        return $this->iconv($utfstr, "gbk", "utf-8");
    }

    /**
     * 繁体转简体
     * @param   $str
     */
    public function big5_to_gbk($str)
    {
        return $this->iconv($str, "gbk", "big5");
    }

    /**
     * unicode转gbk
     * @param  string   $c
     */
    public function unicode_to_gbk($c)
    {
        //todo unicode转gbk
    }

    ////////////////////////////////////////////////////////////////////////////////////////
    /*  To  big5                                                                          */
    ////////////////////////////////////////////////////////////////////////////////////////
    /**
     * 任意字符转big5
     * @param   string  $str
     */
    public function to_big5($str)
    {
        //todo 任意字符转big5
    }

    /**
     * 简体转繁体
     * @param   $str
     */
    public function gbk_to_big5($str)
    {
        return $this->iconv($str, "gbk", "big5");
    }

    /**
     * utf8转unicode
     * @param  $c
     */
    public function utf8_to_unicode($c)
    {
        switch(strlen($c))
        {
            case 1:
                $n = ord($c);
                break;
            case 2:
                $n = (ord($c[0]) & 0x3f) << 6;
                $n += ord($c[1]) & 0x3f;
                break;
            case 3:
                $n = (ord($c[0]) & 0x1f) << 12;
                $n += (ord($c[1]) & 0x3f) << 6;
                $n += ord($c[2]) & 0x3f;
                break;
            case 4:
                $n = (ord($c[0]) & 0x0f) << 18;
                $n += (ord($c[1]) & 0x3f) << 12;
                $n += (ord($c[2]) & 0x3f) << 6;
                $n += ord($c[3]) & 0x3f;
                break;
        }
        return $n;
    }

    /**
     * Ascii转拼音
     * @param   $asc
     * @param   $pyarr
     */
    public function asc_to_pinyin($asc, &$pyarr)
    {
        static $table = array();
        if(empty($table))
        {
            $filepath = $this->_var["syspath"]["data"] . "encoding" . DIRECTORY_SEPARATOR . "gb-pinyin.table";
            $table    = $this->file->read($filepath, "table", false, array("cut"  => "-", "turn" => 1));
            ksort($table);
        }
        if($asc < 128)
        {
            return chr($asc);
        }
        elseif(isset($table[$asc]))
        {
            return $table[$asc];
        }
        else
        {
            foreach($table as $id => $p)
            {
                if($id >= $asc)
                    return $p;
            }
        }
    }

    /**
     * gbk转拼音
     * @param $txt
     */
    public function gbk_to_pinyin($txt)
    {
        $l  = strlen($txt);
        $i  = 0;
        $py = array();

        while($i < $l)
        {
            $tmp = ord($txt[$i]);
            if($tmp >= 128)
            {
                $asc = abs($tmp * 256 + ord($txt[$i + 1]) - 65536);
                $i   = $i + 1;
            }
            else
            {
                $asc  = $tmp;
            }
            $py[] = $this->asc_to_pinyin($asc);
            $i++;
        }
        return $py;
    }

    /**
     * utf8转拼音
     * @param   string  $txt
     * @return  array
     */
    public function utf8_to_pinyin($txt)
    {
        $txt = $this->utf8_to_gbk($txt);
        return $this->gbk_to_pinyin($txt);
    }

}
?>
