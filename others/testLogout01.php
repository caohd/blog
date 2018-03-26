<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/10 0010
 * Time: 21:07
 */

require_once("core/Input.php");
require_once("private/functions.php");

$input=Input::getInstance();
if(isLogin()){
  $input->session();
  $input->cookie();
  header("location:testhtml/login.html");
}
//session_start();
//if(isset($_SESSION['username'])){
//    session_unset();
//    session_destroy();
//    setcookie(session_name(),'',time()-3600);
//    echo "注销成功";
//    header("location:testLogin00.html");
//}

