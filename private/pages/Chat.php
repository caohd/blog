<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/17 0017
 * Time: 18:40
 */

class Chat
{

    private $chatsInfo=[];
    private $chatInfo=[];


    /**
     * 新建一条聊天记录 一个属于自己的 一个属于对方的
     * @param int $toid  接收信息用户id
     * @return bool
     */
    public function newChat($toid):bool {
        $db=DB::getInstance(getConfig('db'));
        $input=Input::getInstance();
        $fromid=$input->session('userid');
        $content=$input->post('content');

        $db->prepare('INSERT INTO b_chat SET fromid=:fromid,toid=:toid,content=:content,belongid=:belongid')->execute([':fromid'=>$fromid,':toid'=>$toid,':content'=>$content,':belongid'=>$fromid]);
        $numa=$db->lastInsertId();
        $db->prepare('INSERT INTO b_chat SET fromid=:fromid,toid=:toid,content=:content,belongid=:belongid')->execute([':fromid'=>$fromid,':toid'=>$toid,':content'=>$content,':belongid'=>$toid]);
        $numb=$db->lastInsertId();

        return (!!$numa and !!$numb);

    }

    /**
     * @API
     */
    public  function deleteChats() {
        $input=Input::getInstance();
        $uid=User::getCurUser()['id'];
        $other = $input->post('uid');
        $db=DB::getInstance(getConfig('db'));
        $db->prepare('DELETE FROM b_chat WHERE b_chat.belongid=? AND ((b_chat.toid=? AND b_chat.fromid=?) OR (b_chat.toid=? AND b_chat.fromid=?))')
            ->execute([$uid, $uid, $other, $other, $uid]);
        echo packData('succes', 0, [], '删除成功');
    }


    /**
     * 获得与某人的所有聊天记录
     * @param $toid
     * @return array|null
     */
    public function getChats($toid):array {
        $input=Input::getInstance();
        $uid=$input->session('userid');

        $db=DB::getInstance(getConfig('db'));
        $db->prepare('SELECT * FROM b_chat WHERE b_chat.belongid=:belongid AND (b_chat.fromid=:fromid OR b_chat.toid=:toid)')->execute([':belongid'=>$uid,':fromid'=>$toid,':toid'=>$toid]);
        $this->chatInfo=$db->getResultSet()??null;
        return $this->chatInfo;
    }


    /**
     * 根据用户id获取用户所有的聊天
     * @param $uid
     * @return array|null
     */
    public function getAllChats($uid):array {
        $db=DB::getInstance(getConfig('db'));
        $db->prepare('SELECT * FROM b_chat WHERE b_chat.belongid=:belongid')->execute([':belongid'=>$uid]);
        $this->chatsInfo=$db->getResultSet()??null;
        return $this->chatsInfo;
    }

    /**
     * 这是一个测试用的方法
     */
    public function dddd()
    {
        $db = DB::getInstance(getConfig('db'));
        $input = Input::getInstance();
        if ($input->get('user')) {
            $u1 = 'caohd1';
            $u2 = 'caohd';
        } else {
            $u1 = 'caohd';
            $u2 = 'caohd1';
        }
//        $user = Input::getInstance()->session('user');
        $db->prepareAndExec('SELECT b_user.*, b_location.city FROM b_user LEFT JOIN b_location ON b_location.id=b_user.locationid WHERE username=? LIMIT 1', [$u1]);
        $u1 = $db->getResultSet()[0];
        $db->prepareAndExec('SELECT b_user.*, b_location.city FROM b_user LEFT JOIN b_location ON b_location.id=b_user.locationid WHERE username=? LIMIT 1', [$u2]);
        $u2 = $db->getResultSet()[0];
        unset($u1['password']);
        unset($u2['password']);

        echo packData('success', 0, ['u1'=>$u1, 'u2'=>$u2], '');
    }

    public function init()
    {
        $sql = '';
    }
}