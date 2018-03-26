<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/18 0018
 * Time: 13:49
 */

class Relation
{

    /**
     * 关注或取消关注用户
     * 1对2  1的关注个数+1
     * 情况1：1对2没有建立过关系 新建一个关系并将uid1对uid2设置为关注了
     * 情况2: 1对2建立过关系 更新将uid1对uid2设置为关注了
     * 2对1  2的粉丝个数+1
     * 情况1: 2对1没有建立过关系 新建一个关系并将uid1对uid2设置为关注了
     * 情况2：2对1建立过关系 更新将uid1对uid2设置为关注了
     *
     * @param string $uid1 关注者
     * @param string $uid2 被关注者
     * @param string $status=1 默认为1关注0取消关注
     * @return bool
     */
    public function follow($uid1,$uid2,string $status='1'):bool {

        $db=DB::getInstance(getConfig('db'));

        if(!$this->hasRelation($uid1,$uid2)){
            $db->prepare('INSERT INTO b_relationship SET uid1=:uid1,uid2=:uid2,f1to2=:f1to2')->execute([':uid1'=>$uid1,':uid2'=>$uid2,':f1to2'=>$status]);
            $num12=$db->lastInsertId();
        }else{
            $db->prepare('UPDATE b_relationship SET f1to2=:f1to2 WHERE b_relationship.uid1=:uid1 AND uid2=:uid2')
                ->execute([':f1to2'=>$status,':uid1'=>$uid1,':uid2'=>$uid2]);
            $num12=$db->getEffectRow();
        }


        if(!$this->hasRelation($uid2,$uid1)){
            $db->prepare('INSERT INTO b_relationship SET uid1=:uid1,uid2=:uid2,f2to1=:f2to1')->execute([':uid1'=>$uid2,':uid2'=>$uid1,':f2to1'=>$status]);
            $num21=$db->lastInsertId();
        }else{
            $db->prepare('UPDATE b_relationship SET f2to1=:f2to1 WHERE b_relationship.uid1=:uid1 AND uid2=:uid2')
                ->execute([':f2to1'=>$status,':uid1'=>$uid2,':uid2'=>$uid1]);
            $num21=$db->getEffectRow();
        }
        $temp=1;
        if(intval($status)==0) $temp=-1;
        $uid1follow=intval(getInfoById($uid1,'b_user','following'))+$temp;
        $uid2fans=intval(getInfoById($uid2,'b_user','follower'))+$temp;

        $db->prepare('UPDATE b_user SET following=:following WHERE b_user.id=:uid')->execute([':following'=>$uid1follow,':uid'=>$uid1]);
        $num1=$db->getEffectRow();
        $db->prepare('UPDATE b_user SET follower=:follower WHERE b_user.id=:uid')->execute([':follower'=>$uid2fans,':uid'=>$uid2]);
        $num2=$db->getEffectRow();

        if($num12>0 and $num1>0 and $num2>0 and $num21>0){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @API search.html
     */
    public function concern()
    {
        $uid1 = User::getCurUser()['id'];
        $uid2 = (Input::getInstance())->post('uid');
        if ($this->follow($uid1, $uid2)) {
            echo packData('success', 0, [], '关注成功');
        } else {
            echo packData('fail', 1, [], '关注失败');
        }
    }

    public function addConcern()
    {
        $input = Input::getInstance();
        $id = $input->post('id');
        if ($this->follow(User::getCurUser()['id'], $id, 1)) {
            echo packData('success', 0, [], '关注成功');
        } else {
            echo packData('fail', 1, [], '关注失败');
        }
    }

    /**
     * 是否有关系表
     * @param $uid1
     * @param $uid2
     * @return bool
     */
    private function hasRelation($uid1,$uid2):bool{
        $db=DB::getInstance(getConfig('db'));
        $sql="SELECT id FROM b_relationship WHERE uid1 =:uid1 AND uid2=:uid2";
        $exe=[':uid1'=>$uid1,':uid2'=>$uid2];
        $db->prepare($sql)->execute($exe);
        $num=count($db->getResultSet());
        return !!$num;

    }

    /**
     * 移除粉丝
     * user1移除粉丝user2 即让u2取消关注u1
     * @param $uid1
     * @param $uid2
     * @return bool
     */
    public function removeFollower($uid1,$uid2):bool {
        $result=$this->follow($uid2,$uid1,0);
        return $result;
    }

    /**
     * 我关注了他，但是我不想关注他了
     * @API
     */
    public function cancelShip()
    {
        $input = Input::getInstance();
        $id = $input->post('id');
        if ($this->follow(User::getCurUser()['id'], $id, 0)) {
            echo packData('success', 0, [], '取消关注成功');
        } else {
            echo packData('fail', 1, [], '取消关注失败');
        }
    }

    /**
     * 他关注了我，但是我不想让他关注了
     * @API
     */
    public function cancelConcern()
    {
        $input = Input::getInstance();
        $id = $input->post('id');
        if ($this->follow($id, User::getCurUser()['id'], 0)) {
            echo packData('success', 0, [], '取消关注成功');
        } else {
            echo packData('fail', 1, [], '取消关注失败');
        }
    }
    /*
     * 是否关注中
     * user1是否正在关注user2
     */
    public function isFollowing($uid1,$uid2):bool {
        $db=DB::getInstance(getConfig('db'));
        $sql="SELECT id FROM b_relationship WHERE uid1 =:uid1 AND uid2=:uid2 AND f1to2=:f1to2";
        $exe=[':uid1'=>$uid1,':uid2'=>$uid2,':f1to2'=>1];
        $db->prepare($sql)->execute($exe);
        $num=count($db->getResultSet());
        return !!$num;
    }

    /**
     * @API firend.html
     */
    public function init()
    {
        $db = DB::getInstance(getConfig('db'));
        $input = Input::getInstance();
        if (null !== $input->get('user')) {
            $user = User::getUserByName($input->get('user'));
        } else {
            $user = User::getCurUser();
        }
        if (empty($user)) {
            echo packData('fail', 2, [], '用户不存在');
            return;
        }
        $db->prepareAndExec(
            "SELECT uid2 AS uid, 0 AS f, b_user.nickname, b_user.username, b_user.logo,b_user.brief 
                  FROM b_relationship LEFT JOIN b_user ON b_user.id=uid2
                  WHERE uid1={$user['id']} AND f1to2=1 ORDER BY uid DESC ", []);
        $follow = $db->getResultSet();
        $db->prepareAndExec(
            "SELECT uid1 AS uid, 1 AS f, b_user.nickname, b_user.username, b_user.logo,b_user.brief 
                  FROM b_relationship LEFT JOIN b_user ON b_user.id=uid1
                  WHERE uid2={$user['id']} AND f1to2=1 ORDER BY uid DESC ", []);
        $fans = $db->getResultSet();

        $sql =
            <<<SQL
            
            SELECT 
              b_chat.*, b_user.id AS uid, logo, nickname, username, if(toid=?, 2, 1) AS type # 1代表发送，2代表接受
            FROM b_chat
              LEFT JOIN b_user ON b_user.id=if(toid=?, fromid, toid)
            WHERE belongid=? AND (b_chat.fromid=? OR b_chat.toid=?)
            ORDER BY time DESC 
SQL;
        $db->prepareAndExec($sql, [$user['id'], $user['id'], $user['id'], $user['id'], $user['id'],]);
        $chats = $db->getResultSet();
        unset($user['password']);
        $data = [
            'user'=>$user,
            'follow'=>$follow,
            'fans'=>$fans,
            'logo'=>User::getCurUser()['logo'],
            'nickname'=>User::getCurUser()['nickname']
        ];
        if ($user['username'] === $input->session('user')) {
            $data['chats'] = $chats;
        }
        echo packData('success', 0, $data, '绝望啊');
    }
}