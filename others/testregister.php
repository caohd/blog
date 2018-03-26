<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/4 0004
 * Time: 17:30
 */

require_once('core/DB.php');
require_once ('core/Input.php');
require_once ('private/config.php');
require_once ('private/functions.php');

$input=Input::getInstance();
if($input->post('submit')){
    $usr=$input->post('username');
    $pwd=$input->post('password');
    $nkn=$input->post('nickname');
    if($usr==''||$pwd==''||$nkn==''){
        echo json_encode(
            [
                'status'  => 'fail',
                'code'    => 200,
                'data'   => [],
                'msg'     =>'用户名或密码或昵称为空'

            ]);
    }else{
        if(!isUsernameExist($usr)){
           $db=DB::getInstance(getConfig('db'));
           $db->prepareAndExec('INSERT INTO b_user SET username=:username,password=:password,nickname=:nickname',[':username'=>$usr,':password'=>$pwd,':nickname'=>$nkn]);
           if(!!$db->lastInsertId()){
               $input->session('username',$usr);
               $input->session('password',$pwd);
               $usid=getUserId($usr);
               if($usid>0){
                   $input->session('userid',$usid);
               }
               echo json_encode(
                   [
                       'status'  => 'success',
                       'code'    => 200,
                       'data'   => [],
                       'msg'     =>'用户注册成功'

                   ]);
               header("location:testCompleteProfile.html");

           }else{

               echo json_encode(
                   [
                       'status'  => 'fail',
                       'code'    => 200,
                       'data'   => [],
                       'msg'     =>'用户数据插入失败'

                   ]);
           }

        }else{
            echo json_encode(
                [
                    'status'  => 'fail',
                    'code'    => 200,
                    'data'   => [],
                    'msg'     =>'用户名存在'

                ]);
        }

    }
}else{
    echo json_encode(
        [
            'status'  => 'fail',
            'code'    => 200,
            'data'   => [],
            'msg'     =>'submit不成功'

        ]);
}

