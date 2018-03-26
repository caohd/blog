<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/15 0015
 * Time: 18:58
 */

class Like
{
    private $likeInfo=[];
    private $per = 10;
    public function ILike()
    {
        $rst = false;
        $input = Input::getInstance();
        if ('a' === $input->post('id')[0]) { // article
            $aid = substr($input->post('id'), strlen('article'));
            if (empty($row = $this->isLike(1, User::getCurUser()['id'], $aid))) {
                $rst = $this->belike(1, $aid);
            } else {
                $rst = $this->rmLike($aid, 'b_article', $row['id']);
            }
        } else if ('m' === $input->post('id')[0]){
            $aid = substr($input->post('id'), strlen('moment'));
            if (empty($row = $this->isLike(3, User::getCurUser()['id'], $aid))) {
                $rst = $this->belike(3, $aid);
            } else {
                $rst = $this->rmLike($aid, 'b_moment', $row['id']);
            }
        }
        if ($rst) {
            echo packData('success', 0, [], '操作成功');
        } else {
            echo packData('fail', 1, [], '操作失败');

        }
    }

    public function rmlike($aid, $table, int $lid)
    {
        $db = DB::getInstance(getConfig('db'));
        $db->prepareAndExec("UPDATE {$table} SET belike=belike-1 WHERE id=?", [$aid]);

        $db->prepareAndExec('DELETE FROM b_like WHERE id=?', [$lid]);
        return !!$db->getEffectRow();
    }
    /*
     *点赞
     * @param $typeof 点赞的类型  1为博文 2为图片
     * @param $aid 类型的id
     * @return bool
     */
    public function belike(int $type, int $aid) : bool {
        $uid=User::getCurUser()['id'];
        $resultbb = false;
        switch ($type){
            case 1:
                $table = 'b_article';
                break;
            case 2:
                $table = 'b_picture';
                break;
            case 3:
                $table = 'b_moment';
                break;
            default:
                return false;
        }
        $db=DB::getInstance(getConfig('db'));
        $db->prepare("SELECT belike FROM $table WHERE $table.id=:id")->execute([':id'=>$aid]);
        $result=$db->getResultSet()[0];

        $belike = intval($result['belike']) + 1;
        if($this->updateNumData($table, $aid,'belike', $belike)) {
            $db->prepare('INSERT INTO b_like SET uid=:uid,typeof=:typeof,aid=:aid')->execute([':uid'=>$uid,':typeof'=>$type,':aid'=>$aid]);
            $lastid=$db->lastInsertId();
            if($lastid>0){
                $resultbb = true;
            }
        }
        return $resultbb;
    }

    /**
     * 更新某表数目
     * @param string $table  表
     * @param string $aid 类型id
     * @param string $content  更新的字段
     * @param string $num 更新的数据
     * @return bool
     */
    private function updateNumData($table,$aid,$content,$num) : bool {
        $db=DB::getInstance(getConfig('db'));
        $db->prepare("UPDATE $table SET $content=:content WHERE $table.id=:id")->execute([':content'=>$num,':id'=>$aid]);
        $updatenum=$db->getEffectRow();
        return !!$updatenum;
    }


    /*
     * 根据用户id获得所有用户点过赞的点赞信息
     * @param $userid=0 默认为ini之后的userid
     * @return array
     */
    public function getLikeInfo($userid=0):array {
        if($userid==0 and !empty($this->uid))
            $userid=$this->uid;
        $db=DB::getInstance(getConfig('db'));
        $db->prepare("SELECT * FROM b_like WHERE b_like.uid=:uid")->execute([':uid'=>$userid]);
        $this->likeInfo=$db->getResultSet()??null;
        return $this->likeInfo;
    }

    public function isLike(int $tableType, int $uid, int $aid)
    {
        $sql = 'SELECT id FROM b_like WHERE uid=? AND aid=? AND typeof=?';
        $db = DB::getInstance(getConfig('db'));
        $db->prepareAndExec($sql, [$uid, $aid, $tableType]);
        if (0 === count($db->getResultSet())) {
            return [];
        } else {
            return $db->getResultSet()[0];
        }
    }

    public function init()
    {
        $input = Input::getInstance();
        $data = [];
        if (null === $input->get('user')) {
            $user = User::getCurUser();
        } else {
            $user = User::getUserByName($input->get('user'));
        }
        if (empty($user)) {
            echo packData('fail', 2, [], '用户不存在');
            return;
        }
        $data['nick'] = User::getCurUser()['nickname'];
        $data['logo'] = User::getCurUser()['logo'];
        $r = $this->getData($this->per, $user['id'],111111111, 111111111);
        $data['articles'] = $r['articles'];
        $data['moments'] = $r['moments'];
        unset($user['password']);
        $data['user'] = $user;
        echo packData('success', 0, $data, '请求成功');
    }

    public function getData(int $per, $uid, int $ia, int $im) : array
    {
        $db = DB::getInstance(getConfig('db'));
        $asql =
            <<< SQL
            SELECT
              b_article.*, b_user.nickname, b_user.logo, b_user.username as user, b_like.id as `like`
            FROM b_article
              LEFT JOIN b_user ON b_user.id=b_article.uid
              RIGHT JOIN b_like ON b_like.aid=b_article.id
            WHERE flag=0 AND b_article.id < $ia  AND b_like.uid={$uid} AND b_like.typeof=1
            ORDER BY b_like.time DESC
            LIMIT $per;
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
              RIGHT JOIN b_like ON b_like.uid={$uid} AND b_like.aid=b_moment.id AND b_like.typeof=3
            WHERE b_moment.id < $im 
            ORDER BY b_like.time DESC
            LIMIT 10;
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
}