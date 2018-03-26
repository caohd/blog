<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/17 0017
 * Time: 13:59
 */

class Moment
{
    private $pictureInfo=[];
    private $momentInfo=[];
    private $id;


    /**创建朋友圈的文字内容 无即''
     */
    public function createContent(){
        $db=DB::getInstance(getConfig('db'));
        $input=Input::getInstance();
        $uid=$input->session('userid');
        $content=$input->post('content');

        $db->prepare('INSERT INTO b_moment SET uid=:uid,content=:content')->execute([':uid'=>$uid,':content'=>$content]);
        $num=$db->lastInsertId();
        if(!!$num){
            $this->id=$num;
            echo packData('success',200,[],'新建朋友圈内容成功');
        }else{
            echo packData('fail',200,[],'新建朋友圈内容失败');
        }

    }
    public function getById($id)
    {
        $db = DB::getInstance(getConfig('db'));
        $sql =
            <<< SQL
            SELECT 
              b_moment.*, b_user.nickname,b_user.logo, b_user.username AS user, b_mpicture.src, b_like.id as `like`
            FROM b_moment 
              LEFT JOIN b_user ON b_user.id=b_moment.uid 
              LEFT JOIN b_mpicture ON b_mpicture.aid=b_moment.id
              LEFT JOIN b_like ON b_like.uid=? AND b_like.aid=b_moment.id AND b_like.typeof=3
            WHERE b_moment.id=?
            ORDER BY time DESC 
            LIMIT 1
SQL;
        $uid = User::getCurUser()['id'];
        $db->prepareAndExec($sql, [$uid, $id]);
        return $db->getResultSet();
    }
    /**
     * @API index.html
     */
    public function publish()
    {
        $input = Input::getInstance();
        $db = DB::getInstance(getConfig('db'));
        $sql = 'INSERT INTO b_moment (uid, content) VALUES (?, ?)';
        $db->prepareAndExec($sql, [User::getCurUser()['id'], $input->post('content')]);
        $mid = $db->lastInsertId();
        if ($mid) {
            $srcs = $input->post('srcs');
            $sql = 'INSERT INTO b_mpicture VALUES ';
            foreach ($srcs as $src) {
                $fullSrc = 'public/images/'.$src;
                if (file_exists($fullSrc)) {
                    $sql .= "(NULL, {$mid}, '{$src}'),";
                }
            }
            $sql = rtrim($sql, ',');
            $db->prepareAndExec($sql, []);
        }
        if ($mid > 0) {
            $sql =
                <<< SQL
            SELECT 
            b_moment.*, b_user.nickname,b_user.logo, b_mpicture.src
            FROM b_moment 
            LEFT JOIN b_user ON b_user.id=b_moment.uid 
            LEFT JOIN b_mpicture ON b_mpicture.aid=b_moment.id
            WHERE b_moment.id=?
SQL;

            $db->prepareAndExec($sql, [$mid]);
            echo packData('success', 0, ['moment' => $db->getResultSet()], '发表成功');
        } else {
            echo packData('fail', 1, [], '系统错误');
        }
    }
    /**
     * 为朋友圈添加图片
     * 必须先创建朋友圈内容 有无也可
     * @param string $nameofpicture file传递过来的name
     * @return bool
     */
    public function createPicture($nameofpicture):bool{
        $result=false;
        if(!!$this->id){
            $aid=$this->id;
            $upload=new Upload($nameofpicture);
            if($upload->isImage()) {
                $picturearr = $upload->upl();
            }
            if(is_array($picturearr)){
                $temp=explode('/',$picturearr[0]);
                for($i=0;$i<count($temp);$i++){
                    $picturesrc=$temp[$i];
                }
            }

            $db=DB::getInstance(getConfig('db'));
            $db->prepare('INSERT INTO b_mpicture SET aid=:aid,src=:src')->execute([':aid'=>$aid,':src'=>$picturesrc]);
            if(!!$db->lastInsertId()){
               $result=true;
            }
        }
        return $result;
    }



    /**
     * @param int $aid 默认为当前朋友圈id
     * @return array
     */
    public function getPictureInfo($aid=0):array {
        if(0==$aid) $aid=$this->id;
        if($aid>0){
            $db=DB::getInstance(getConfig('db'));
            $db->prepare('SELECT * FROM b_mpicture WHERE b_mpicture.aid=:aid')->execute([':aid'=>$aid]);
            $this->pictureInfo=$db->getResultSet()??null;
        }
        return $this->pictureInfo;
    }


    /**
     * 根据用户id获得跟用户id所有朋友圈
     * @param $userid
     * @return array
     */
    public function getMomentInfo($userid):array {
        $db=DB::getInstance(getConfig('db'));
        $db->prepare('SELECT * FROM b_moment WHERE b_moment.uid=:uid')->execute([':uid'=>$userid]);
        $this->momentInfo=$db->getResultSet() ?? null;
        return $this->momentInfo;
    }

}