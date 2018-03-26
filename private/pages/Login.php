<?php
/**
 * Created by PhpStorm.
 * User: caohd
 * Date: 17-12-12
 * Time: 下午5:14
 */

class Login
{
    private $_userInfo = [];

    /**
     * @API
     */
    public function index ()
    {
        $input=Input::getInstance();
//        print_r($input->post());
        $user = $input->post('username');
        $psw = $input->post('password');
        if($user == "" || $psw == "")
        {
            echo packData('fail', 2, [], '用户名或密码为空');
        }
        else
        {
            if($this->isUserMatchPsw($user, $psw)){
                $input->session('user', $this->_userInfo['username']);
                $input->cookie('user', $this->_userInfo['username']);
                $input->cookie('admin', User::getUserByName($user)['type']);
                echo packData('success', 0, [], '登录成功');
            }else
            {
                echo packData('fail', 1, [], '用户名或密码错误');
            }
        }
    }

    /**
     * @API
     */
    public function logout()
    {
        $input = Input::getInstance();
        $input->cookie();
        $input->session();
        echo packData('success', 0, [], '退出登录成功');
    }

    /**
     * @API
     */
    public function isUser()
    {
        $user = (Input::getInstance())->post('user');
        if (empty(User::getUserByName($user)) && empty(User::getUserByEmail($user))) {
            echo packData('fail', 1, [], '用户不存在');
        } else {
            echo packData('success', 0, [], '用户存在');
        }
    }

    /**
     * @API
     */
    public function userExist()
    {
        if (empty(User::getUserByName(Input::getInstance()->post('user')))) {
            echo packData('fail', 1, [], '用户不存在');
        } else {
            echo packData('success', 0, [], '用户存在');
        }
    }

    public function truePassword()
    {
        $input = Input::getInstance();
        $password = $input->post('password');
        if ($this->isUserMatchPsw(User::getCurUser()['username'], $password)) {
            echo packData('success', 0, [], '配对成功');
        } else {
            echo packData('fail', 1, [], '配对失败');
        }
    }
    /**
     * @API
     */
    private function emailExist()
    {
        if (empty(User::getUserByEmail(Input::getInstance()->post('email')))) {
            echo packData('fail', 1, [], '用户不存在');
        } else {
            echo packData('success', 0, [], '用户存在');
        }
    }
    /**
     * 用户名和密码是否匹配
     * @return bool
     * @@param string $username 用户名
     * @param string $password 密码
     */
    function isUserMatchPsw(string $username, string $password) : bool
    {
        if (($this->_userInfo = User::getUserByName($username)) || $this->_userInfo = User::getUserByEmail($username)) {
            if ([] === $this->_userInfo) {
                return false;
            } else {
                if (md5(sha1($password)) === $this->_userInfo['password']) {
                    return true;
                } else {
                    return false;
                }
            }
        }

    }

    public function findPassword()
    {

    }
}