<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/15 0015
 * Time: 15:12
 */

class Article
{
    private $articleInfo=[];


    /*
     * 发表文章
     * @API index.html
     */
    public function publish(){
        $db=DB::getInstance(getConfig('db'));
        $input=Input::getInstance();
        $uid=User::getCurUser()['id'];
        $title=$input->post('title');
        $content=$input->post('content');
        $flag=$input->post('flag');
        $db->prepare('INSERT INTO b_article SET title=:title,content=:content,uid=:uid,flag=:flag')->execute([':title'=>$title,':content'=>$content,':uid'=>$uid,':flag'=>$flag]);
        $num=$db->lastInsertId();
        if($num>0){
            $sql =
               <<< SQL
            SELECT 
            b_article.*, b_user.nickname, b_user.logo, b_user.username AS user,b_like.id AS `like`
            FROM b_article 
            LEFT JOIN b_user ON b_user.id=b_article.uid
            LEFT JOIN b_like ON b_like.aid=b_article.id AND b_like.uid=? AND b_like.typeof=?
            WHERE b_article.id=?
            ORDER BY time DESC 
            LIMIT 1
SQL;

            $db->prepareAndExec($sql, [User::getCurUser()['id'], $flag, $num]);
            if($flag==0){
                echo packData('success',0, ['new' => $db->getResultSet()[0]],'发表成功');
            }elseif ($flag==2){
                echo packData('success',1,['new' => $db->getResultSet()[0]],'存入草稿成功');
            }
        }else{
            echo packData('fail',2,[],'插入数据失败，发表失败');
        }
    }

    public function delete()
    {
        $input = Input::getInstance();
        $db = DB::getInstance(getConfig('db'));
        $table = '';
        switch ($input->post('id')[0]) {
            case 'a':
                $table = 'b_article';
                break;
            case 'm':
                $table = 'b_moment';
                break;
            default:
                break;
        }
        $aid = substr($input->post('id'), $table === 'b_article' ? strlen('article') : strlen('moment'));
        $sql = "DELETE FROM {$table} WHERE ($table.id=? AND $table.uid=?) OR ($table.id=? AND ?=1)";
        $db->prepareAndExec($sql, [$aid, User::getCurUser()['id'], User::getCurUser()['type']]);
        if ($db->getEffectRow()) {
            echo packData('success', 0, [], '操作成功');
        } else {
            echo packData('fail', 1, [], '操作失败');

        }
    }
    /*
     * 转发文章
     * 没有草稿 转发直接状态为0->发表
     * @param int $aid 文章id
     * @return bool
     */
    public function repost($aid):bool {
        $db=DB::getInstance(getConfig('db'));
        $input=Input::getInstance();
        $uid = User::getCurUser()['id'];
        $db->prepare('SELECT * FROM b_article WHERE b_article.id=:id')->execute([':id'=>$aid]);
        $resarr=$db->getResultSet()[0] ?? null;
        $db->prepareAndExec('SELECT id FROM b_article WHERE uid=? AND title=? AND fromid=?', [User::getCurUser()['id'], $resarr['title'], $resarr['uid']]);
        if (count($db->getResultSet())) { // 已经转载过了
            return false;
        }

        $result=false;
        if(null!=$resarr){
            $repostnum = intval($resarr['repost']) + 1;
            if($this->updateNumData($aid,'repost',$repostnum)) {
                $title = $resarr['title']; //标题
                $content = $resarr['content'];//内容
                $fromid = $resarr['uid'];//转载自哪位用户id
                $flag = 0;//状态为发表
                $db->prepare('INSERT INTO b_article SET title=:title,content=:content,uid=:uid,flag=:flag,fromid=:fromid')->execute([':title' => $title, ':content' => $content, ':uid' => $uid, ':flag' => $flag, ':fromid' => $fromid]);
                $num = $db->lastInsertId();
                if ($num > 0) {
                    $result=true;
                }
            }
        }
        return $result;
    }

    public function reprint()
    {
        $aid = (Input::getInstance())->post('id');
        if ($this->repost($aid)) {
            echo packData('success', 0, [], '转载成功');
        } else {
            echo packData('fail', 1, [], '转载失败');
        }
    }

    /*
     * 根据其他文章id更新文章的内容的数量
     * @param int $aid 要更新的文章id
     * @param string $content 字段名称
     * @param int $num 更新数量
     * @return bool
     */
    private function updateNumData($aid,$content,$num):bool {
        $db=DB::getInstance(getConfig('db'));
        $db->prepare("UPDATE b_article SET $content=:content WHERE b_article.id=:id")->execute([':content'=>$num,':id'=>$aid]);
        $updatenum=$db->getEffectRow();
        return !!$updatenum;
    }


    /*
     *根据文章id更新阅读量
     * @param int $aid 文章id
     * @return bool
     */
    public function UpdateReaders($aid):bool {
        $db=DB::getInstance(getConfig('db'));
        $db->prepare('SELECT * FROM b_article WHERE b_article.id=:id')->execute([':id'=>$aid]);
        $resarr=$db->getResultSet()[0] ?? null;
        $result=false;
        if(null!=$resarr){
            $redersnum = intval($resarr['readers']) + 1;
            if($this->updateNumData($aid,'readers',$redersnum)){
                $result=true;
            }
        }
        return $result;
    }

    function moreReader()
    {
        $input = Input::getInstance();
        $id = $input->post('id');
        if ($this->UpdateReaders($id)) {
            echo packData('success', 0, [], '成功');
        } else {
            echo packData('fail', 1, [], '失败');
        }
    }


    /**
     * 将草稿发布
     * @param int $aid 文章id
     * @return bool
     */
    public function draftToPublish($aid):bool {
        $db=DB::getInstance(getConfig('db'));
        $db->prepare('SELECT * FROM b_article WHERE b_article.id=:id')->execute([':id'=>$aid]);
        $resarr=$db->getResultSet()[0] ?? null;
        $result=false;
        if(null!=$resarr){
            $flag=$resarr['flag'];
            if($flag==1){
                $flag=0;
                if($this->updateNumData($aid,'flag',$flag)){
                    $result=true;
                }
            }
        }
        return $result;
    }

    /*
    * 根据userid获得作者为user的文章的所有信息
    * @param int $userid 用户id
    */
    public function getArticleInfo($userid):array {
        $db=DB::getInstance(getConfig('db'));
        $db->prepare('SELECT * FROM b_article WHERE b_article.uid=:uid')->execute([':uid'=>$userid]);
        $this->articleInfo=$db->getResultSet() ?? null;
        return $this->articleInfo;
    }


    /**
     * 根据文章id删除文章
     * @param int $id 文章id
     * @return bool
     */
    public  function deleteArticle($id):bool {
       $db=DB::getInstance(getConfig('db'));
       $db->prepare('DELETE FROM b_article WHERE b_article.id=:id')->execute([':id'=>$id]);
       $num=$db->getEffectRow();
       return !!$num;
   }

    public function getByMAId() {
        $input = Input::getInstance();
        $type = 1;
        switch ($input->post('id')[0]) {
            case 'a':
                $type = 1;
                $aid = substr($input->post('id'), strlen('article'));
                $data = $this->getById($aid);
                break;
            case 'm':
                $type = 2;
                $aid = substr($input->post('id'), strlen('moment'));
                $data = (new Moment())->getById($aid);
                break;
            default:
                $data = [];
                break;
        }
        echo packData('success', 0, ['type'=>$type, 'ma'=>$data], '获得数据成功');
    }

    private function getById($id)
    {
        $db = DB::getInstance(getConfig('db'));
        $sql =
            <<< SQL
            SELECT 
              b_article.*, b_user.nickname, b_user.logo, b_user.username as user, b_like.id as `like`
            FROM b_article 
              LEFT JOIN b_user ON b_user.id=b_article.uid
              LEFT JOIN b_like ON b_like.aid=b_article.id AND b_like.uid=? AND b_like.typeof=1
            WHERE b_article.id = ?
            LIMIT 1
SQL;
        $uid = User::getCurUser()['id'];
        $db->prepareAndExec($sql, [$uid, $id]);
        return $db->getResultSet();
    }
}