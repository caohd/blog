<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/16 0016
 * Time: 15:17
 */

class Album
{

    private $albumInfo=[];

    /**
     * 创建新相册
     */
    public function creatNewAlbum(){
        $input=Input::getInstance();
        $uid=User::getCurUser()['id'];
        $albumname=$input->post('albumname');

        $db=DB::getInstance(getConfig('db'));
        if(empty($albumname)){
            $albumname='新相册';
        }
        $db->prepare('INSERT INTO b_album SET uid=:uid,name=:name')->execute([':uid'=>$uid,':name'=>$albumname]);
        if(!!$db->lastInsertId()){
            echo packData('success',0,['nid' => $db->lastInsertId(), 'albumname' => $albumname],'成功创建相册');
        }else{
            echo packData('fail',1,[],'创建相册失败');
        }
    }


    /**
     * 根据相册id增加浏览量 每次+1
     * @param int $id
     * @return bool
     */
    public function addPageview($id):bool
    {
        $db=DB::getInstance(getConfig('db'));
        $db->prepare('SELECT * FROM b_album WHERE b_album.id=:id')->execute([':id'=>$id]);
        $resarr=$db->getResultSet()[0] ?? null;
        $result=false;
        if(null!=$resarr){
            $pageviewnum = intval($resarr['pageview']) + 1;
            if($this->updateNumData($id,'pageview',$pageviewnum)){
                $result=true;
            }
        }
        return $result;
    }

    /**
     * 根据id更新某字段数据
     * @param $aid
     * @param $content
     * @param $num
     * @return bool
     */
    private function updateNumData($aid,$content,$num):bool {
        $db=DB::getInstance(getConfig('db'));
        $db->prepare("UPDATE b_album SET $content=:content WHERE b_album.id=:id")->execute([':content'=>$num,':id'=>$aid]);
        $updatenum=$db->getEffectRow();
        return !!$updatenum;
    }

    /**
     * 根据相册id删除相册
     * @param int $id
     * @return bool
     */
    public function deleteAlbum($id):bool {
        $db=DB::getInstance(getConfig('db'));
        $db->prepare('DELETE FROM b_album WHERE (b_album.id =? AND uid=?) OR (b_album.id =? AND ?=1)')
            ->execute([$id, User::getCurUser()['id'], User::getCurUser()['type']]);
        if (1 === ($row = $db->getEffectRow())) {
            $db->prepareAndExec('SELECT src FROM b_picture WHERE aid=? ', [$id]);
            $pics = $db->getResultSet();
            foreach ($pics as $pic) {
                @unlink('public/images/' . $pic['pic']);
            }
            $db->prepareAndExec('DELETE FROM b_picture WHERE id=?', [$id]);
        }
        return !!$row;
    }

    /**
     * @API
     */
    public function delete()
    {
        $input = Input::getInstance();
        $id = $input->post('id');
        if ($this->deleteAlbum($id)) {
            echo packData('success', 0, [], '删除相册成功');
        } else {
            echo packData('fail', 1, [], '删除相册失败');
        }
    }
    /**
     * 根据用户id获得所有相册信息
     * @param int $userid
     * @return array
     */
    public function getAlbumInfo($userid):array {
        $db=DB::getInstance(getConfig('db'));
        $sql =
            <<<SQL
            SELECT
              *, cover
            FROM b_album
              LEFT JOIN (SELECT src AS cover, aid
                         FROM b_picture AS a
                           RIGHT JOIN  (SELECT max(id) AS mid
                                        FROM b_picture
                                        GROUP BY aid) AS b
                             ON a.id=b.mid) AS mp
                ON mp.aid=b_album.id
            WHERE uid=?
            ORDER BY b_album.time DESC 
SQL;
        $db->prepare($sql)->execute([$userid]);
        $this->albumInfo=$db->getResultSet()??null;
        return $this->albumInfo;
    }

    public function init()
    {
        $input = Input::getInstance();
        if ($input->get('user')) {
            $user = User::getUserByName($input->get('user'));
        } else {
            $user = User::getCurUser();
        }
        unset($user['password']);
        $data['nick'] = User::getCurUser()['nickname'];
        $data['logo'] = User::getCurUser()['logo'];
        $data['albums'] = $this->getAlbumInfo($user['id']);
        $data['user'] = $user;
        echo packData('success', 0, $data, '请求成功');
    }
}