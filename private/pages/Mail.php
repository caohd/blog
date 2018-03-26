<?php
/**
 * Created by PhpStorm.
 * User: caohd
 * Date: 17-12-16
 * Time: 下午7:41
 */

class Mail
{
    private $from = 'caohd@caohdgg.xyz';

    /**
     * 发送认证邮件到指定邮箱
     * @API information.html
     */
    public function checkEmail($to = '')
    {
        $input = Input::getInstance();
        if ('' === $to)
            $to = $input->post('m');
        $db = DB::getInstance(getConfig('db'));
        $db->prepareAndExec('UPDATE b_user SET mail=? WHERE username=?', [$to, $input->session('user')]);

        $smtp = new Smtp(getConfig('smtp'));
        $title = '绑定邮箱';//邮件主题
        $salt = 'fodia';
        $src = 'http://'.DOMAIN_NAME.'/finish.html?user='.$input->session('user').'&r='.
            md5(sha1($salt.':'.($input->session('user').':'.$to)));

        $content =
                <<< HTML
            欢迎注册Fodia
            点击以下的连接
            <a href="$src">完成注册</a>
HTML;

        $type = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件

        $status = $smtp->sendMail($to, $this->from, $title, $content, $type);
        if ($status) {
            echo packData('success', 0, [], '发送成功');
        } else {
            echo packData('fail', 1, [], '发送失败');
        }
    }


    public function changeBindEmail()
    {
        $input = Input::getInstance();
        $email = $input->post('email');

        if ($email === User::getCurUser()['mail']) {
            echo packData('success', 2, [], '没有更改邮箱');
        } else if (0 != User::getCurUser()['authflag']) {
            $this->checkEmail($email);
        } else {
            $smtp = new Smtp(getConfig('smtp'));
            $salt = 'fodia';
            $k = md5(sha1($salt.':'.User::getCurUser()['username'].':'.$email));
            $src = "http://".DOMAIN_NAME."/finish.html?r={$k}&n={$email}&user=".$input->session('user');
            $title = '解除邮箱绑定';
            $content = <<<HTML
            点击下面的连接确定取消该邮箱的绑定，<br />
            如果不点击这里的话,修改邮箱将被阻止
            <a href="{$src}">点击完成邮箱更改</a>
HTML;
            $type = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件
            $to = User::getCurUser()['mail'];

            $s1 = $smtp->sendMail($to, $this->from, $title, $content, $type);


            $to = $email;
            $title = '绑定邮箱';
            $src = 'http://'.DOMAIN_NAME.'/finish.html?user='.$input->session('user').'&r='.
                md5(sha1($salt.':'.($input->session('user').':'.$to)));
            $content =
                <<< HTML
            欢迎注册Fodia
            点击以下的连接
            <a href="$src">完成注册</a>
HTML;
            $s2 = $smtp->sendMail($to, $this->from, $title, $content, $type);
            if ($s1 && $s2) {
                echo packData('success', 0, [], '申请修改绑定邮箱成功，请到相应的邮箱进行操作');
            } else {
                echo packData('fail', 1, [], '申请修改绑定邮箱失败');
            }
        }
    }
}