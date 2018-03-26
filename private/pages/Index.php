<?php

class Index
{
    private $per = 10;
    /**
     * 获得index.html的初始化数据
     * @API index.html
     */
    public function init()
    {
        $data = [];
        $input = Input::getInstance();
        if (!$input->session('user')) {
            echo packData('success', 403, [], '权限不足');
            return;
        }
        $r = $this->getData($this->per, 111111111, 111111111);
        $data['articles'] = $r['articles'];
        $data['moments'] = $r['moments'];
        $data['user'] = $input->session('user');
        $data['logo'] = User::getCurUser()['logo'];
        $data['nick'] = User::getCurUser()['nickname'];
        if (0 === count($data['articles']) && 0 === count($data['moments']))
            echo packData('success', 1, $data, '没有更多数据了');
        else
            echo packData('success', 0, $data, '请求成功');
    }

    public function getData(int $per, int $ia, int $im) : array
    {
        $db = DB::getInstance(getConfig('db'));
        $ids = implode($this->getIDs(), ',');
        $asql =
            <<< SQL
            SELECT 
            b_article.*, b_user.nickname, b_user.logo, b_user.username as user, b_like.id as `like`
            FROM b_article 
            LEFT JOIN b_user ON b_user.id=b_article.uid
            LEFT JOIN b_like ON b_like.aid=b_article.id AND b_like.uid=? AND b_like.typeof=1
            WHERE b_article.uid IN ($ids) AND flag=0 AND b_article.id < $ia
            ORDER BY time DESC 
            LIMIT $per
SQL;
        $db->prepareAndExec($asql, [User::getCurUser()['id']]);
        $articles = $db->getResultSet();
        $msql =
            <<< SQL
            SELECT 
            b_moment.*, b_user.nickname,b_user.logo, b_user.username AS user, b_mpicture.src, b_like.id as `like`
            FROM b_moment 
            LEFT JOIN b_user ON b_user.id=b_moment.uid 
            LEFT JOIN b_mpicture ON b_mpicture.aid=b_moment.id
            LEFT JOIN b_like ON b_like.uid=? AND b_like.aid=b_moment.id AND b_like.typeof=3
            WHERE b_moment.uid IN ($ids)  AND b_moment.id < $im
            ORDER BY time DESC 
            LIMIT $per
SQL;
        $db->prepareAndExec($msql, [User::getCurUser()['id']]);
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

        $r = $this->getData($this->per, $input->post('am') ?? 1, $input->post('mm') ?? 1);
        if (0 === count($r['articles']) && 0 === count($r['moments']))
            echo packData('success', 1, [], '没有更多数据了');
        else
            echo packData('success', 0, $r, '请求成功');
    }
    /**
     * 获得已经关注的所有用户的id数组
     * @return array
     */
    private function getIDs() : array
    {
        $rv = [];
        $db = DB::getInstance(getConfig('db'));
        $user = User::getCurUser();
        $rv[] = $user['id'];
        $db->prepareAndExec('SELECT uid1, uid2, f1to2 FROM b_relationship WHERE uid1=?',
            [$user['id']]);
        $rst = $db->getResultSet();
        foreach ($rst as $relationship) {
            if ($relationship['uid1'] === $user['id'] && 1 == $relationship['f1to2']) {
                $rv[] = $relationship['uid2'];
            }
        }
        return $rv;
    }


    /**
     * 获得所有与当前用户有关系的用户id数组
     * uid1为当前用户
     * @return array
     */
    private function getAllIDs() : array
    {
        $rv = [];
        $db = DB::getInstance(getConfig('db'));
        $user = User::getCurUser();
        $db->prepareAndExec('SELECT uid1, uid2, f1to2, f2to1 FROM b_relationship WHERE uid1=? ',
            [$user['id']]);
        $rst = $db->getResultSet();
        foreach ($rst as $relationship) {
            if ($relationship['uid1'] === $user['id'] && 1 == $relationship['f1to2']) {
                $rv[] = $relationship['uid2'];
            }elseif ($relationship['uid1']===$user['id']&&1===$relationship['f2to1']){
                $rv[]=$relationship['uid2'];
            }
        }
        return $rv;
    }


    /**
     * 获得album.html的初始化数据
     * @API album.html
     */
    public function homeAlbumInit(){
        $data = [];
        $input = Input::getInstance();
        $db = DB::getInstance(getConfig('db'));

        $user = User::getCurUser();
        $asql =
            <<< SQL
            SELECT b_album.*,max(sr.src) 
            FROM b_album LEFT JOIN(SELECT src,aid FROM b_picture ORDER BY time DESC LIMIT 1) sr 
            ON sr.aid=b_album.id 
            GROUP BY b_album.id=? 
            ORDER BY time DESC
            LIMIT 10
SQL;
        $db->prepareAndExec($asql, [$user['id']]);
        $data['album'] = $db->getResultSet();

        $data['user'] = $input->session('user');
        echo packData('success', 0, $data, '请求成功');
    }

    /**
     * 获得pictures.html的初始化数据
     * @API pictures.html
     */
    public function homePictureInit(){

        $data = [];
        $input = Input::getInstance();
        $db = DB::getInstance(getConfig('db'));

        $user = User::getCurUser();
        $asql =
            <<< SQL
            SELECT 
            b_picture.*, b_album.name,b_album.pictures
            FROM b_picture LEFT JOIN b_album ON b_album.id=b_picture.aid
            WHERE uid=?
            ORDER BY time DESC 
            LIMIT 10
SQL;
        $db->prepareAndExec($asql, [$user['id']]);
        $data['picture'] = $db->getResultSet();

        $data['user'] = $input->session('user');
        echo packData('success', 0, $data, '请求成功');
    }

    /**
     * 获得world.html的初始化数据
     * @API world.html
     * uid1为user
     */

    public function worldInit(){
        $data = [];
        $input = Input::getInstance();
        $db = DB::getInstance(getConfig('db'));

        $user = User::getCurUser();
        $wsql =
            <<< SQL
            SELECT b_article.*, b_user.nickname, b_user.logo,b_relationship.f1to2,b_relationship.f2to1 
            FROM b_article 
            LEFT JOIN b_user ON b_user.id=b_article.uid 
            LEFT JOIN b_relationship ON (b_relationship.uid2=b_article.uid AND b_relationship.uid1=?) 
            ORDER BY readers DESC
            LIMIT 10
SQL;
        $db->prepareAndExec($wsql, [$user['id']]);
        $data['world'] = $db->getResultSet();

        $data['user'] = $input->session('user');
        echo packData('success', 0, $data, '请求成功');

    }

    public function upl() {
        $upl = new Upload('momentPic', 'public/images');
        if ($upl->isImage()) {
            if ($val = $upl->upl()) {
                $lastIndex = strripos($val[0], '/', 0);
                echo packData('success', 0, ['src'=>substr($val[0], $lastIndex)], '上传成功');
            } else
                echo packData('success', 1, [], '上传失败');
        } else {
            echo packData('fail', 2, [], '非法图片类型');
        }
    }
}