<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/13 0013
 * Time: 20:59
 */

class Comment
{
    private $commentInfo=[];

    /*
     * 评论
     */
    public function makeComment($aid, $type):bool {
        $input=Input::getInstance();
        $content=$input->post('content');
        $uid=User::getCurUser()['id'];

        $btype='';
        switch ($type){
            case 0:
                $btype='b_comment';
                break;
            case 1:
                $btype='b_article';
                break;
            case 2:
                $btype='b_moment';
                break;
            default:break;
        }
        $db=DB::getInstance(getConfig('db'));
        $db->prepare('INSERT INTO b_comment SET aid=:aid,typeof=:typeof,uid=:uid,content=:content')
            ->execute([':aid' => $aid, ':typeof' => $type, ':uid' =>$uid, ':content' => $content]);
        $num=$db->lastInsertId();
        $result=false;
        if($num>0){
            if(!!$type){
                //when type is moment(2) or article(1)
                if($this->updateCommentNumData($btype,$aid)){
                   $result=true;
                }
            }else{
                $result=true;
            }
        }
        return $result;
    }

    /**
     * @API 评论的评论
     */
    public function cComment()
    {
        $input = Input::getInstance();
        $id = $input->post('id') ?? 0;
        if ($this->makeComment($id, 0)) {
            echo packData('success', 0, [], '添加评论成功');
        } else {
            echo packData('fail', 1, [], '添加评论失败');
        }
    }

    public function macomment()
    {
        $input = Input::getInstance();
        $id = $input->post('id') ?? 0;
        $rv = false;
        switch ($id[0]) {
            case 'a' :
                $rv = $this->makeComment(substr($id, strlen('article')), 1);
                break;
            case 'm':
                $rv = $this->makeComment(substr($id, strlen('moment')), 2);
                break;
            default:
                // to do nothing
        }
        if ($rv) {
            echo packData('success', 0, ['id' => User::getCurUser()['id']], '添加评论成功');
        } else {
            echo packData('fail', 1, [], '添加评论失败');
        }
    }

    /**
     * @API home.html index.html
     */
    public function delete()
    {
        $input = Input::getInstance();
        $id = $input->post('id');
        echo $this->deleteComment($id);
    }
    /**
     * 根据评论id删除评论
     * 删除对应的评论数
     * @param $id
     * @return bool
     */
    public function deleteComment($id){
        $db=DB::getInstance(getConfig('db'));

        $db->prepare('SELECT b_comment.*, b_user.username FROM b_comment LEFT JOIN b_user ON b_user.id=b_comment.uid  WHERE b_comment.id=:id')->execute([':id'=>$id]);

        $result=$db->getResultSet()['0']??null;
        if(null !== $result && (isSystemManager() || User::getCurUser()['username'] === $result['username'])){
            $type=$result['typeof'];
            $aid=$result['aid'];

            $db->prepare('DELETE FROM b_comment WHERE b_comment.id=? OR (b_comment.typeof=0 AND b_comment.aid=?)')->execute([$id, $id]);
            $num=$db->getEffectRow();
            if(!!$num){
                if(!!$type) { //delete comment num when type is moment or article
                    $btype = '';
                    switch ($type) {
                        case 1:
                            $btype = 'b_article';
                            break;
                        case 2:
                            $btype = 'b_moment';
                            break;
                        default:
                            break;

                    }
                    if ($this->updateCommentNumData($btype, $aid, -1)) {
                        return packData('success', 0, [], '成功在文章或朋友圈中删除该评论数');
                    }else{
                        return packData('fail',2,[],'在文章或朋友圈中删除该评论数失败');
                    }
                }
                return packData('success',1,[],'删除评论成功');
            }else{
                return packData('fail',3,[],'删除评论失败');
            }
        }
        return packData('fail', 4, [], '无法删除');
    }

    /**
     * @API index.html home.html
     */
    public function loadComment()
    {
        $input = Input::getInstance();
        $db = DB::getInstance(getConfig('db'));
        $type = 1;
        switch ($input->post('id')[0]) {
            case 'a':
                $type = 1;
                break;
            case 'm':
                $type = 2;
                break;
            default:
                break;
        }
        $aid = substr($input->post('id'), 1 === $type ? strlen('article') : strlen('moment'));

        $c = $this->getCommentInfo($aid, $type);
        $sql = "SELECT b_comment.*, b_user.nickname AS nick, b_user.logo ".
                "FROM b_comment LEFT JOIN b_user ON b_user.id=b_comment.uid WHERE aid=? AND typeof=0 ";
        $db->prepare($sql);
        for ($i = 0; $i < count($c); $i ++) {
            $db->execute([$c[$i]['id']]);
            $c[$i]['ccomm'] = $db->getResultSet();
        }

        echo packData('success', 0, ['comment' => $c], '获取评论成功');
    }

    /*
     * 更新评论数
     * @param $table 表
     * @param $status //默认加1条评论数
     * @return bool
     */
    private function updateCommentNumData($table,$aid,$status=1):bool
    {
        $num=0;
        $db=DB::getInstance(getConfig('db'));
        $db->prepare("SELECT comment FROM $table WHERE id=:aid")->execute([':aid'=>$aid]);
        $comment=$db->getResultSet()[0]['comment'];
        $comment=intval($comment)+$status;
        $db->prepare("UPDATE $table SET comment=:comment WHERE $table.id = :aid")->execute([':comment'=>$comment,':aid'=>$aid]);
        $num=$db->getEffectRow();
        return !!$num;
    }

    /**
     * 根据类型和id查找所有评论信息
     * @param  $aid
     * @param $type
     * @return array
     */
    public function getCommentInfo($aid,$type):array {
       if([]===$this->commentInfo){
           $db=DB::getInstance(getConfig('db'));
           $db->prepare('SELECT b_comment.*, b_user.nickname as nick, b_user.logo FROM b_comment LEFT JOIN b_user ON b_user.id=b_comment.uid  WHERE aid=:aid AND typeof=:typeof')->execute([':aid'=>$aid,':typeof'=>$type]);
           $this->commentInfo=$db->getResultSet() ?? [];
       }
       return $this->commentInfo;
    }
}