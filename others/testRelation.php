<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/12 0012
 * Time: 14:59
 */

require_once('core/DB.php');
require_once('core/Input.php');
require_once ('private/functions.php');
require_once ('private/config.php');
require_once ('RelationFunction.php');

$input=Input::getInstance();
if(isLogin()){
    $uid1 = $input->session('userid');
    $uid2 = '2';
    if($uid1!=$uid2) {
        if ($input->post('follow') == '关注') {
            if (!isFollowing($uid1, $uid2)) {
                follow($uid1, $uid2);
                echo packData('success',200,'','关注成功');
            } else {
                echo packData('fail',200,'','已关注过');
            }
        }
        if ($input->post('defollow') == '取消关注') {
            if (isFollowing($uid1, $uid2)) {
                follow($uid1, $uid2, 0);
                echo packData('success',201,'','取消关注成功');
            } else {
               echo  packData('fail',201,'','尚未关注此人');
            }
        }
        if ($input->post('remove') == '移除粉丝') {
            if (isFollowing($uid2, $uid1)) {
                removeFollower($uid1, $uid2);
                echo packData('success',202,'','移除粉丝成功');
            } else {
                echo packData('fail',202,'','此人尚未是您粉丝');
            }
        }
    }else{
        echo packData('fail',203,'','不可关注自己');
    }

}