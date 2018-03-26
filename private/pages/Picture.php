<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/16 0016
 * Time: 19:24
 */

class Picture
{
    private $pictureInfo=[];


    /**
     * 根据form中文件定义name和相册id以及获得的照片名字新建照片
     * 相册的照片数目+1 pictures b_album
     * @param string $nameofpicture  post中文件的name的名称
     * @param int $aid 相册id
     * @return  bool
     */
    public function createPicture(string $src, string $picturename, $aid):bool {
//        ////获得路径
//        $input=Input::getInstance();
//        $picturename=$input->post('picturename');
//
//        $upload=new Upload($nameofpicture);
//        if($upload->isImage()) {
//            $scrarr = $upload->upl();//头像
//        }
//        if(is_array($scrarr)){
//            $temp=explode('/',$scrarr[0]);
//            for($i=0;$i<count($temp);$i++){
//                $picturesrc=$temp[$i];
//            }
//        }
        $db=DB::getInstance(getConfig('db'));
        $result=false;
        $db->prepare('INSERT INTO b_picture SET aid=:aid,name=:name,src=:src')->execute([':aid'=>$aid,':name'=>$picturename,':src'=>$src]);
        if(!!$db->lastInsertId()) {
            if($this->albumUpdate($aid)){
                $result=true;
            }
        }
        return $result;
    }

    public function getPicInfoBySrc()
    {
        $input = Input::getInstance();
        $db = DB::getInstance(getConfig('db'));
        $src = $input->post('src');
        $sql =
            <<< SQL
            SELECT 
              b_picture.*, b_like.id AS isLike 
            FROM b_picture 
              LEFT JOIN b_like ON b_like.uid=? AND b_like.typeof=2 AND b_like.aid=b_picture.id
            WHERE src REGEXP ?
SQL;
        $db->prepareAndExec($sql, [User::getCurUser()['id'], '\/{0,1}'.$src]);
        if (count($db->getResultSet())) {
            echo packData('success', 0, ['picInfo' => $db->getResultSet()[0]], '获取数据成功');
        } else {
            echo packData('fail', 1, [], '获取数据失败');
        }
    }
    /**
     * @API pictures.html
     */
    public function addPicToAlbum()
    {
        $input = Input::getInstance();
        $aid = $input->get('id');
        $name = $input->post('name');
        $src = $input->post('src');
        if ($this->createPicture($src, $name, $aid)) {
            echo packData('success', 0, [], '添加成功');
        } else {
            echo packData('fail', 1, [], '添加失败');
        }
    }

    /**
     * 相片的增加或减少 更新相册
     * @param int $aid 相册id
     * @param int $status 默认为+1即增加照片
     * @return bool
     */
    private function albumUpdate($aid,$status=1):bool{
        $db=DB::getInstance(getConfig('db'));
        $db->prepare("SELECT * FROM b_album WHERE b_album.id=:id")->execute([':id'=>$aid]);
        $result=$db->getResultSet()[0] ?? null;
        $resultbb=false;
        if(null!=$result) {
            $picturenum = intval($result['pictures']) +$status;
            if($this->updateNumData('b_album',$aid,'pictures',$picturenum)){
                $resultbb=true;
            }
        }
        return $resultbb;
    }


    /**
     * 根据照片id删除照片
     * 并在相册的照片数目-1  pictures b_album
     * @param $id
     * @return bool
     */
    public function deletePicture($id):bool {

        $db=DB::getInstance(getConfig('db'));

        $db->prepare('SELECT aid, uid FROM b_picture RIGHT JOIN b_album ON b_album.id=b_picture.aid WHERE b_picture.id=:id')->execute([':id'=>$id]);
        $result=$db->getResultSet()[0] ?? null;
        $resultbb=false;
        if(null!=$result){
            if (isSystemManager() || $result['uid'] === User::getCurUser()['id']) {
                $aid = $result['aid'];
                $db->prepare('DELETE FROM b_picture WHERE b_picture.id=:id')->execute([':id' => $id]);
                $num = $db->getEffectRow();
                if ($num > 0) {
                    if ($this->albumUpdate($aid, -1)) {
                        $resultbb = true;
                    }
                }
            } else {
                $resultbb = false;
            }
        }
        return $resultbb;

    }

    public function delete()
    {
        $input = Input::getInstance();
        $id = $input->post('id');

        if ($this->deletePicture($id)) {
            echo packData('success', 0, [], '删除图片成功');
        } else {
            echo packData('fail', 1, [], '删除图片失败');
        }
    }

    /**
     * 更新某表数目
     * @param string $table  表
     * @param string $aid 类型id
     * @param string $content  更新的字段
     * @param string $num 更新的数据
     * @return bool
     */
    private function updateNumData($table,$aid,$content,$num):bool {
        $db=DB::getInstance(getConfig('db'));
        $db->prepare("UPDATE $table SET $content=:content WHERE $table.id=:id")->execute([':content'=>$num,':id'=>$aid]);
        $updatenum=$db->getEffectRow();
        return !!$updatenum;
    }

    /**
     * 根据相册id获得相册内部所有的照片信息
     * @param $aid
     * @return array
     */
    public function getPicturesInfo($aid):array {
        $db=DB::getInstance(getConfig('db'));
        $db->prepare('SELECT * FROM b_picture WHERE b_picture.aid=:aid ORDER BY time DESC ')->execute([':aid'=>$aid]);
        $this->pictureInfo=$db->getResultSet()??[];
        return $this->pictureInfo;
    }
    
    /**
     * @API pictures.html
     */
    public function allPictures()
    {
        $input = Input::getInstance();
        $id = $input->get('id');
        if ($input->get('user')) {
            $user = User::getUserByName($input->get('user'));
        } else {
            $user = User::getCurUser();
        }
        unset($user['password']);
        $data['nick'] = User::getCurUser()['nickname'];
        $data['logo'] = User::getCurUser()['logo'];
        $data['pics'] = $this->getPicturesInfo($id);
        $data['user'] = $user;
        $db = DB::getInstance();
        $db->prepareAndExec('SELECT * FROM b_album WHERE id=?', [$id]);
        $data['album'] = $db->getResultSet()[0];
        echo packData('success', 0, $data, '获得数据成功');
    }

    /**
     * @API pictures.html
     */
    public function likePic()
    {
        $input = Input::getInstance();
        $db = DB::getInstance(getConfig('db'));
        $src = $input->post('src');
        $sql = 'SELECT * FROM b_picture WHERE src REGEXP ?';
        $db->prepareAndExec($sql, ['\/{0,1}'.$src]);
        if ($res = $db->getResultSet()) {
            $db->prepareAndExec('INSERT INTO b_like (uid, typeof, aid) VALUES (?, 2, ?)', [User::getCurUser()['id'], $res[0]['id']]);
            if ($db->getEffectRow())
                $db->prepareAndExec('UPDATE b_picture SET belike=belike+1 WHERE id=?', [$res[0]['id']]);

            if ($db->getEffectRow()) {
                echo packData('success', 0, [], 'success');
            } else {
                echo packData('fail', 1, [], '点赞失败');
            }
        } else {
            echo packData('fail', 2, [], '照片不存在');
        }
    }
    public function rmLike()
    {
        $input = Input::getInstance();
        $db = DB::getInstance(getConfig('db'));
        $src = $input->post('src');
        $sql = 'SELECT * FROM b_picture WHERE src REGEXP ?';
        $db->prepareAndExec($sql, ['\/{0,1}'.$src]);
        if ($res = $db->getResultSet()) {
            $db->prepareAndExec('DELETE FROM b_like WHERE uid=? AND typeof=2 AND aid=?', [User::getCurUser()['id'], $res[0]['id']]);
            if ($db->getEffectRow())
                $db->prepareAndExec('UPDATE b_picture SET belike=belike-1 WHERE id=?', [$res[0]['id']]);

            if ($db->getEffectRow()) {
                echo packData('success', 0, [], 'success');
            } else {
                echo packData('fail', 1, [], '点赞失败');
            }
        } else {
            echo packData('fail', 2, [], '照片不存在');
        }
    }
}