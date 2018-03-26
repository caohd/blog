<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/13 0013
 * Time: 16:09
 */
require_once('core/Input.php');
require_once('core/DB.php');
require_once ('core/Upload.php');
require_once ('private/config.php');
require_once ('private/functions.php');

$aid=2;//test文章id  getArticleInfo()['id']  getCommentInfo()['id']
$type=1;//test 评论文章

$input=Input::getInstance();
$db=DB::getInstance(getConfig('db'));
$content=$input->post('content');
if(!empty($content)) {
    $userid = $input->session('userid');
    $db->prepare('INSERT INTO b_comment SET aid=:aid,typeof=:typeof,uid=:uid,content=:content')->execute([':aid' => $aid, ':typeof' => $type, ':uid' => $userid, ':content' => $content]);
    $num=$db->lastInsertId();
    echo $num;
    if($num!=0){
        echo packData('success',201,'','评论成功');
    }else{
        echo packData('fail',200,'','插入失败');
    }
}else{
    echo packData('fail',201,'','评论为空');
}



