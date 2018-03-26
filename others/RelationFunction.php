<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/11 0011
 * Time: 19:54
 */

//require_once('core/DB.php');
//require_once('core/Input.php');
//require_once ('private/functions.php');
//require_once ('private/config.php');


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
 */
function follow($uid1,$uid2,string $status='1'){
    $db=DB::getInstance(getConfig('db'));
    if(!hasRelation($uid1,$uid2)){
        $db->prepare('INSERT INTO b_relationship SET uid1=:uid1,uid2=:uid2,f1to2=:f1to2')->execute([':uid1'=>$uid1,':uid2'=>$uid2,':f1to2'=>$status]);
    }else{
        $db->prepare('UPDATE b_relationship SET f1to2=:f1to2 WHERE b_relationship.uid1=:uid1 AND uid2=:uid2')->execute([':f1to2'=>$status,':uid1'=>$uid1,':uid2'=>$uid2]);
    }


    if(!hasRelation($uid2,$uid1)){
        $db->prepare('INSERT INTO b_relationship SET uid1=:uid1,uid2=:uid2,f2to1=:f2to1')->execute([':uid1'=>$uid2,':uid2'=>$uid1,':f2to1'=>$status]);
    }else{
        $db->prepare('UPDATE b_relationship SET f2to1=:f2to1 WHERE b_relationship.uid1=:uid1 AND uid2=:uid2')->execute([':f2to1'=>$status,':uid1'=>$uid2,':uid2'=>$uid1]);
    }
    $temp=1;
    if(intval($status)==0) $temp=-1;
    $uid1follow=intval(getInfoById($uid1,'b_user','following'))+$temp;
    $uid2fans=intval(getInfoById($uid2,'b_user','follower'))+$temp;

    $db->prepare('UPDATE b_user SET following=:following WHERE b_user.id=:uid')->execute([':following'=>$uid1follow,':uid'=>$uid1]);
    $db->prepare('UPDATE b_user SET follower=:follower WHERE b_user.id=:uid')->execute([':follower'=>$uid2fans,':uid'=>$uid2]);
}


/**
 * 是否有关系表
 * @param $uid1
 * @param $uid2
 * @return bool
 */
function hasRelation($uid1,$uid2):bool{
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
 */
function removeFollower($uid1,$uid2){
    follow($uid2,$uid1,0);
}

/*
 * 是否关注中
 * user1是否正在关注user2
 */
function isFollowing($uid1,$uid2):bool {
    $db=DB::getInstance(getConfig('db'));
    $sql="SELECT id FROM b_relationship WHERE uid1 =:uid1 AND uid2=:uid2 AND f1to2=:f1to2";
    $exe=[':uid1'=>$uid1,':uid2'=>$uid2,':f1to2'=>1];
    $db->prepare($sql)->execute($exe);
    $num=count($db->getResultSet());
    return !!$num;
}