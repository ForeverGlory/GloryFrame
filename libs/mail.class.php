<?php
/**
 * mail.class.php   邮件收发类
 * @package         GloryFrame
 * @author          ForeverGlory@qq.com
 * @license         http://code.google.com/p/gloryframe/
 * @copyright       2012 - ?    ForeverGlory
 * @link            frame/libs/mail.class.php
 * @version         $
 * //todo 抄送、密送、紧急、已读回执 未完成 添加附件时有错误
 */
class mail
{
    /**
     * 配置信息
     */
    private $setting = array();
    private $m = null;

    /**
     * 构造函数
     * @param	string/array	$setting	缓存配置，或配置名
     *          string          读取 mail.config.php 里名称的配置
     *          array(
     *              .
     *          )
     * @return  void
     */
    public function __construct($setting = '')
    {
        static $isFirst = true;
        if($isFirst)
        {
           GloryFrame::Auto($this,1);
            if(is_string($setting) || empty($setting))
            {
                if(empty($setting))
                {
                    $setting = $this->_var["setting"]["mail"];
                }
                $setting = $this->config->load('mail', array($setting));
            }
            $this->setting = $setting;
            $this->m = $this->load->lib(array("class.phpmailer", "PHPMailer"), "", 1, "mail", ".php");
        }
    }

    /**
     * 获取邮件地址
     * @param   string  $mailstr
     * @return  array   == array("mail"=>mail,"name"=>name)
     */
    public function getMailAddress($mailstr = "")
    {
        $address = array();
        if(preg_match('/^(.+?)\<(.+?)\>$/', $mailstr, $from))
        {
            $address["mail"] = $address[0]      = trim($from[2]);
            $address["name"] = $address[1]      = trim($from[1]);
        }
        else
        {
            $address["name"] = $address[0]      = trim($mailstr);
        }
        return $address;
    }

    /**
     * 生成邮件地址
     * @param   array   $mailarray
     * @return  string  == mail <mail@localhost>
     */
    public function setMailAddress($mailarray = array())
    {
        $address = "";
        if($mailarray["name"])
        {
            $address = $mailarray["name"] . " <" . $mailarray["mail"] . ">";
        }
        else
        {
            $address = $mailarray["mail"];
        }
        return $address;
    }

    /**
     * 发送邮件
     * @param   string/array    $address    收件地址
     *                          string      mail@localhost / name <mail@localhost>
     *                          array       array(string,string...)
     * @param   string          $subject    标题
     * @param   string          $message    内容
     * @param   string/array    $attachment 附件
     *                          string      /path/path/file 服务器文件地址
     *                          array       array(string,string...)
     * @param   array           $option     其它配置选项
     * @return  true/false
     */
    public function send($address, $subject, $message, $attachment = null, $option = array())
    {
        $option["IsSMTP"] = true;
        $this->__initMail($option);
        $this->addAddress($address);
        $this->setTitle($subject);
        $this->setBody($message);
        $this->AddAttachment($attachment);
        if($this->m->send())
        {
            return true;
        }
        else
        {
            $this->debug->msg("Mailer:" . $this->m->ErrorInfo);
            return false;
        }
    }

    /**
     * 添加收件人
     * @param   string/array    $address    收件地址
     *                          string      mail@localhost / name <mail@localhost>
     *                          array       array(string,string...)
     */
    public function addAddress($address)
    {
        if(is_array($address))
        {
            foreach($address as $val)
            {
                $this->addAddress($val);
            }
        }
        else
        {
            list($mail, $name) = $this->getMailAddress($address);
            $this->m->AddAddress($mail, $name);
        }
    }

    /**
     * 设置邮件标题
     * @param   string  $subject    标题
     */
    public function setTitle($subject)
    {
        $this->m->Subject = $subject;
    }

    /**
     * 设置邮件内容
     * @param   string  $message    内容
     */
    public function setBody($message)
    {
        $this->m->MsgHTML($message);
    }

    /**
     * 添加附件
     * @param   string/array    $attachment 附件
     *                          string      /path/path/file 服务器文件地址
     *                          array       array(string,string...)
     * //todo 添加附件时，将会出现错误
     * E_DEPRECATED 8192    FR_PATH\libs\mail\class.phpmailer.php(1675)     Function set_magic_quotes_runtime() is deprecated
     */
    public function AddAttachment($attachment)
    {
        if(is_array($attachment))
        {
            foreach($attachment as $value)
            {
                $this->AddAttachment($value);
            }
        }
        else
        {
            $this->m->AddAttachment($attachment);
        }
    }

    /**
     * 初始化邮件发送
     * @param   array   $setting    配置信息
     */
    private function __initMail($setting = array())
    {
        if($setting["IsSMTP"])
        {
            $this->m->IsSMTP();
        }
        $this->m->SMTPAuth = array_key_exists("Auth", $setting) ? $setting["Auth"] : $this->setting["Auth"];
        $this->m->Host = array_key_exists("Host", $setting) ? $setting["Host"] : $this->setting["Host"];
        $this->m->Port = array_key_exists("Port", $setting) ? $setting["Port"] : $this->setting["Port"];
        $this->m->Username = array_key_exists("Username", $setting) ? $setting["Username"] : $this->setting["Username"];
        $this->m->Password = array_key_exists("Password", $setting) ? $setting["Password"] : $this->setting["Password"];
        $this->m->From = array_key_exists("From", $setting) ? $setting["From"] : $this->setting["From"];
        $this->m->FromName = array_key_exists("FromName", $setting) ? $setting["FromName"] : $this->setting["FromName"];
        $this->m->IsHTML(array_key_exists("IsHTML", $setting) ? $setting["IsHTML"] : $this->setting["IsHTML"]);
        $ReplyTo   = array_key_exists("ReplyTo", $setting) ? $setting["ReplyTo"] : $this->setting["ReplyTo"];
        $addresses = strToArray(";", $ReplyTo);
        foreach($addresses as $address)
        {
            list($mail, $name) = $this->getMailAddress($address);
            $this->m->AddReplyTo($mail, $name);
        }
        $this->m->CharSet = $this->setting["CharSet"];
    }

}
?>