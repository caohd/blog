<?php

class World
{
    private $per = 10;
    /**
     * @API world.html
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
        $asql =
            <<< SQL
            SELECT 
            b_article.*, b_user.nickname, b_user.logo, b_user.username as user, b_like.id as `like`, b_relationship.id AS isFriend
            FROM b_article 
            LEFT JOIN b_user ON b_user.id=b_article.uid
            LEFT JOIN b_like ON b_like.aid=b_article.id AND b_like.uid=? AND b_like.typeof=1
            LEFT JOIN b_relationship ON b_relationship.uid1=? AND b_relationship.uid2=b_user.id AND b_relationship.f1to2=1
            WHERE flag=0 AND b_article.id < $ia
            ORDER BY time DESC 
            LIMIT $per
SQL;
        $db->prepareAndExec($asql, [User::getCurUser()['id'], User::getCurUser()['id']]);
        $articles = $db->getResultSet();
        $msql =
            <<< SQL
            SELECT 
            b_moment.*, b_user.nickname,b_user.logo, b_user.username AS user, b_mpicture.src, b_like.id as `like`, b_relationship.id AS isFriend
            FROM b_moment 
            LEFT JOIN b_user ON b_user.id=b_moment.uid 
            LEFT JOIN b_mpicture ON b_mpicture.aid=b_moment.id
            LEFT JOIN b_like ON b_like.uid=? AND b_like.aid=b_moment.id AND b_like.typeof=3
            LEFT JOIN b_relationship ON b_relationship.uid1=? AND b_relationship.uid2=b_user.id AND b_relationship.f1to2=1
            WHERE  b_moment.id < $im
            ORDER BY time DESC 
            LIMIT $per
SQL;
        $db->prepareAndExec($msql, [User::getCurUser()['id'], User::getCurUser()['id']]);
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
}