<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/17 0017
 * Time: 15:58
 */

class Message
{

    private $messageInfo=[];


    /**
     * 新建消息
     * @param int $toid   接收者用户
     * @param int $typeof  消息类型
     * @return bool
     */
    public function createMessage($toid,$typeof):bool{
        $input=Input::getInstance();
        $fromid=$input->session('userid');
        $db=DB::getInstance(getConfig('db'));
        $db->prepare('INSERT INTO b_message SET fromid=:fromid,toid=:toid,typeof=:typeof')->execute([':fromid'=>$fromid,':toid'=>$toid,':typeof'=>$typeof]);
        $num=$db->lastInsertId();
        return $num;
    }


    /**
     * 根据消息id将未读消息变为已读
     * @param int $id
     * @return bool
     */
    public function updateMesaageStatus($id):bool
    {
        $isread=1;
       $db=DB::getInstance(getConfig('db'));
       $db->prepare('UPDATE b_message SET isread=:isread WHERE b_message.id=:id')->execute([':isread'=>$isread,':id'=>$id]);
       return !!$db->getEffectRow();

    }


    /**
     * 根据消息id删除消息
     * @param int $id
     * @return bool
     */
    public function deleteMessage($id):bool {
        $db=DB::getInstance(getConfig('db'));
        $db->prepare('DELETE FROM b_message WHERE b_message.id=:id')->execute([':id'=>$id]);
        $num=$db->getEffectRow();
        return $num;
    }


    /**
     * 根据接受者用户id获得所有的消息信息
     * @param int $uid 接受者用户id
     * @return array|null
     *
     */
    public function getMessageInfo($uid):array {
        $db=DB::getInstance(getConfig('db'));
        $db->prepare('SELECT * FROM b_message WHERE b_message.toid=:toid')->execute([':toid'=>$uid]);
        $this->messageInfo=$db->getResultSet() ?? null;
        return $this->messageInfo;
    }

    /**
     * @API message.html
     */
    public function init()
    {
        $input = Input::getInstance();
        $db = DB::getInstance(getConfig('db'));
        $cur = User::getCurUser();
        $sql =
            <<< SQL
            SELECT b_comment.*, b_user.nickname, b_user.logo, b_moment.id AS mid, b_article.id AS aid
            FROM b_comment
              LEFT JOIN b_user ON b_comment.uid=b_user.id
              LEFT JOIN b_article ON b_article.id=b_comment.aid AND b_comment.typeof=1
              LEFT JOIN b_moment ON b_moment.id=b_comment.aid AND b_comment.typeof=2
            WHERE b_comment.aid IN (SELECT id FROM b_article WHERE b_article.uid=?)
              OR b_comment.aid IN (SELECT id FROM b_moment WHERE b_moment.uid=?)
            ORDER BY b_comment.time DESC
SQL;
        $db->prepareAndExec($sql, [$cur['id'], $cur['id']]);
        $comments = $db->getResultSet();

        $sql =
            <<< SQL
            SELECT 
              b_like.uid, b_like.time, b_user.logo, b_user.nickname, b_picture.id, b_picture.name
            FROM b_like
              RIGHT JOIN b_picture ON b_picture.id=b_like.aid
              RIGHT JOIN b_album ON b_album.id=b_picture.aid
              LEFT JOIN b_user ON b_user.id=b_like.uid
            WHERE typeof=2 AND b_album.uid=?
            ORDER BY time DESC 
SQL;
        $db->prepareAndExec($sql, [$cur['id']]);
        $picLikes = $db->getResultSet();

        $sql =
            <<<SQL
            SELECT 
              b_article.uid, b_user.nickname, b_user.logo, b_article.time, b_article.title
            FROM b_article
              LEFT JOIN b_user ON b_user.id=b_article.uid
            WHERE b_article.fromid=?
            ORDER BY time DESC 
SQL;
        $db->prepareAndExec($sql, [$cur['id']]);
        $forwards = $db->getResultSet();

        $sql =
            <<<SQL
            
            SELECT 
              b_chat.*, b_user.id AS uid, logo, nickname, username, if(toid=?, 2, 1) AS type # 1代表发送，2代表接受
            FROM b_chat
              LEFT JOIN b_user ON b_user.id=if(toid=?, fromid, toid)
            WHERE belongid=? AND (b_chat.fromid=? OR b_chat.toid=?)
            ORDER BY time DESC 
SQL;
        $db->prepareAndExec($sql, [$cur['id'], $cur['id'], $cur['id'], $cur['id'], $cur['id']]);
        $chats = $db->getResultSet();

        unset($cur['password']);
        echo packData('success', 0, ['user'=>$cur, 'picLikes'=>$picLikes, 'forwards'=>$forwards, 'comments'=>$comments, 'chats' => $chats], 'success');
    }
}