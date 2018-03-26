<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/13 0013
 * Time: 15:32
 */

class Register
{
    private $_userInfo = [];

    /**
     * @API
     */
    public function index ()
    {

        $input=Input::getInstance();
        $usr = $input->post('username');
        $nkn=$input->post('nickname');
        $psw = $input->post('password');

        if($usr == "" || $psw == ""||$nkn=='')
        {
            echo packData('fail', 2, [], '用户名或密码或昵称为空');
        }
        else
        {
            if(empty(User::getUserByName($usr))){
                  if($this->AddUserInfo($usr,$psw,$nkn)){
                      $input->session('user',$usr);
                      $input->cookie('user',$usr);
                      echo packData('success',0,[],'成功注册');
                  }
            }else{
                echo packData('fail',1,[],'用户名存在');
            }
        }
    }

    /**
     *添加用户信息包括用户名密码和昵称
     * @param string $username
     * @param string $password
     * @param string $nickname
     * @return bool
     */
    public function AddUserInfo(string $username, string $password, string $nickname) : bool
    {
        $db = DB::getInstance(getConfig('db'));
        $pwd = md5(sha1($password));
        $db->prepare('INSERT INTO b_user SET username=:username,password=:password,nickname=:nickname')
            ->execute([':username' => $username, ':password' => $pwd, ':nickname' => $nickname]);
        return !!$db->lastInsertId();
    }


    /**
     * @param string $user
     * @return array
     */
    public function getUserInfo(string $user) : array
    {
        if ([] === $this->_userInfo || $this->_userInfo['username'] !== $user) {
            $db = DB::getInstance(getConfig('db'));
            $db->prepare('SELECT * FROM b_user WHERE username=:username LIMIT 1')->execute([':username', $user]);
            $this->_userInfo = $db->getResultSet()[0] ?? [];
        }

        return $this->_userInfo;
    }
}