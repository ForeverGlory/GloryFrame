<?php
/**
 * 生成验证码
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/libs/checkcode.class.php
 * @version         $
 * demo:
 * $checkcode = new checkcode();
 * $checkcode->image($code='');
 */
class checkcode
{
    /**
     * 默认配置
     * @var type
     */
    private $setting = array();
    /**
     * 当前配置
     * @var type
     */
    private $cur = array();
    /**
     * 图片内存
     */
    private $img;

    function __construct($setting = array())
    {
       GloryFrame::Auto($this, 1);
        $this->font = $this->_var["syspath"]["data"] . "font" . DIRECTORY_SEPARATOR . "elephant.ttf";
        if(empty($setting) || is_array($setting))
        {
            if(empty($setting))
            {
                $setting = $this->_var["setting"]["checkcode"];
            }
            $setting = $this->config->load("checkcode", array($setting));
        }
        $this->setting = $setting;
    }

    /**
     * 生成图片
     */
    public function image($code = '', $option = array())
    {
        $this->cur = arrayMerge($this->setting, $option);
        if(empty($code))
        {
            $this->cur["code"] = random($this->cur["codeLen"]);
        }
        else
        {
            $this->cur["code"] = $code;
            $this->cur["codeLen"] = strlen($code);
        }
        $this->img = imagecreatetruecolor($this->cur["width"], $this->cur["height"]);
        //设置背景色
        if(empty($this->cur["backGround"]))
        {
            $background = imagecolorallocate($this->img, rand(0, 156), rand(0, 156), rand(0, 156));
        }
        else
        {
            $background = imagecolorallocate($this->img, hexdec(substr($this->cur["backGround"], 1, 2)), hexdec(substr($this->cur["backGround"], 3, 2)), hexdec(substr($this->cur["backGround"], 5, 2)));
        }
        //画一个柜形，设置背景颜色。
        imagefilledrectangle($this->img, 0, $this->cur["height"], $this->cur["width"], 0, $background);
        if($this->cur["isDisturb"])
        {
            $this->creatLine();
        }
        $this->creatFont();
        $this->output();
    }

    /**
     * 获取验证码
     */
    public function getCode()
    {
        return strtolower($this->cur["code"]);
    }

    /**
     * 生成文字
     */
    private function creatFont()
    {
        $x = $this->cur["width"] / $this->cur["codeLen"];
        for($i = 0; $i < $this->cur["codeLen"]; $i++)
        {
            if(empty($this->cur["fontColor"]))
            {
                $fontColor = imagecolorallocate($this->img, rand(0, 156), rand(0, 156), rand(0, 156));
            }
            else
            {
                $fontColor = imagecolorallocate($this->img, hexdec(substr($this->cur["fontColor"], 1, 2)), hexdec(substr($this->cur["fontColor"], 3, 2)), hexdec(substr($this->cur["fontColor"], 5, 2)));
            }
            if($this->cur["is"])
            {
                $angle = rand(-30, 30);
            }
            else
            {
                $angle = rand(-10, 10);
            }
            imagettftext($this->img, $this->cur["fontSize"], $angle, $x * $i + rand(0, 5), $this->cur["height"] / 1.4, $fontColor, $this->font, substr($this->cur["code"], $i, 1));
        }
    }

    /**
     * 画线 干扰正常识别
     */
    private function creatLine()
    {
        imagesetthickness($this->img, 3);
        $xpos   = ($this->cur["fontSize"] * 2) + rand(-5, 5);
        $width  = $this->cur["width"] / 2.66 + rand(3, 10);
        $height = $this->cur["fontSize"] * 2.14;

        if(rand(0, 100) % 2 == 0)
        {
            $start = rand(0, 66);
            $ypos  = $this->cur["height"] / 2 - rand(10, 30);
            $xpos += rand(5, 15);
        }
        else
        {
            $start = rand(180, 246);
            $ypos  = $this->cur["height"] / 2 + rand(10, 30);
        }

        $end       = $start + rand(75, 110);
        $fontColor = imagecolorallocate($this->img, rand(0, 156), rand(0, 156), rand(0, 156));
        imagearc($this->img, $xpos, $ypos, $width, $height, $start, $end, $fontColor);

        $width = $this->cur["width"] * 0.75;
        if(rand(1, 75) % 2 == 0)
        {
            $start = rand(45, 111);
            $ypos  = $this->cur["height"] / 2 - rand(10, 30);
            $xpos += rand(5, 15);
        }
        else
        {
            $start = rand(200, 250);
            $ypos  = $this->cur["height"] / 2 + rand(10, 30);
        }

        $end       = $start + rand(75, 100);
        $fontColor = imagecolorallocate($this->img, rand(0, 156), rand(0, 156), rand(0, 156));
        imagearc($this->img, $width, $ypos, $width, $height, $start, $end, $fontColor);
    }

    /**
     * 输出图片
     */
    private function output()
    {
        $this->output->setContentType("image/png", "");
        imagepng($this->img);
        imagedestroy($this->img);
    }

}