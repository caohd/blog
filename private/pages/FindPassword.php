<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/18 0018
 * Time: 19:29
 */

class FindPassword
{

    private $from = 'caohd@caohdgg.xyz';

    /**
     * 发送认证邮件到指定邮箱
     * @API changepassword.html
     */
    public function forgetPassword()
    {
        $input = Input::getInstance();
        $to = $input->post('mail');
        $db = DB::getInstance(getConfig('db'));
        $db->prepareAndExec('SELECT * FROM b_user WHERE mail=?', [$to]);
        $result=$db->getResultSet()[0]??null;
        if($result!=null) {
            $user = $result['username'];
            $smtp = new Smtp(getConfig('smtp'));
            $title = '忘记密码';//邮件主题
            $salt = 'fodia';
            $src = 'http://' . DOMAIN_NAME . '/finish.html?user=' . $user . '&r=' .
                md5($salt . ':' . ($user . ':' . $to));

            $content =
                <<< HTML
            欢迎回来Fodia
            请点击以下的连接
            <a href="$src">更改密码</a>
HTML;

            $type = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件
            $status = $smtp->sendMail($to, $this->from, $title, $content, $type);
            if ($status) {
                echo packData('success', 0, [], '发送成功');
            } else {
                echo packData('fail', 1, [], '发送失败');
            }
        }else{
            echo packData('fial',2,[],'不存在该邮箱');
        }
    }

    public function changePassword(){
        $input = Input::getInstance();
        $username=$input->get('user');
        $newpassword=md5(sha1($input->post('password')));
        $comfirmpassword=md5(sha1($input->post('comfirmpassword')));
        if($newpassword===$comfirmpassword){
            $db = DB::getInstance(getConfig('db'));
            $db->prepare('UPDATE b_user SET password=:password WHERE b_user.username=:username')->execute([':password'=>$newpassword,':username'=>$username]);
            if(!!$db->getEffectRow()){
                echo packData('success',3,[],'更改密码成功');
            }else{
                echo packData('fail',4,[],'更改密码失败');
            }
        }else{
            echo packData('fail',5,[],'两次输入密码不相同');
        }
    }
}