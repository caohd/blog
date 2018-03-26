<?php

/**
 * 用户表的使用频率太高，特别加一个类
 */
class User
{
    public static $_curUser = [];

    /**
     * 上传图片
     * @API  information.html
     */
    public function upl()
    {
        $upl = new Upload('logo', 'public/images');
        if ($upl->isImage()) {
            if ($val = $upl->upl()) {
                $lastIndex = strripos($val[0], '/', 0);
                echo packData('success', 0, ['logo'=>substr($val[0], $lastIndex)], '上传成功');
            } else
                echo packData('success', 1, [], '上传失败');
        } else {
            echo packData('fail', 2, [], '非法图片类型');
        }
    }
    /**
     * 获得当前用户信息
     * @return array
     */
    public static function getCurUser()
    {
        if (empty(self::$_curUser)) {
            self::$_curUser = self::getUserByName((Input::getInstance())->session('user'));
        }
        return self::$_curUser;
    }

    /**
     * 获得当前用户信息
     * @API  information.html
     */
    public function getCurUserInfo()
    {
        $user = self::getCurUser();
        unset($user['id']);
        unset($user['password']);
        echo packData('success', 0, ['user'=>$user], '获取内容成功');
    }

    /**
     * 获得所有的地址信息
     * @API  information.html
     */
    public function allLocations()
    {
        $db = DB::getInstance(getConfig('db'));
        $db->prepareAndExec('SELECT * FROM b_location', []);
        echo packData('success', 0, ['locations'=>$db->getResultSet()], '获取内容成功');
    }

    /**
     * 保存logo
     * @API information.html
     */
    public function saveLogo()
    {
        $input = Input::getInstance();
        $logo = $input->post('logo');
        $db = DB::getInstance(getConfig('db'));
        $db->prepareAndExec('UPDATE b_user set logo=? WHERE username=?', [$logo, $input->session('user')]);
        if ($db->getEffectRow() > 0) {
            echo packData('success', 0, [], '更新头像成功');
        } else {
            echo packData('fail', 1, [], '更新头像失败');
        }
    }

    /**
     * 更新用户信息
     * @API information.html
     */
    public function updateUser()
    {
        $input = Input::getInstance();
        $db = DB::getInstance(getConfig('db'));
        $db->prepareAndExec('UPDATE b_user SET locationid=?, sex=?, brief=? ,birthday=?, nickname=? WHERE id='.User::getCurUser()['id'],
            [
                $input->post('location'),
                $input->post('sex'),
                $input->post('intro'),
                $input->post('bir'),
                $input->post('nickname') ?? User::getCurUser()['nickname']
            ]);
        echo packData('success', $db->getEffectRow(), [], '更新用户信息成功');
    }

    /**
     * 完成邮箱验证
     * @API finish.html
     */
    public function finish()
    {
        $input = Input::getInstance();
        if (null !== $input->get('n')) {
            echo $this->changeBindMail();
        } else {
            echo $this->mailAuth();
        }
    }

    public function changeBindMail()
    {
        $salt = 'fodia';
        $input = Input::getInstance();
        $s = md5(sha1($salt.':'.User::getCurUser()['username'].':'.$input->get('n')));
        if ($s === $input->get('r')) {
            $db = DB::getInstance(getConfig('db'));
            $db->prepareAndExec('UPDATE b_user SET mail=?, authflag=1 WHERE username=?', [$input->get('n'), $input->get('user')]);
            if (0 === $db->getEffectRow()) {
                return packData('fail', 2, [], '解除绑定失败');
            } else {
                return packData('success', 0, [], '解除绑定成功');
            }
        } else {
            return packData('fail', 1, [], '解除绑定失败');
        }
    }
    public function mailAuth()
    {
        $db = DB::getInstance(getConfig('db'));
        $input = Input::getInstance();
        $db->prepareAndExec('SELECT * FROM b_user WHERE username=?',[$input->get('user')]);
        $user = $db->getResultSet();
        if (empty($user)) {
            return packData('fail', 403, [], '非法访问');
        } else {
            $salt = 'fodia';
            $s = md5(sha1($salt.':'.$user[0]['username'].':'.$user[0]['mail']));

            if ($s === $input->get('r')) {
                $db->prepareAndExec('UPDATE b_user SET authflag=0 WHERE username=?', [$user[0]['username']]);

                // 登录
                $input->session('user', $user[0]['username']);
                $input->cookie('user', $user[0]['username']);
                return packData('success', 0, [], '验证成功');
            } else {
                return packData('fail', 1, [], '验证失败');
            }
        }
    }

    /**
     * 通过用户名获得用户信息
     * @param $user
     * @return array
     */
    public static function getUserByName($user)
    {
        $db = DB::getInstance(getConfig('db'));
        $db->prepareAndExec('SELECT * FROM b_user WHERE username=? LIMIT 1', [$user]);
        return $db->getResultSet()[0] ?? [];
    }

    /**
     * 通过email获得用户信息
     * @param $email
     * @return array
     */
    public static function getUserByEmail($email)
    {
        $db = DB::getInstance(getConfig('db'));
        $db->prepareAndExec('SELECT * FROM b_user WHERE mail=? LIMIT 1', [$email]);
        return $db->getResultSet()[0] ?? [];
    }

    public function changeBrief()
    {
        $input = Input::getInstance();
        $content = $input->post('content');

        $db = DB::getInstance(getConfig('db'));
        $id = User::getCurUser()['id'];
        $db->prepareAndExec("UPDATE b_user SET brief=? WHERE id=$id", [$content]);
        if ($db->getEffectRow()) {
            echo packData('success', 0, [], '更新成功');
        } else {
            echo packData('success', 1, [], '更新失败');
        }
    }
    /**
     * @API home.html
     */
    public function init()
    {
        $input = Input::getInstance();
        $username = $input->get('user') ?? $input->session('user');
        $user = self::getUserByName($username);
        if (empty($user)) {
            echo packData('fail', 1, [], '不存在的用户');
            return ;
        }
        $am = $this->getData($user['id'], 10, 100000000, 100000000);
        unset($user['password']);
        echo packData('success', 0, ['nick' => self::getCurUser()['nickname'], 'logo' => self::getCurUser()['logo'],'user' => $user, 'articles' => $am['articles'], 'moments' => $am['moments']], '请求成功');
    }

    /**
     * @API changeInfo.html
     */
    public function changePassword()
    {
        $input = Input::getInstance();
        $db = DB::getInstance(getConfig('db'));
        $password = $input->post('password');
        $sql = "UPDATE b_user SET password=? WHERE id=?";
        $db->prepareAndExec($sql, [md5(sha1($password)), User::getCurUser()['id']]);
        if (1 === $db->getEffectRow()) {
            echo packData('success', 0, [], '更新密码成功');
        } else {
            echo packData('fail', 1, [], '更新密码失败');
        }
    }

    public function getData(int $uid, int $per, int $ia, int $im) : array
    {
        $db = DB::getInstance(getConfig('db'));
        $fromid = '';
        switch ((Input::getInstance())->post('t')) {
            case 1:
                $fromid = 'fromid=0';
                break;
            case 2:
                $fromid = 'fromid>0';
                break;
            default:
                $fromid = 'fromid>=0';
        }
        $asql =
            <<< SQL
            SELECT 
            b_article.*, b_user.nickname, b_user.logo, b_user.username as user, b_like.id as `like`
            FROM b_article 
            LEFT JOIN b_user ON b_user.id=b_article.uid
            LEFT JOIN b_like ON b_like.aid=b_article.id AND b_like.uid=$uid AND b_like.typeof=1
            WHERE b_article.uid=$uid AND flag>=0 AND b_article.id < $ia AND $fromid
            ORDER BY time DESC 
            LIMIT $per
SQL;
        $db->prepareAndExec($asql, []);
        $articles = $db->getResultSet();

        $msql =
            <<< SQL
            SELECT 
            b_moment.*, b_user.nickname,b_user.logo, b_user.username AS user, b_mpicture.src, b_like.id as `like`
            FROM b_moment 
            LEFT JOIN b_user ON b_user.id=b_moment.uid 
            LEFT JOIN b_mpicture ON b_mpicture.aid=b_moment.id
            LEFT JOIN b_like ON b_like.uid=$uid AND b_like.aid=b_moment.id AND b_like.typeof=3
            WHERE b_moment.uid=$uid  AND b_moment.id < $im
            ORDER BY time DESC 
            LIMIT $per
SQL;
        $db->prepareAndExec($msql, []);
        $moments = $db->getResultSet();
        // merge
        $a = 0;
        $m = 0;
        $as = [];
        $ms = [];
        for ($i = 0; $i < $per && $i < count($articles) + count($moments); $i ++) {
            if ($a === count($articles)) {
                $ms[] = $moments[$m];
                $m ++;
            } else if ($m === count($moments)) {
                $as[] = $articles[$a];
                $a ++;
            } else if ($articles[$a]['time'] > $moments[$m]['time']) {
                $as[] = $articles[$a];
                $a ++;
            } else {
                $ms[] = $moments[$m];
                $m ++;
            }
        }
        return ['articles' => $as, 'moments' => $ms];
    }

    public function loadMore()
    {
        $input = Input::getInstance();

        $r = $this->getData(self::getUserByName($input->get('user') ?? $input->session('user'))['id'], $this->per, $input->post('am') ?? 1, $input->post('mm') ?? 1);
        if (0 === count($r['articles']) && 0 === count($r['moments']))
            echo packData('success', 1, [], '没有更多数据了');
        else
            echo packData('success', 0, $r, '请求成功');
    }

    /**
     * @API info.html message.html
     */
    public function infoInit()
    {
        $db = DB::getInstance(getConfig('db'));
        $input = Input::getInstance();
        $user = $input->get('user') ?? $input->session('user');
//        $user = Input::getInstance()->session('user');
        $db->prepareAndExec('SELECT b_user.*, b_location.city FROM b_user LEFT JOIN b_location ON b_location.id=b_user.locationid WHERE username=? LIMIT 1', [$user]);
        $user = $db->getResultSet()[0];
        unset($user['password']);
        echo packData('success', 0, ['nick'=> User::getCurUser()['nickname'], 'logo' => User::getCurUser()['logo'], 'user' => $user], '请求成功');
    }

    public function getUserInfo()
    {
        $input = Input::getInstance();
        $username = $input->post('user');
        if ($username) {
            $user = User::getUserByName($username);
            unset($user['password']);
        } else {
            $user = [];
        }
        if (empty($user)) {
            echo packData('fail', 1, [], '用户不存在');
        } else {
            echo packData('success', 0, ['user'=>$user], '请求成功');
        }
    }
}