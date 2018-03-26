
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/4 0004
 * Time: 17:26
 */
require_once('core/DB.php');
require_once ('core/Input.php');
require_once ('private/config.php');
require_once ('private/functions.php');


$input=Input::getInstance();
if($input->post('submit'))
{
    $user = $input->post('username');
    $psw = $input->post('password');
    if($user == "" || $psw == "")
    {
        echo json_encode(
            [
                'status'  => 'fail',
                'code'    => 200,
                'data'   => [],
                'msg'     =>'用户名或者密码为空'

            ]);
    }
    else
    {
        if(isUserMatchPsw($user,$psw)){
             $input->session('username',$user);
             $input->session('password',$psw);
             $usid=getUserId($user);
            if($usid>0){
            $input->session('userid',$usid);
            }
            echo json_encode(
                [
                    'status'  => 'success',
                    'code'    => 200,
                    'data'   => [],
                    'msg'     =>'用户名和密码匹配，登录成功'

                 ]
            );
        }else
        {
            if(isUsernameExist($user)){
                echo json_encode(
                    [
                        'status'  => 'fail',
                        'code'    => 200,
                        'data'   => [],
                        'msg'     =>'密码不正确'

                    ]);
            }else{
                echo json_encode(
                    [
                        'status'  => 'fail',
                        'code'    => 200,
                        'data'   => [],
                        'msg'     =>'用户名尚未被注册'

                    ]);
            }

        }
    }
}
else
{
    echo json_encode(
        [
            'status'  => 'fail',
            'code'    => 200,
            'data'   => [],
            'msg'     =>'登录提交不成功'

        ]);
}


