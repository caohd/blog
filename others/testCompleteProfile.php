<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/10 0010
 * Time: 11:16
 */
require_once('core/Input.php');
require_once('core/DB.php');
require_once ('private/config.php');
require_once ('private/functions.php');

if(isLogin()) {
    if(updateUserBasicInfo()){
        echo json_encode(
            [
                'status'  => 'success',
                'code'    => 200,
                'data'   => [],
                'msg'     =>'更新个人基本信息成功'

            ]);
    }else{
        echo json_encode(
            [
                'status'  => 'fail',
                'code'    => 200,
                'data'   => [],
                'msg'     =>'未有资料需要更新'

            ]);
    }
}else{
    echo json_encode(
        [
            'status'  => 'fail',
            'code'    => 200,
            'data'   => [],
            'msg'     =>'未登录'

        ]);
}
//if(isLogin()){
//
//$userid=$_SESSION['userid'];
//$input=Input::getInstance();
//$logo=$input->post('logo');//头像
//$sex=$input->post('sex');//性别
//$brief=$input->post('brief');//个性签名
//$mail=$input->post('mail');//邮箱
//$city=$input->post('city');//所在城市id
//$date=$input->post('birthyear').'-'.$input->post('birthmonth').'-'.$input->post('birthday');//生日
//
//$db=DB::getInstance(getConfig('db'));
//$sql='UPDATE b_user SET password=:password,nickname=:nickname,mail=:mail,logo=:logo,sex=:sex,brief=:brief,birthday=:birthday,locationid=:locationid WHERE b_user.id=:id';
//$exe=[':password'=>'',':nickname'=>'',':mail'=>$mail,':logo'=>$logo,':sex'=>$sex,':brief'=>$brief,':birthday'=>$date,':locationid'=>$city,':id'=>$userid];
//$db->prepareAndExec($sql,$exe);
//}else{
//    echo '未登录';
//}




