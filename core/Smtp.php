<?php

class Smtp
{
    private $_config = [
        'port'          => 25,
        'timeout'       => 100,
        'logfile'       => '/dev/null',
        'smtpserver'   => '',
        'auth'          => true,
        'host'          => '',
        'user'          => '',
        'password'      => '',
    ];

    private $sock;

    public function __construct(array $config)
    {
        $this->_config = array_merge($this->_config, $config);
    }

    /**
     * 发送邮件
     * @param string $to        要发送给的目标
     * @param string $from      谁发送的
     * @param string $subject   标题
     * @param string $body      内容
     * @param string $mailtype  邮件类型
     * @param string $cc        CC信息
     * @param string $bcc
     * @param string $additional_headers
     * @return bool 发送成功返回true否则返回false
     */
    public function sendMail(
        string $to, string $from, string $subject = "", string $body = "", string $mailtype = '', string $cc = "",
        $bcc = "", $additionalHeaders = "")
    {
        // 获得mail正确的mail格式 ***@******.****
        $from = $this->getAddress($this->stripComment($from));
        // 清除body里面的\r\n
        $body = preg_replace("/(^|(\r\n))(\.)/", "\1.\3", $body);
        // 设置头信息
        $header = "MIME-Version:1.0\r\n";
        if($mailtype=="HTML"){
            $header .= "Content-Type:text/html\r\n";
        }
        $header .= "To: ".$to."\r\n";
        if ($cc != "") {
            $header .= "Cc: ".$cc."\r\n";
        }
        $header .= "From: $from<".$from.">\r\n";
        $header .= "Subject: ".$subject."\r\n";
        $header .= $additionalHeaders;
        $header .= "Date: ".date("r")."\r\n";
        $header .= "X-Mailer:By Redhat (PHP/".phpversion().")\r\n";
        list($msec, $sec) = explode(" ", microtime());
        $header .= "Message-ID: <".date("YmdHis", $sec).".".($msec*1000000).".".$from.">\r\n";


        $tos = explode(",", $this->stripComment($to));
        if ($cc != "") {
            $tos = array_merge($tos, explode(",", $this->stripComment($cc)));
        }
        if ($bcc != "") {
            $tos = array_merge($tos, explode(",", $this->stripComment($bcc)));
        }

        $rv = true;

        // 循环发送信息
        foreach ($tos as $to) {
            $to = $this->getAddress($to);
            // 打开连接
            if (!$this->sockopen($to)) {
                $this->writeToLog("Error: Cannot send email to ".$to."\n");
                $rv = false;
                continue;
            }
            // 发送
            if ($this->send($this->_config['host'], $from, $to, $header, $body)) {
                $this->writeToLog("E-mail has been sent to <".$to.">\n");
            } else {
                $this->writeToLog("Error: Cannot send email to <".$to.">\n");
                $rv = false;
            }
            // 关闭连接
            fclose($this->sock);
            $this->writeToLog("Disconnected from remote host\n");
        }

        return $rv;
    }

    /**
     * 发送邮件
     * @param string $helo      发件人身份
     * @param string $from      发件人
     * @param string $to        计划接收人
     * @param string $header    发送的邮件头
     * @param string $body      邮件内容
     * @return bool             发送成功与否
     */
    private function send(string $helo, string $from, string $to, string $header, string $body = "")
    {
        // 协商发送
        // 发送HELO命令以标识发件人自己的身份
        if (!$this->putCMD("HELO", $helo)) {
            return $this->errorLog("sending HELO command");
        }
        //auth
        if($this->_config['auth']){
            if (!$this->putCMD("AUTH LOGIN", base64_encode($this->_config['user']))) {
                return $this->errorLog("sending HELO command");
            }
            if (!$this->putCMD("", base64_encode($this->_config['password']))) {
                return $this->errorLog("sending HELO command");
            }
        }
        // 发送MAIL请求，表明即将发送邮件
        if (!$this->putCMD("MAIL", "FROM:<".$from.">")) {
            return $this->errorLog("sending MAIL FROM command");
        }
        // 发送RCPT命令，以标识该电子邮件的计划接收人
        if (!$this->putCMD("RCPT", "TO:<".$to.">")) {
            return $this->errorLog("sending RCPT TO command");
        }

        // 发送内容
        // 发送邮件，用命令DATA发送
        if (!$this->putCMD("DATA")) {
            return $this->errorLog("sending DATA command");
        }
        if (!$this->sendContent($header, $body)) {
            return $this->errorLog("sending message");
        }

        // 发送结束内容
        // 以.表示结束输入内容一起发送出去
        if (!$this->sendEOM()) {
            return $this->errorLog("sending <CR><LF>.<CR><LF> [EOM]");
        }
        // 结束此次发送，用QUIT命令退出
        if (!$this->putCMD("QUIT")) {
            return $this->errorLog("sending QUIT command");
        }

        return TRUE;
    }

    /**
     * 建立和$address的连接
     * @param string $address
     * @return bool
     */
    private function sockopen(string $address)
    {

        if ($this->_config['smtpserver'] == "") {
            return $this->sockopenMX($address);
        } else {
            return $this->sockopenRaley();
        }

    }

    /**
     * 通过relay连接建立sock
     * @return bool
     */
    private function sockopenRaley()
    {
        $this->writeToLog("Trying to ".$this->_config['smtpserver'].":".$this->_config['port']."\n");
        $this->sock = @fsockopen($this->_config['smtpserver'], $this->_config['port'], $errno, $errstr, $this->_config['timeout']);

        if (!($this->sock && $this->sendOK())) {
            $this->writeToLog("Error: Cannot connenct to relay host ".$this->_config['smtpserver']."\n");
            $this->writeToLog("Error: ".$errstr." (".$errno.")\n");
            return FALSE;
        }
        $this->writeToLog("Connected to relay host ".$this->_config['smtpserver']."\n");

        return TRUE;
    }

    /**
     * 没有设置smtpserver的时候通过解析计划接收人的邮件信息获得远程主机信息
     * @param $address
     * @return bool
     */
    private function sockopenMX(string $address)
    {
        $domain = preg_replace("/^.+@([^@]+)$/", "\1", $address);
        if (!@getmxrr($domain, $MXHOSTS)) {
            $this->writeToLog("Error: Cannot resolve MX \"".$domain."\"\n");
            return FALSE;
        }

        foreach ($MXHOSTS as $host) {
            $this->writeToLog("Trying to ".$host.":".$this->_config['port']."\n");
            $this->sock = @fsockopen($host, $this->_config['port'], $errno, $errstr, $this->_config['timeout']);
            if (!($this->sock && $this->sendOK())) {
                $this->writeToLog("Warning: Cannot connect to mx host ".$host."\n");
                $this->writeToLog("Error: ".$errstr." (".$errno.")\n");
                continue;
            }
            $this->writeToLog("Connected to mx host ".$host."\n");
            return TRUE;

        }
        $this->writeToLog("Error: Cannot connect to any mx hosts (".implode(", ", $MXHOSTS).")\n");

        return FALSE;
    }

    /**
     * 发送邮件的内容
     * @param $header
     * @param $body
     * @return bool
     */
    private function sendContent($header, $body)
    {
        fputs($this->sock, $header."\r\n".$body);
        return TRUE;
    }

    /**
     * 发送.结束发送
     * @return bool
     */
    private function sendEOM()
    {
        fputs($this->sock, "\r\n.\r\n");
        return $this->sendOK();
    }

    /**
     * 检测是否发送成功
     * @return bool
     */
    private function sendOK()
    {
        $response = str_replace("\r\n", "", fgets($this->sock, 512));
        if (!preg_match("/^[23]/", $response)) {
            fputs($this->sock, "QUIT\r\n");
            fgets($this->sock, 512);
            $this->writeToLog("Error: Remote host returned \"".$response."\"\n");
            return FALSE;
        }

        return TRUE;
    }

    /**
     * 向计划收件人发送包
     * @param $cmd
     * @param string $arg
     * @return bool
     */
    private function putCMD($cmd, $arg = "")
    {

        if ($arg != "") {
            if($cmd=="")
                $cmd = $arg;
            else
                $cmd = $cmd." ".$arg;
        }
        fputs($this->sock, $cmd."\r\n");

        return $this->sendOK();
    }

    /**
     * 写入错误日志
     * @param string $string
     * @return bool
     */
    private function errorLog($string)
    {
        $this->writeToLog("Error: Error occurred while ".$string.".\n");
        return FALSE;
    }

    /**
     * 把日志信息写入到log文件
     * @param $message
     * @return bool
     */
    private function writeToLog(string $message)
    {

        if ($this->_config['logfile'] == "") {
            return TRUE;
        }

        $message = date("M d H:i:s ").get_current_user()."[".getmypid()."]: ".$message;
        if (!@file_exists($this->_config['logfile']) || !($fp = @fopen($this->_config['logfile'], "a"))) {
            return FALSE;
        }

        flock($fp, LOCK_EX);
        fputs($fp, $message);
        fclose($fp);

        return TRUE;
    }

    /**
     * 去除address中的不合法字符
     * @param string $address
     * @return string
     */
    private function stripComment(string $address)
    {

        $comment = "/\([^()]*\)/";
        while (preg_match($comment, $address)) {
            $address = preg_replace($comment, "", $address);
        }
        return $address;
    }

    /**
     * 去除address中的不合法字符
     * @param string $address
     * @return string
     */
    private function getAddress(string $address)
    {
        $address = preg_replace("/([ \t\r\n])+/", "", $address);
        $address = preg_replace("/^.*<(.+)>.*$/", "\1", $address);
        return $address;
    }
}
